<?php

namespace App\Controller;

class BaseController
{
    protected const HEADER_HTTP_OK = 'HTTP/1.1 200 OK';
    protected const HEADER_HTTP_BAD_REQUEST = 'HTTP/1.1 400 Bad Request';
    protected const HEADER_HTTP_INTERNAL_SERVER_ERROR = 'HTTP/1.1 500 Internal Server Error';
    protected const CONTENT_TYPE_JSON = 'Content-Type: application/json';
    protected const HTTP_ERROR_MESSAGE_INADEQUATE_LIMIT_PARAMETER = 'Limit cannot be more than 10.';
    protected const HTTP_ERROR_MESSAGE_INTERNAL_SERVER_ERROR = 'Something went wrong';

    /**
     * Send API output.
     *
     * @param array $data
     * @param array $httpHeaders
     */
    protected function sendOutput(array $data, array $httpHeaders = []): void
    {
        $httpHeaders = array_merge($httpHeaders, [self::CONTENT_TYPE_JSON]);
        foreach ($httpHeaders as $httpHeader) {
            header($httpHeader);
        }

        echo json_encode($data);
        exit;
    }
}