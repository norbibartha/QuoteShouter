<?php

namespace App\Controller;

use App\Exceptions\InvalidParameterException;
use App\Services\QuotesService;
use Exception;

class QuotesController extends BaseController
{
    public function __construct(private QuotesService $quotesService)
    {
    }

    /**
     * Get list of quotes shouted by person ({name})
     * Send proper error message with HTTP code if something goes wrong
     *
     *  Endpoint: [route: "/shout/{name}?limit={number}", methods: ['GET']]
     *
     * @param array $params
     *
     * @return void
     */
    public function shoutAction(array $params): void
    {
        $pathVariables = $params['pathVariables'];
        $queryParams = $params['queryParams'];

        try {
            $name = $pathVariables[2];
            $limit = !empty($queryParams['limit']) ? (int)$queryParams['limit'] : 1;

            if ($limit > 10) {
                throw new InvalidParameterException(self::HTTP_ERROR_MESSAGE_INADEQUATE_LIMIT_PARAMETER);
            }

            $quotes = $this->quotesService->getQuotesByAuthor(authorName: $name, limit: $limit);

            $this->sendOutput($quotes, [self::HEADER_HTTP_OK]);
        } catch (InvalidParameterException $exception) {
            $this->sendOutput(
                data: ['error' => $exception->getMessage()],
                httpHeaders: [self::HEADER_HTTP_BAD_REQUEST]
            );
        } catch (Exception $exception) {
            $this->sendOutput(
                data: [
                    'error' => self::HTTP_ERROR_MESSAGE_INTERNAL_SERVER_ERROR,
                    'exceptionMessage' => $exception->getMessage(),
                ],
                httpHeaders: [self::HEADER_HTTP_INTERNAL_SERVER_ERROR]
            );
        }
    }
}