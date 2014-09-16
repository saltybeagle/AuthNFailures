<?php
namespace AuthNFailures\Reports;

use AuthNFailures\ActiveRecord\DynamicRecordList;

class Over500FailuresByIPAndSubject extends DynamicRecordList
{
    public function getDefaultOptions()
    {
        return array(
            'listClass' => __CLASS__,
            'itemClass' => '\\AuthNFailures\\ActiveRecord\\DynamicRecord',
        );
    }

    public function getColumns()
    {
        return array(
                'ip_address',
                'service',
                'subject',
                'total',
        );
    }
    
    public function getFromClause()
    {
        return 'FROM `over-500-failures-last-24hrs-by-ip-and-subject`';
    }
}