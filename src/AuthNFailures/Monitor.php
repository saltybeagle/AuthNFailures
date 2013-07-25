<?php

namespace AuthNFailures;

use AuthNFailures\Subjects\AboveThreshold;

class Monitor
{

    public function getSubjectsAboveThreshold($threshold)
    {
        $subjects = new AboveThreshold($threshold);
        return $subjects;
    }

    public function searchAboveThresholdCallback($threshold, $subjectCallback)
    {
        $subjects = $this->getSubjectsAboveThreshold($threshold);

        // Go through all the results and do something
        iterator_apply($subjects, $subjectCallback, array($subjects));
    }
}