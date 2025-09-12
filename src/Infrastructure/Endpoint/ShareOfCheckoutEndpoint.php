<?php

namespace Alma\API\Infrastructure\Endpoint;

use Alma\API\Infrastructure\Exception\Endpoint\ShareOfCheckoutEndpointException;
use Alma\API\Infrastructure\Exception\RequestException;
use Psr\Http\Client\ClientExceptionInterface;

class ShareOfCheckoutEndpoint extends AbstractEndpoint
{
    const SHARE_OF_CHECKOUT_ENDPOINT = '/v1/share-of-checkout';
    const SHARE_OF_CHECKOUT_CONSENT_ENDPOINT = self::SHARE_OF_CHECKOUT_ENDPOINT . '/consent';

    /**
     * @param array $data
     *
     * @return array
     * @throws ShareOfCheckoutEndpointException
     */
    public function share(array $data): array
    {
        try {
            $request = null;
            $request = $this->createPutRequest(self::SHARE_OF_CHECKOUT_ENDPOINT, $data);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new ShareOfCheckoutEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new ShareOfCheckoutEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return $response->getJson();
    }

    /**
     *
     * @return array
     * @throws ShareOfCheckoutEndpointException
     */
    public function getLastUpdateDates(): array
    {
        try {
            $request = null;
            $request = $this->createGetRequest(self::SHARE_OF_CHECKOUT_ENDPOINT);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new ShareOfCheckoutEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new ShareOfCheckoutEndpointException($response->getReasonPhrase(), $request, $response);
        }
        return $response->getJson();
    }

    /**
     * Consent share of checkout
     * @return bool
     * @throws ShareOfCheckoutEndpointException
     */
    public function addConsent(): bool
    {
        try {
            $request = null;
            $request = $this->createPostRequest(self::SHARE_OF_CHECKOUT_CONSENT_ENDPOINT);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new ShareOfCheckoutEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new ShareOfCheckoutEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return true;
    }

    /**
     * Consent share of checkout
     * @return bool
     * @throws ShareOfCheckoutEndpointException
     */
    public function removeConsent(): bool
    {
        try {
            $request = null;
            $request = $this->createDeleteRequest(self::SHARE_OF_CHECKOUT_CONSENT_ENDPOINT);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new ShareOfCheckoutEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new ShareOfCheckoutEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return true;
    }
}
