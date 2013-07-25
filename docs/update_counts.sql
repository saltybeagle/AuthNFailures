UPDATE counts
    LEFT JOIN (
        SELECT count(*) AS current_count, events.subject
        FROM `events`
        LEFT JOIN (
            SELECT subject, MAX(reset_timestamp) AS last_reset
            FROM resets
            GROUP BY subject
        ) AS last_resets
            ON events.subject = last_resets.subject
        WHERE
            (
                (events.timestamp > last_resets.last_reset)
                OR last_resets.last_reset IS NULL
            )
        GROUP BY events.subject
    ) AS current_total_counts
        ON counts.subject = current_total_counts.subject
SET counts.current_count = IFNULL(current_total_counts.current_count, 0)
