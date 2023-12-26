<?php

namespace Alma\API\Endpoints;

use Alma\API\Entities\Insurance\Contract;
use Alma\API\Entities\Insurance\File;
use Alma\API\Entities\Insurance\Subscription;
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
            $files = [];
			$response = $this->request(self::INSURANCE_PATH.'insurance-contracts/' . $insuranceContractExternalId . '?cms_reference=' . $cmsReference . '&product_price=' . $productPrice)->get();
			if ($response->isError()) {
				throw new RequestError($response->errorMessage, null, $response);
			}
            if (!$response->json) {
                return null;
            }
            foreach ($response->json['files'] as $file) {
                $files[] = new File(
                    $file['name'],
                    $file['type'],
                    $file['public_url']
                );
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
                $files
            );
		}

		throw new ParamsException('Invalid parameters');
	}

    /**
     * @throws ParamsException
     * @throws RequestError
     */
    public function subscription($subscriptionArray)
    {
        $subscriptionData = ['subscriptions' => []];
        if (gettype($subscriptionArray) !== 'array') {
            throw new ParamsException('Invalid Parameters');
        }
        foreach ($subscriptionArray as $subscription){
            if (get_class($subscription) !== Subscription::class) {
                throw new ParamsException('Invalid Parameters');
            }
            $subscriptionData['subscriptions'][] = [
                'insurance_contract_id' => $subscription->getContractId(),
                'cms_reference' => $subscription->getCmsReference(),
                'product_price' => $subscription->getProductPrice(),
                'subscriber' => [
                    'email' => $subscription->getSubscriber()->getEmail(),
                    'phone_number' => $subscription->getSubscriber()->getPhoneNumber(),
                    'last_name' => $subscription->getSubscriber()->getLastName(),
                    'first_name' => $subscription->getSubscriber()->getFirstName(),
                    'birthdate' => $subscription->getSubscriber()->getBirthDate(),
                    'address' => [
                        'address_line_1' => $subscription->getSubscriber()->getAddressLine1(),
                        'address_line_2' => $subscription->getSubscriber()->getAddressLine2(),
                        'zip_code' => $subscription->getSubscriber()->getZipCode(),
                        'city' => $subscription->getSubscriber()->getCity(),
                        'country' => $subscription->getSubscriber()->getCountry(),
                    ]
                ],
            ];
        }
        /**
         * TODO : Why this code does work ?!!!
        $response = $this->request(self::INSURANCE_PATH . 'insurance-contracts/subscriptions')
            ->setRequestBody($subscriptionData)
            ->post();
         */

        $request = $this->request(self::INSURANCE_PATH . 'insurance-contracts/subscriptions');
        $request->setRequestBody($subscriptionData);
        $response = $request->post();
        if ($response->isError()) {
            throw new RequestError($response->errorMessage, null, $response);
        }

        return $response->json;
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