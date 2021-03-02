<?php

namespace PlacetoPay\BancolombiaSDK\Exceptions;

/**
 * Handles all the basic errors given by the external service.
 */
class ErrorResponseException extends BancolombiaException
{
    protected $meta;
    protected $title;

    public function __construct(array $meta, $code, $title, $detail)
    {
        $this->meta = $meta;
        $this->title = $title;

        parent::__construct($detail, $code);
    }

    public function meta(): array
    {
        return $this->meta;
    }

    /**
     * Basic message that the service responds on a failure case.
     * @return string
     */
    public function title()
    {
        return $this->title;
    }

    public static function fromResponse(array $response)
    {
        return new self(
            $response['meta'],
            $response['errors'][0]['status'],
            $response['errors'][0]['title'],
            $response['errors'][0]['detail'],
        );
    }
}
