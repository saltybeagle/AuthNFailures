<?php
namespace AuthNFailures;

use AuthNFailures\ActiveRecord\Database;

/**
 * A subject to track authentication failures for
 *
 * @author Brett Bieber
 *
 */
class Subject
{

    protected $options = array(
        'id' => false, // Username, uid, eppn etc
    );

    public function __construct($options = array())
    {
        $this->options = $options + $this->options;
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
        return $this->options['id'];
    }

    /**
     * Resets the counter for the user
     *
     * @param int $timestamp Time of the reset event
     */
    public function resetCounter($timestamp = null)
    {
        if (!$timestamp) {
            $timestamp = time();
        }
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
     * Update total count for the subject
     */
    public function updateCount()
    {
        // @TODO Update count should calculate what the total count should be

    }

}