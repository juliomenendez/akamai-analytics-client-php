<?php

namespace Akamai\Analytics\AbstractService;

use Akamai\Analytics\Exception\RequestException;
use Akamai\Analytics\Exception\BaseException;
use Akamai\Analytics\Exception\InvalidDataStoreParametersException;
use Akamai\Analytics\Exception\NoDataStoreException;

abstract class Base
{
    const VERSION = '0.0.5';
    const DATE_FORMAT = 'm/d/Y:H:i';

    public static $utcTz = null;

    protected static $apiVersion = 'v1';

    private $edgeClient;
    private $endpointPrefix;

    public function __construct($host, $token, $secret, $accessToken, array $httpOptions = [])
    {
        $httpOptions = array_merge([
            'timeout' => '120.0'
        ], $httpOptions, [
            'base_uri' => $host,
        ]);

        $this->edgeClient = new \Akamai\Open\EdgeGrid\Client($httpOptions);

        $this->edgeClient->setAuth($token, $secret, $accessToken);

        $this->endpointPrefix = implode('/', ['', $this->getApiType(), static::$apiVersion]);

        self::$utcTz = new \DateTimeZone('UTC');
    }

    public function getEdgeClient()
    {
        return $this->edgeClient;
    }

    abstract public function getApiType();
    abstract public function getReportType();

    protected function buildEndpoint($resource, $id = null, $childResource = null, $childId = null)
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
            throw new BaseException($error);
        }

        return $data;
    }

    protected function get($endpoint, array $options = [])
    {
        return $this->request('GET', $endpoint, $options);
    }

    protected function request($method, $endpoint, array $options = [])
    {//print_r($options);die();
        try {
            $options['headers'] = [
                'Accept' => 'application/json'
            ];

            $response = $this->getEdgeClient()->request($method, $endpoint, $options);
        } catch (\GuzzleHttp\Exception\GuzzleException $ex) {
            if ($response = $ex->getResponse()) {
                $content = $response->getBody()->getContents();

                if (strpos($content, 'No data store found') !== false) {
                    throw new NoDataStoreException($content);
                }

                // This exception is given by an invalid combination of dimensions
                // and metrics (both should belong to same data store) or it could be due
                // to a `endDate` - `startDate` difference in days greater than
                // the `purgeIntervalInDays` of the Data Source
                if (strpos($content, 'Could not find the data store') !== false) {
                    throw new InvalidDataStoreParametersException($content);
                }

                throw new RequestException($content);
            } else {
                throw new BaseException($ex->getMessage());
            }
        }

        return $this->parseResponse($response);
    }

    protected function execute($endpoint, \DateTime $startDate, \DateTime $endDate, array $dimensions, array $metrics, array $params = [])
    {
        $startDate = $this->prepareDateParam($startDate);
        $endDate = $this->prepareDateParam($endDate);
        $dimensions = implode(',', $dimensions);
        $metrics = implode(',', $metrics);

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
