<?php

namespace Tests\Cases;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class BaseClientMock
{
    /**
     * @var RequestInterface
     */
    protected $request;

    public function response($code, $body, $headers = [], $reason = null)
    {
        if (is_array($body)) {
            $body = json_encode($body);
        }

        $headers = array_replace([
            'Server' => 'nginx/1.10.3 (Ubuntu)',
            'Date' => 'Thu, 25 Feb 2021 00:25:03 GMT',
            'Content-Type' => 'application/vnd.bancolombia.v1+json',
            'Content-Length' => '430',
            'Connection' => 'keep-alive',
            'X-Backside-Transport' => 'OK OK',
            'X-Global-Transaction-ID' => 'a31c559f6036ee5f3cb3eabd',
            'Access-Control-Expose-Headers' => 'APIm-Debug-Trans-Id, X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset, X-Global-Transaction-ID',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST',
            'X-RateLimit-Limit' => 'name=rate-limit,100;',
            'X-RateLimit-Remaining' => 'name=rate-limit,98;',
        ], $headers);

        return new FulfilledPromise(
            new Response($code, $headers, utf8_decode($body), '1.1', utf8_decode($reason))
        );
    }

    public function acquirer()
    {
        return $this->request->getHeaderLine('acquirer');
    }
}
