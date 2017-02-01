<?php

namespace Akamai\Analytics;

use Akamai\Analytics\AbstractService\MediaAnalytics;

class Qos extends MediaAnalytics
{
    const API_REPORT_TYPE_QOS = 'qos-monitor';

    public function getReportType()
    {
        return self::API_REPORT_TYPE_QOS;
    }
}
