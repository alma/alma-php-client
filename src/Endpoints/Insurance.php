<?php

namespace Alma\API\Endpoints;

use Alma\API\Exceptions\ParamsException;
use Alma\API\RequestError;

class Insurance extends Base
{
	const INSURANCE_PATH = '/v1/insurance/';

	/**
	 * @throws ParamsException
	 */
	public function getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice)
	{
		$validationProductReferenceIdRegex =  '/^[a-zA-Z0-9- ]+$/';

		if (gettype($cmsReference) === 'integer') {
			$cmsReference = (string)$cmsReference;
		}
		if (gettype($cmsReference) === 'string' && preg_match($validationProductReferenceIdRegex, $cmsReference)){
			$response = $this->request(self::INSURANCE_PATH.'insurance-contracts/' . $insuranceContractExternalId . '?cms_reference=' . $cmsReference . '&product_price=' . $productPrice)->get();
			if ($response->isError()) {
				throw new RequestError($response->errorMessage, null, $response);
			}
			//TODO Wait for insurance data for return
			return;
		}

		throw new ParamsException('Invalid product reference');
	}
}