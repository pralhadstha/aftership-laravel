<?php

declare(strict_types=1);

namespace AfterShip\Drivers;

use AfterShip\Contracts\DriverInterface;
use AfterShip\Support\ExceptionMapper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

final class SdkDriver implements DriverInterface
{
    private Client $client;

    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly int $timeout,
    ) {
        $this->client = new Client([
            'base_uri' => rtrim($this->baseUrl, '/') . '/',
            'timeout' => $this->timeout,
            'headers' => [
                'as-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function createTracking(array $data): array
    {
        return $this->request('POST', 'trackings', ['tracking' => $data]);
    }

    public function getTracking(string $id): array
    {
        return $this->request('GET', "trackings/{$id}");
    }

    public function listTrackings(array $query = []): array
    {
        return $this->request('GET', 'trackings', query: $query);
    }

    public function updateTracking(string $id, array $data): array
    {
        return $this->request('PUT', "trackings/{$id}", ['tracking' => $data]);
    }

    public function deleteTracking(string $id): array
    {
        return $this->request('DELETE', "trackings/{$id}");
    }

    public function markTrackingCompleted(string $id, string $reason = 'DELIVERED'): array
    {
        return $this->request('POST', "trackings/{$id}/mark-as-completed", [
            'reason' => $reason,
        ]);
    }

    public function listCouriers(): array
    {
        return $this->request('GET', 'couriers/all');
    }

    public function detectCourier(array $data): array
    {
        return $this->request('POST', 'couriers/detect', ['tracking' => $data]);
    }

    public function getCourier(string $slug): array
    {
        return $this->request('GET', "couriers/{$slug}");
    }

    public function estimateDeliveryDate(array $data): array
    {
        return $this->request('POST', 'estimated-delivery-date', $data);
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    private function request(string $method, string $uri, array $body = [], array $query = []): array
    {
        $options = [];

        if (!empty($body)) {
            $options['json'] = $body;
        }

        if (!empty($query)) {
            $options['query'] = $query;
        }

        try {
            $response = $this->client->request($method, $uri, $options);

            return $this->decodeResponse($response);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $responseBody = $this->decodeResponse($response);

            throw ExceptionMapper::fromHttpResponse($statusCode, $responseBody);
        } catch (GuzzleException $e) {
            throw new \AfterShip\Exceptions\ApiException(
                message: 'AfterShip API request failed: ' . $e->getMessage(),
                code: (int) $e->getCode(),
                previous: $e,
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeResponse(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($body, true);

        return $decoded ?? [];
    }
}
