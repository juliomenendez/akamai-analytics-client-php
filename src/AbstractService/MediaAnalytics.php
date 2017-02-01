<?php

namespace Akamai\Analytics\AbstractService;

abstract class MediaAnalytics extends Base
{
    const API_TYPE_MEDIA_ANALYTICS = 'media-analytics';
    const API_RESOURCE_REPORT_PACK = 'report-packs';

    const API_CHILD_RESOURCE_DATA_STORE = 'data-stores';
    const API_CHILD_RESOURCE_DATA_SOURCE = 'data-sources';
    const API_CHILD_RESOURCE_DATA = 'data';

    public function getApiType()
    {
        return self::API_TYPE_MEDIA_ANALYTICS;
    }

    public function getReportPacks()
    {
        $endpoint = $this->buildEndpoint(self::API_RESOURCE_REPORT_PACK);
        return $this->get($endpoint);
    }

    public function getReportPack($id)
    {
        $endpoint = $this->buildEndpoint(self::API_RESOURCE_REPORT_PACK, $id);
        return $this->get($endpoint);
    }

    public function getDataStores($reportId)
    {
        $endpoint = $this->buildEndpoint(
            self::API_RESOURCE_REPORT_PACK, $reportId, self::API_CHILD_RESOURCE_DATA_STORE
        );

        return $this->get($endpoint);
    }

    public function getDataSources($reportId)
    {
        $endpoint = $this->buildEndpoint(
            self::API_RESOURCE_REPORT_PACK, $reportId, self::API_CHILD_RESOURCE_DATA_SOURCE
        );

        return $this->get($endpoint);
    }

    public function getData($reportId, \DateTime $startDate, \DateTime $endDate, array $dimensions, array $metrics, array $params = [])
    {
        $startDate = $this->prepareDateParam($startDate);
        $endDate = $this->prepareDateParam($endDate);
        $dimensions = implode(',', $dimensions);
        $metrics = implode(',', $metrics);

        $endpoint = $this->buildEndpoint(
            self::API_RESOURCE_REPORT_PACK, $reportId, self::API_CHILD_RESOURCE_DATA
        );

        return $this->get($endpoint, [
            'query' => array_merge($params, [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'dimensions' => $dimensions,
                'metrics' => $metrics
            ])
        ]);
    }
}
