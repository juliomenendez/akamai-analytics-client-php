<?php

namespace Akamai\Analytics\AbstractService;

abstract class MediaReports extends Base
{
    const API_TYPE = 'media-reports';

    public function getApiType()
    {
        return self::API_TYPE;
    }

    public function getDimensions()
    {
        return $this->request('GET', $this->buildEndpoint('dimensions'));
    }

    public function getMetrics()
    {
        return $this->request('GET', $this->buildEndpoint('metrics'));
    }

    public function getData(array $cpCodes, \DateTime $startDate, \DateTime $endDate, array $dimensions, array $metrics, array $params = [])
    {
        $endpoint = $this->buildEndpoint('data');

        $params['cpcodes'] = implode(',', $cpCodes);

        return $this->execute($endpoint, $startDate, $endDate, $dimensions, $metrics, $params);
    }
}
