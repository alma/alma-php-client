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
	public function getInsuranceContracts($productReference)
	{
		$validationProductReferenceIdRegex =  '/^[a-zA-Z0-9- ]+$/';

		if (gettype($productReference) === 'integer') {
			$productReference = (string)$productReference;
		}
		if (gettype($productReference) === 'string' && preg_match($validationProductReferenceIdRegex, $productReference)){
			$response = $this->request(self::INSURANCE_PATH.'insurance-contracts?cms_product_id='.$productReference)->get();
			if ($response->isError()) {
				throw new RequestError($response->errorMessage, null, $response);
			}
			//TODO Wait for insurance data for return
			return;
		}

		throw new ParamsException('Invalid product reference');
	}
}