<?php

namespace Akamai\Analytics;

class Download extends AbstractService
{
    public function getReportType()
    {
        return self::API_TYPE_DOWNLOAD;
    }
}
