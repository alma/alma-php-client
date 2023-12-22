<?php

namespace Alma\API\Endpoints;

use Alma\API\Entities\Insurance\Contract;
use Alma\API\Exceptions\ParamsException;
use Alma\API\RequestError;

class Insurance extends Base
{
	const INSURANCE_PATH = '/v1/insurance/';

    /**
     * @param string $insuranceContractExternalId
     * @param string $cmsReference
     * @param int $productPrice
     * @return Contract
     * @throws ParamsException
     * @throws RequestError
     */
	public function getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice)
	{
		if (gettype($cmsReference) === 'integer') {
			$cmsReference = (string)$cmsReference;
		}
		if (
            $this->checkParamValidated($cmsReference) &&
            $this->checkParamValidated($insuranceContractExternalId) &&
            $this->checkPriceFormat($productPrice)
        ){
			$response = $this->request(self::INSURANCE_PATH.'insurance-contracts/' . $insuranceContractExternalId . '?cms_reference=' . $cmsReference . '&product_price=' . $productPrice)->get();
			if ($response->isError()) {
				throw new RequestError($response->errorMessage, null, $response);
			}
            return new Contract(
                $response->json['id'],
                $response->json['name'],
                $response->json['protection_days'],
                $response->json['description'],
                $response->json['cover_area'],
                $response->json['compensation_area'],
                $response->json['exclusion_area'],
                $response->json['uncovered_area'],
                $response->json['price'],
                $response->json['files']
            );
		}

		throw new ParamsException('Invalid parameters');
	}

    /**
     * @param int $productPrice
     * @return false|int
     */
    private function checkPriceFormat($productPrice)
    {
        $validationProductReferenceIdRegex =  '/^[0-9]+$/';
        return preg_match($validationProductReferenceIdRegex, $productPrice);
    }

    /**
     * @param string $param
     * @return bool
     */
    private function checkParamValidated($param)
    {
        $validationProductReferenceIdRegex =  '/^[a-zA-Z0-9-_ ]+$/';

        return gettype($param) === 'string' && preg_match($validationProductReferenceIdRegex, $param);
    }
}