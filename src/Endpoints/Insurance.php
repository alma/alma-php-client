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

    public function __construct($client_context)
    {
        parent::__construct($client_context);

        $this->insuranceValidator = new InsuranceValidator();
    }

    /**
     * @param int $insuranceContractExternalId
     * @param string $cmsReference
     * @param int|string $productPrice
     * @param string | null $customerSessionId
     * @param string | null $cartId
     * @return Contract|null
     * @throws MissingKeyException
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice, $customerSessionId = null, $cartId = null)
    {
        if (is_int($cmsReference)) {
            $cmsReference = (string)$cmsReference;
        }

        $this->checkParameters($cmsReference, $insuranceContractExternalId, $productPrice);

        $request = $this->request(sprintf(
                "%sinsurance-contracts/%s",
                self::INSURANCE_PATH,
                $insuranceContractExternalId)
        )->setQueryParams([
            'cms_reference' => $cmsReference,
            'product_price' => $productPrice
        ]);


        $this->addCustomerSessionToRequest($request, $customerSessionId, $cartId);

        $response = $request->get();

        if ($response->isError()) {
            throw new RequestException($response->errorMessage, null, $response);
        }

        // @todo is it a possible case, or do we need to throw an exception
        if (!$response->json) {
            return null;
        }
        $arrayUtils = new ArrayUtils();
        $arrayUtils->checkMandatoryKeys(Contract::$mandatoryFields, $response->json);

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
     * @param string | null $customerSessionId
     * @param string | null $cartId
     * @return mixed
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function subscription($subscriptionArray, $paymentId = null, $customerSessionId = null, $cartId = null)
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
        $request = $this->request(self::INSURANCE_PATH . 'subscriptions')
            ->setRequestBody($subscriptionData);

        $this->addCustomerSessionToRequest($request, $customerSessionId, $cartId);

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
        $arrayUtils = new ArrayUtils();

        foreach ($data['files'] as $file) {
            $arrayUtils->checkMandatoryKeys(File::$mandatoryFields, $file);

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

    /**
     * @param \Alma\API\Request $request
     * @param string | null $customerSessionId
     * @param string | null $cartId
     * @return void
     */
    public function addCustomerSessionToRequest($request, $customerSessionId = null, $cartId = null)
    {
        if ($customerSessionId) {
            $request->addCustomerSessionIdToHeader($customerSessionId);
        }

        if ($cartId) {
            $request->addCartIdToHeader($cartId);
        }
    }
}
