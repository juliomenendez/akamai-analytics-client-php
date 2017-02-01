<?php

namespace Akamai\Analytics\MediaReports;

use Akamai\Analytics\AbstractService\MediaReports;

class ObjectDelivery extends MediaReports
{
    const REPORT_TYPE = 'object-delivery';

    public function getReportType()
    {
        return self::REPORT_TYPE;
    }
}


