<?php

namespace Akamai\Analytics\MediaAnalytics;

use Akamai\Analytics\AbstractService\MediaAnalytics;

class Download extends MediaAnalytics
{
    const API_REPORT_TYPE_DOWNLOAD = 'download-analytics';

    public function getReportType()
    {
        return self::API_REPORT_TYPE_DOWNLOAD;
    }
}
