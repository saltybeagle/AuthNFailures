<?php
namespace AuthNFailures;

class ResetManager extends Resets
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
     * Returns SQL used for determining the latest reset
     *
     * If the $subject parameter is passed, a placeholder for a prepared
     * statement will be inserted.
     *
     * @param string $subject ID of the subject
     *
     * @return string
     */
    public static function getSQLForLastReset($subject = null)
    {
        $sql = 'SELECT subject, MAX(reset_timestamp) AS last_reset
                FROM resets
                ';
        if ($subject) {
            $sql .= 'WHERE subject = ? ';
        }
        $sql .='GROUP BY subject';

        return $sql;
    }
}
