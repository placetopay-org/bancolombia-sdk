<?php

namespace Tests\Cases;

use Psr\Http\Message\RequestInterface;

class ExternalSystemMock extends BaseClientMock
{
    public function __invoke(RequestInterface $request, array $options)
    {
        $this->request = $request;

        if (strpos($request->getUri()->getPath(), 'oauth2/token') !== false) {
            return $this->authenticate();
        } elseif (strpos($request->getUri()->getPath(), 'action/registry') !== false) {
            return $this->transferIntent();
        } elseif (strpos($request->getUri()->getPath(), 'validate') !== false) {
            return $this->transferQuery();
        } elseif (strpos($request->getUri()->getPath(), 'health') !== false) {
            return $this->healthCheck();
        }

        dd($request->getUri());
    }

    private function authenticate()
    {
        parse_str($this->request->getBody()->getContents(), $parameters);

        return $this->response(200, [
            'token_type' => 'Bearer',
            'access_token' => 'AAIkM2IyNzIxZTctMWFmYy00NTU3LThkOTAtYjBlYjIwZGZkZTkxxES5ev-vsZaotr5JvvQKvDpLKCm73idKQhPHVikS37rb8UYMt2bVxUwpH_3nBkamnL1UQOXQXn16D-oZl2dVNVZWADkAbPhSzibzUNcQ9C3feUXu-Y9S3sFhaB2geCEaSxjYHE7VH6kT9fbqwFEWU8JQ7HxCUw6DZFSlh8kvgFqCk8n0JoYIxcVF5InwafLs',
            'expires_in' => 1200,
            'consented_on' => time(),
            'scope' => $parameters['scope'],
        ]);
    }

    private function transferIntent()
    {
        $parameters = json_decode($this->request->getBody()->getContents(), true);
        $parameters = $parameters['data'][0];

        if (empty($parameters['commerceTransferButtonId']) || $parameters['commerceTransferButtonId'] !== 'h4ShG3NER1C') {
            return $this->response(400, [
                'meta' => [
                    '_messageId' => '544f0085-f175-4a83-bb63-be955ab935a2',
                    '_version' => '1.0',
                    '_requestDate' => '2021-03-04T15:31:30.908Z',
                    '_responseSize' => 1,
                    '_clientRequest' => '3b2721e7-1afc-4557-8d90-b0eb20dfde91',
                ],
                'errors' => [
                    [
                        'href' => 'https://tools.ietf.org/html/rfc7231#section-6.5.1',
                        'status' => '400',
                        'code' => 'BP400',
                        'title' => 'Bad Request',
                        'detail' => 'El botón no existe.',
                    ],
                ],
            ]);
        }

        if (strpos($parameters['transferReference'], 'UNAVAILABLE') !== false) {
            return $this->response('500', [
                'meta' => [
                    '_messageId' => '9621d086-02c3-4ce0-b33d-9f551ca377e6',
                    '_version' => '1.0',
                    '_requestDate' => '2021-02-24T19:25:03.324Z',
                    '_responseSize' => 1,
                    '_clientRequest' => '3b2721e7-1afc-4557-8d90-b0eb20dfde91',
                ],
                'errors' => [
                    [
                        'href' => 'https://tools.ietf.org/html/rfc7231#section-6.6.1',
                        'status' => '500',
                        'code' => 'SP500',
                        'title' => 'Internal Server Error',
                        'detail' => 'Error leyendo la peticion del servidor {"status":503,"title":"Service Unavailable"}',
                    ],
                ],
            ]);
        }

        // In order to test some cases will associate given references with the code returned to them
        $references = [
            '123456789' => '_SQC5uKmF6L',
        ];
        $code = $references[$parameters['transferReference']] ?? '_' . substr(md5($parameters['transferReference']), 10);

        return $this->response(200, [
            'meta' => [
                '_messageId' => '37f26bca-2ab3-4612-8ae6-96943dd5a24f',
                '_version' => '1.0',
                '_requestDate' => '2021-02-25T08:32:47.062Z',
                '_clientRequest' => '3b2721e7-1afc-4557-8d90-b0eb20dfde91',
            ],
            'data' => [
                [
                    'header' => [
                        'type' => 'Transference',
                        'id' => $code,
                    ],
                    'transferCode' => $code,
                    'redirectURL' => 'https://sandbox-boton-dev.apps.ambientesbc.com/web/transfer-gateway/checkout/' . $code,
                ],
            ],
        ]);
    }

    private function transferQuery()
    {
        preg_match('/transfer\/(\w+)\/action/', $this->request->getUri()->getPath(), $matches);

        if (!($reference = $matches[1] ?? null)) {
            return $this->response(500, []);
        }

        if (strpos($reference, 'PEND') !== false) {
            $data = [
                [
                    'header' => [
                        'type' => 'Transference',
                        'id' => $reference,
                    ],
                    'transferState' => 'pending',
                    'transferStateDescription' => null,
                    'transferVoucher' => null,
                    'transferDate' => null,
                    'transferReference' => '1614721251',
                    'transferAmount' => 3458,
                ],
            ];
        } elseif (strpos($reference, 'REJE') !== false) {
            $data = [
                [
                    'header' => [
                        'type' => 'Transference',
                        'id' => $reference,
                    ],
                    'transferState' => 'rejected',
                    'transferStateDescription' => 'Expired',
                    'transferVoucher' => null,
                    'transferDate' => null,
                    'transferReference' => '1614721290',
                    'transferAmount' => 3458,
                ],
            ];
        } else {
            $data = [
                [
                    'header' => [
                        'type' => 'Transference',
                        'id' => $reference,
                    ],
                    'transferState' => 'approved',
                    'transferStateDescription' => null,
                    'transferVoucher' => 'TRjJvCHT8qNj',
                    'transferDate' => '2021-03-02T14:57:27',
                    'transferReference' => '1614714969',
                    'transferAmount' => 3458,
                ],
            ];
        }

        return $this->response(200, [
            'meta' => [
                '_messageId' => 'ac100199-301d-4df6-b532-be955ab93821',
                '_version' => '1.0',
                '_requestDate' => '2021-03-02T14:59:17.649Z',
                '_clientRequest' => '3b2721e7-1afc-4557-8d90-b0eb20dfde91',
            ],
            'data' => $data,
        ]);
    }

    private function healthCheck()
    {
        return $this->response(200, []);
    }
}
