<?php

namespace Akamai\Analytics;

abstract class AbstractService
{
    const VERSION = '0.0.1';
    const API_VERSION = 'v1';
    const API_PREFIX = 'media-analytics';

    const API_TYPE_AUDIENCE = 'audience-analytics';
    const API_TYPE_QOS = 'qos-monitor';
    const API_TYPE_VIEWER = 'viewer-diagnostics';
    const API_TYPE_DOWNLOAD = 'download-analytics';

    const API_RESOURCE_REPORT_PACK = 'report-packs';

    const API_CHILD_RESOURCE_DATA_STORE = 'data-stores';
    const API_CHILD_RESOURCE_DATA_SOURCE = 'data-sources';
    const API_CHILD_RESOURCE_DATA = 'data';

    const DATE_FORMAT = 'm/d/Y:H:i';

    public static $utcTz = null;

    private $edgeClient;
    private $endpointPrefix;

    public function __construct($host, $token, $secret, $accessToken)
    {
        $this->edgeClient = new \Akamai\Open\EdgeGrid\Client([
            'base_uri' => $host,
            'timeout' => '4.0'
        ]);

        $this->edgeClient->setAuth($token, $secret, $accessToken);

        $this->endpointPrefix = implode('/', ['', self::API_PREFIX, self::API_VERSION]);

        self::$utcTz = new \DateTimeZone('UTC');
    }

    public function getEdgeClient()
    {
        return $this->edgeClient;
    }

    abstract public function getReportType();

    protected function buildEndpoint($resource, $id = null, $childResource = null, $id = null)
    {
        return implode('/', array_merge([$this->endpointPrefix, $this->getReportType()], func_get_args()));
    }

    public function prepareDateParam(\DateTime $date)
    {
        return $date->setTimezone(self::$utcTz)->format(self::DATE_FORMAT);
    }

    protected function parseResponse($response)
    {
        $data = json_decode($response->getBody()->getContents(), true);

        if ($error = json_last_error()) {
            throw new Exception\BaseException($error);
        }

        return $data;
    }

    protected function get($endpoint, array $options = [])
    {
        return $this->request('GET', $endpoint, $options);
    }

    protected function request($method, $endpoint, array $options = [])
    {
        try {
            $response = $this->getEdgeClient()->request($method, $endpoint, $options);
        } catch (\GuzzleHttp\Exception\GuzzleException $ex) {
            if ($response = $ex->getResponse()) {
                $content = $response->getBody()->getContents();

                if (strpos($content, 'No data store found') !== false) {
                    throw new Exception\NoDataStoreException($content);
                }

                // This exception is given by an invalid combination of dimensions
                // and metrics (both should belong to same data store) or it could be due
                // to a `endDate` - `startDate` difference in days greater than
                // the `purgeIntervalInDays` of the Data Source
                if (strpos($content, 'Could not find the data store') !== false) {
                    throw new Exception\InvalidDataStoreParametersException($content);
                }

                throw new Exception\RequestException($content);
            } else {
                throw new Exception\BaseException($ex->getMessage());
            }
        }

        return $this->parseResponse($response);
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
