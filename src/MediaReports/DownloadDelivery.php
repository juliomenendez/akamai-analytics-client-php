<?php

namespace Akamai\Analytics\MediaReports;

use Akamai\Analytics\AbstractService\MediaReports;

class DownloadDelivery extends MediaReports
{
    const REPORT_TYPE = 'download-delivery';

    public function getReportType()
    {
        return self::REPORT_TYPE;
    }
}

