<?php

namespace Tests\Feature;

use PlacetoPay\BancolombiaSDK\BancolombiaButton;
use PlacetoPay\BancolombiaSDK\Exceptions\BancolombiaException;
use Tests\BaseTestCase;

class NotificationTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_handles_the_validation_for_signature()
    {
        // This secret is provided by us to Bancolombia
        $clientSecret = '1Fj8eK4rlyUd252L48herdrnEZ';

        $response = BancolombiaButton::handleCallback(
            json_decode('{"transferVoucher":"null","transferAmount":"3458.00","transferStateDescription":"Transaction rejected by user","sign":"747898130598830097d87b8c9ffc14ae28a4cb433fff7f96dcf7a1661d0e6fd436bb59fca03ca682a5004c7c6c9641d43fc9608af70cbe2ac2fa3b83c2dba510","requestDate":"2021-02-25T08:33:06.948-0500","transferState":"rejected","transferDate":"null","transferCode":"_SQC5uKmF6L","transferReference":"1614259966","commerceTransferButtonId":"h4ShG3NER1C"}', true),
            $clientSecret
        );

        $this->assertEquals('_SQC5uKmF6L', $response->code());
        $this->assertEquals('rejected', $response->state());
    }

    /**
     * @test
     */
    public function it_throws_exception_if_the_signature_is_invalid()
    {
        $this->expectException(BancolombiaException::class);
        $clientSecret = 'not_the_secret';

        BancolombiaButton::handleCallback(
            json_decode('{"transferVoucher":"null","transferAmount":"3458.00","transferStateDescription":"Transaction rejected by user","sign":"747898130598830097d87b8c9ffc14ae28a4cb433fff7f96dcf7a1661d0e6fd436bb59fca03ca682a5004c7c6c9641d43fc9608af70cbe2ac2fa3b83c2dba510","requestDate":"2021-02-25T08:33:06.948-0500","transferState":"rejected","transferDate":"null","transferCode":"_SQC5uKmF6L","transferReference":"1614259966","commerceTransferButtonId":"h4ShG3NER1C"}', true),
            $clientSecret
        );
    }
}
