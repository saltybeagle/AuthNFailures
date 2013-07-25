<?php
namespace AuthNFailures;

class CountManager extends Counts
{

    public $options = array('limit'=>30, 'offset'=>0);

    public function __construct($options)
    {
        $this->options = $options+$this->options;
        parent::__construct($this->options);
    }

    public function getOrderByClause()
    {
        return 'ORDER BY subject ASC';
    }

    /**
     * Get the SQL for determining the total count of events since last reset.
     *
     * If the $subject parameter is passed, two placeholders for a prepared
     * statement will be inserted.
     *
     * @param string $subject ID of the subject
     *
     * @return string
     */
    public static function getSQLForCountSinceLastReset($subject = null)
    {
        $sql = 'SELECT count(*) AS current_count, events.subject
                FROM `events`
                    LEFT JOIN ('.ResetManager::getSQLForLastReset($subject).') AS last_resets
                        ON events.subject = last_resets.subject
                WHERE
                    (
                        (events.timestamp > last_resets.last_reset)
                        OR last_resets.last_reset IS NULL
                    )';
        if ($subject) {
            $sql .= ' AND events.subject = ? ';
        }
        $sql .= 'GROUP BY events.subject';
        return $sql;
    }
}
