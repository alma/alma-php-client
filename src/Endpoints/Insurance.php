<?php

namespace Alma\API\Endpoints;

use Alma\API\Entities\Insurance\Contract;
use Alma\API\Entities\Insurance\File;
use Alma\API\Entities\Insurance\Subscription;
use Alma\API\Exceptions\MissingKeyException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Lib\ArrayUtils;
use Alma\API\Lib\InsuranceValidator;
use Alma\API\RequestError;

class Insurance extends Base
{
	const INSURANCE_PATH = '/v1/insurance/';

    /**
     * @var InsuranceValidator
     */
    protected $insuranceValidator;

    /**
     * @var ArrayUtils
     */
    protected $arrayUtils;

    public function __construct($client_context)
    {
        parent::__construct($client_context);

        $this->insuranceValidator = new InsuranceValidator();
        $this->arrayUtils = new ArrayUtils();
    }

    /**
     * @param int $insuranceContractExternalId
     * @param string $cmsReference
     * @param int|string $productPrice
     * @return Contract|null
     * @throws MissingKeyException
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
	public function getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice)
	{
		if (is_int($cmsReference)) {
			$cmsReference = (string)$cmsReference;
		}

        $this->checkParameters($cmsReference, $insuranceContractExternalId, $productPrice);

        $response = $this->request(
            sprintf(
                "%sinsurance-contracts/%s?cms_reference=%s&product_price=%d",
                self::INSURANCE_PATH,
                $insuranceContractExternalId,
                $cmsReference,
                $productPrice
            )
        )->get();

        if ($response->isError()) {
            throw new RequestException($response->errorMessage, null, $response);
        }

        // @todo is it a possible case, or do we need to throw an exception
        if (!$response->json) {
            return null;
        }

        $this->arrayUtils->checkMandatoryKeys(Contract::$mandatoryFields, $response->json);

        $files = $this->getFiles($response->json);

        return $this->buildContract($response->json, $files);
	}

    /**
     * @param string $cmsReference
     * @param int $insuranceContractExternalId
     * @param string $productPrice
     * @return void
     * @throws ParametersException
     */
    public function checkParameters($cmsReference, $insuranceContractExternalId, $productPrice)
    {
        $this->insuranceValidator->checkParamFormat($cmsReference, 'CMS reference');
        $this->insuranceValidator->checkParamFormat($insuranceContractExternalId, 'Insurance contract external id');
        $this->insuranceValidator->checkPriceFormat($productPrice);
    }

    /**
     * @param $subscriptionArray
     * @param null $paymentId
     * @return mixed
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function subscription($subscriptionArray, $paymentId = null)
    {

        if (!is_array($subscriptionArray)) {
            throw new ParametersException(
                sprintf(
                    'The subscription array must to be an array, "%s" found',
                    gettype($subscriptionArray)
                )
            );
        }

        $subscriptionData = $this->buildSubscriptionData($subscriptionArray, $paymentId);

        /**
         * TODO : Why this code does work ?!!!
        $response = $this->request(self::INSURANCE_PATH . 'insurance-contracts/subscriptions')
            ->setRequestBody($subscriptionData)
            ->post();
         */

        $request = $this->request(self::INSURANCE_PATH . 'subscriptions');
        $request->setRequestBody($subscriptionData);
        $response = $request->post();

        if ($response->isError()) {
            throw new RequestException($response->errorMessage, null, $response);
        }

        return $response->json;
    }

    /**
     * @param array $subscriptionArray
     * @param string|null $paymentId
     * @return array
     * @throws ParametersException
     */
    protected function buildSubscriptionData($subscriptionArray, $paymentId = null)
    {
        $subscriptionData = ['subscriptions' => []];

        /**
         * @var Subscription $subscription
         */
        foreach ($subscriptionArray as $subscription) {

            if (
                !is_object($subscription)
                || !$subscription instanceof Subscription
            ) {
                throw new ParametersException('The subscription array does not contains Subscription object');
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

        if (
            null !== $paymentId
            && is_string($paymentId)
        ) {
            $subscriptionData['payment_id'] = $paymentId;
        }

        return $subscriptionData;
    }

    /**
     * @param array $data
     * @return array
     * @throws MissingKeyException
     */
    protected function getFiles($data)
    {
        $files = [];

        foreach ($data['files'] as $file) {

            $this->arrayUtils->checkMandatoryKeys(File::$mandatoryFields, $file);

            $files[] = new File(
                $file['name'],
                $file['type'],
                $file['public_url']
            );
        }

        return $files;
    }

    /**
     * @param array $data
     * @param array $files
     * @return Contract
     */
    protected function buildContract($data, $files)
    {
        return new Contract(
            $data['id'],
            $data['name'],
            $data['protection_days'],
            $data['description'],
            $data['cover_area'],
            $data['compensation_area'],
            $data['exclusion_area'],
            $data['uncovered_area'],
            $data['price'],
            $files
        );
    }
}
