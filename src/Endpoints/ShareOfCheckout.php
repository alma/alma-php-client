<?php

namespace Alma\API\Endpoints;

use Alma\API\RequestError;

class ShareOfCheckout extends Base
{
    const SHARE_OF_CHECKOUT_PATH = '/v1/share-of-checkout/';

    /**
     * @param array $data
     *
     * @return array
     * @throws RequestError
     */
    public function share($data)
    {
        $res = $this->request(self::SHARE_OF_CHECKOUT_PATH)->setRequestBody($data)->put();
        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }
        return $res->json;
    }

    /**
     *
     * @return array
     * @throws RequestError
     */
    public function getLastUpdateDates()
    {
        $res = $this->request(self::SHARE_OF_CHECKOUT_PATH)->get();
        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }
        return $res->json;
    }

    /**
     * Consent share of checkout
     * @throws RequestError
     */
    public function addConsent()
    {
        $res = $this->request(self::SHARE_OF_CHECKOUT_PATH . 'consent')->setRequestBody()->post();
        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }
    }

    /**
     * Consent share of checkout
     * @throws RequestError
     */
    public function removeConsent()
    {
        $res = $this->request(self::SHARE_OF_CHECKOUT_PATH . 'consent')->delete();
        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }
    }
}
