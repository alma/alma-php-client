<?php

namespace Alma\API\Endpoints;

use Alma\API\RequestError;

class ShareOfCheckout extends Base
{
    const SHARE_OF_CHECKOUT_PATH = '/v1/share-of-checkout/';

    /**
     * @param array $data
     *
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

}
