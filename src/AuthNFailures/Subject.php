<?php
namespace AuthNFailures;

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
     * @param string|int $service    Name or ID of the service
     * @param string     $ip_address IP address for the remote authn attempt
     * @param int        $timestamp  Time of the event
     */
    public function increment($service = null, $ip_address = null, $timestamp = null)
    {
        $event = new Event();
        $event->subject    = $this->getId();
        $event->service    = $service;
        $event->ip_address = $ip_address;
        $event->timestamp  = $timestamp;
        $event->save();

        $count = Count::getBySubject($this->getId());
        $count->current_count = $current_count + 1;
        $count->save();

        return $this;

    }

}