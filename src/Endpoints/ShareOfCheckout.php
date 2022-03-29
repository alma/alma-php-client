<?php

namespace Alma\API\Endpoints;

use Alma\API\RequestError;

class ShareOfCheckout extends Base
{
    const SHARE_OF_CHECKOUT_PATH = '/v1/share-of-checkout';


    /**
     * @param array $data
     *
     * @throws RequestError
     */
    public function share($data)
    {
        $res = $this->request(self::SHARE_OF_CHECKOUT_PATH)->setRequestBody($data)->post();
        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }
        return $res->json;
    }

}
