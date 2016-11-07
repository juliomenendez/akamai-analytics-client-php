<?php

namespace Akamai\Analytics;

class Qos extends AbstractService
{
    public function getReportType()
    {
        return self::API_TYPE_QOS;
    }
}
