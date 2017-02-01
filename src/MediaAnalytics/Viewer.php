<?php

namespace Akamai\Analytics;

use Akamai\Analytics\AbstractService\MediaAnalytics;

class Viewer extends MediaAnalytics
{
    const API_REPORT_TYPE_VIEWER = 'viewer-diagnostics';

    public function getReportType()
    {
        return self::API_REPORT_TYPE_VIEWER;
    }
}
