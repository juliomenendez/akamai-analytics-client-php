<?php

namespace Akamai\Analytics;

class Viewer extends AbstractService
{
    public function getReportType()
    {
        return self::API_TYPE_VIEWER;
    }
}
