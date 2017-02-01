<?php

namespace Akamai\Analytics\MediaReports;

use Akamai\Analytics\AbstractService\MediaReports;

class AdaptiveMediaDelivery extends MediaReports
{
    const REPORT_TYPE = 'adaptive-media-delivery';

    public function getReportType()
    {
        return self::REPORT_TYPE;
    }
}


