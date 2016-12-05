<?php

namespace Akamai\Analytics\Exception;

class RequestException extends BaseException
{
    protected $statusCode = 0;
    protected $fieldErrors = [];

    public function __construct($message = "", $code = 0, $previous = NULL)
    {
        parent::__construct($message, $code, $previous);

        $data = json_decode($message, true);

        if (!json_last_error()) {
            $this->message = $data['title'] . ': ' . $data['detail'];
            $this->statusCode = isset($data['httpStatus']) ? $data['httpStatus'] : $data['status'];

            if (isset($data['appError'])) {
                foreach ($data['appError'] as $fieldError) {
                    $this->fieldErrors[$fieldError['field']] = $fieldError['message'];
                }
            }
        }
    }

    public function getHttpStatusCode()
    {
        return $this->statusCode;
    }

    public function getFieldErrors()
    {
        return $this->fieldErrors;
    }
}
