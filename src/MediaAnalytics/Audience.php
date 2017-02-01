<?php

namespace Akamai\Analytics\MediaAnalytics;

use Akamai\Analytics\AbstractService\MediaAnalytics;

class Audience extends MediaAnalytics
{
    const API_REPORT_TYPE_AUDIENCE = 'audience-analytics';

    public function getReportType()
    {
        return self::API_REPORT_TYPE_AUDIENCE;
    }
}
