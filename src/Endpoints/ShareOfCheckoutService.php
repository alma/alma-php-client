<?php

namespace Alma\API\Endpoints;

use Alma\API\Exceptions\RequestException;
use Alma\API\Exceptions\ShareOfCheckoutServiceException;
use Psr\Http\Client\ClientExceptionInterface;

class ShareOfCheckoutService extends Base
{
    const SHARE_OF_CHECKOUT_ENDPOINT = '/v1/share-of-checkout';
    const SHARE_OF_CHECKOUT_CONSENT_ENDPOINT = self::SHARE_OF_CHECKOUT_ENDPOINT . '/consent';

    /**
     * @param array $data
     *
     * @return array
     * @throws ShareOfCheckoutServiceException
     */
    public function share(array $data): array
    {
        try {
            $request = null;
            $request = $this->createPutRequest(self::SHARE_OF_CHECKOUT_ENDPOINT, $data);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new ShareOfCheckoutServiceException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new ShareOfCheckoutServiceException($response->getReasonPhrase(), $request, $response);
        }

        return $response->getJson();
    }

    /**
     *
     * @return array
     * @throws ShareOfCheckoutServiceException
     */
    public function getLastUpdateDates(): array
    {
        try {
            $request = null;
            $request = $this->createGetRequest(self::SHARE_OF_CHECKOUT_ENDPOINT);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new ShareOfCheckoutServiceException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new ShareOfCheckoutServiceException($response->getReasonPhrase(), $request, $response);
        }
        return $response->getJson();
    }

    /**
     * Consent share of checkout
     * @throws ShareOfCheckoutServiceException
     */
    public function addConsent()
    {
        try {
            $request = null;
            $request = $this->createPostRequest(self::SHARE_OF_CHECKOUT_CONSENT_ENDPOINT);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new ShareOfCheckoutServiceException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new ShareOfCheckoutServiceException($response->getReasonPhrase(), $request, $response);
        }
    }

    /**
     * Consent share of checkout
     * @throws ShareOfCheckoutServiceException
     */
    public function removeConsent()
    {
        try {
            $request = null;
            $request = $this->createDeleteRequest(self::SHARE_OF_CHECKOUT_CONSENT_ENDPOINT);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new ShareOfCheckoutServiceException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new ShareOfCheckoutServiceException($response->getReasonPhrase(), $request, $response);
        }
    }
}
