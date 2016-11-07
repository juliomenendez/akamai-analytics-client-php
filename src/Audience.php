<?php

namespace Akamai\Analytics;

class Audience extends AbstractService
{
    public function getReportType()
    {
        return self::API_TYPE_AUDIENCE;
    }
}
