<?php
namespace AuthNFailures;

use AuthNFailures\ActiveRecord\Database;
use AuthNFailures\ActiveRecord\DynamicRecord;

/**
 * A subject to track authentication failures for
 *
 * @author Brett Bieber
 *
 */
class Subject extends DynamicRecord
{

    protected $options = array(
        'id' => false, // Username, uid, eppn etc
    );

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
    }

    public static function getKeys()
    {
        return array('id');
    }

    /**
     * Sets the ID for the subject
     *
     * @param string $id The ID for the subject, e.g. hhusker1 
     */
    public function setId($id)
    {
        $this->options['id'] = $id;

        // chain
        return $this;
    }

    /**
     * Get the id for the subject
     *
     * @return string
     */
    public function getId()
    {
        if (isset($this->id)) {
            return $this->id;
        }
        return $this->options['id'];
    }

    /**
     * Resets the counter for the subject to zero
     *
     * @return bool
     */
    public function resetCounter()
    {

        if ($count = Count::getBySubject($this->getId())) {
            // all ok
        } else {
            $count = new Count();
            $count->subject = $this->getId();
        }

        $count->current_count = 0;
        return $count->save();

    }

    /**
     * Add a reset event for this subject
     *
     * @param int        $timestamp    Time of the event
     * @param string|int $external_key An external unique key for this event
     * @param string     $raw_data     Raw event data, e.g. lines from the LDAP log file
     *
     * @return \AuthNFailures\Subject
     */
    public function addReset($timestamp = null, $external_key = null, $raw_data = null)
    {
        $reset                  = new Reset();
        $reset->subject         = $this->getId();
        $reset->reset_timestamp = $timestamp;
        $reset->external_key    = $external_key;
        $reset->raw_data        = $raw_data;

        $reset->save();

        return $this;
    }

    /**
     * Increment the authentication counter for this subject
     *
     * @param string|int $service      Name or ID of the service
     * @param string     $ip_address   IP address for the remote authn attempt
     * @param int        $timestamp    Time of the event
     * @param string|int $external_key An external unique key for this event
     * @param string     $raw_data     Raw event data, e.g. lines from the LDAP log file
     */
    public function addEvent($service = null, $ip_address = null, $timestamp = null, $external_key = null, $raw_data = null)
    {
        $event = new Event();
        $event->subject    = $this->getId();
        $event->service    = $service;
        $event->ip_address = $ip_address;
        $event->timestamp  = $timestamp;
        $event->external_key = $external_key;
        $event->raw_data     = $raw_data;
        $event->save();

        $this->incrementCount();

        return $this;

    }

    /**
     * Increment the counter for the subject by one
     */
    public function incrementCount()
    {
        $db = Database::getDB();

        $sql = 'INSERT INTO `counts` (subject, current_count) VALUES (?, 1)
                ON DUPLICATE KEY UPDATE current_count = current_count + 1;';

        $stmt = $db->prepare($sql);

        $id = $this->getId();
        $stmt->bind_param('s', $id);

        $stmt->execute();
    }

    /**
     * Update the count (since last reset) for the subject
     */
    public function updateCount()
    {
        $db = Database::getDB();
        $sql = 'UPDATE counts
                LEFT JOIN ('.CountManager::getSQLForCountSinceLastReset($this->getId()).') AS current_total_counts
                        ON counts.subject = current_total_counts.subject
                SET counts.current_count = IFNULL(current_total_counts.current_count, 0)
                WHERE counts.subject = ?';

        $stmt = $db->prepare($sql);

        $id = $this->getId();

        $stmt->bind_param('sss', $id, $id, $id);

        $stmt->execute();
    }

    /**
     * Get the current count for the subject
     *
     * @return int
     */
    public function getCurrentCount()
    {
        if (isset($this->current_count)) {
            return $this->current_count;
        }

        if ($count = Count::getBySubject($this->getId())) {
            return $count->current_count;
        }

        return 0;
    }

    /**
     * Get this user's recent activity
     *
     * @return \AuthNFailures\Subject\RecentActivity
     */
    public function getRecentActivity()
    {
        return new Subject\RecentActivity(array('subject_id'=>$this->getId()));
    }

    /**
     * Get this user's resets
     *
     * @return \AuthNFailures\Subject\Resets
     */
    public function getResets()
    {
    	return new Subject\Resets(array('subject_id'=>$this->getId()));
    }
    
    public function getURL()
    {
        if (!isset($this->id)) {
            return false;
        }

        return Controller::$url . 'subjects/' . $this->id;
    }

}