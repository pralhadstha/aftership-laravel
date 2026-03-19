<?php

declare(strict_types=1);

namespace AfterShip\Drivers;

use AfterShip\Contracts\DriverInterface;
use AfterShip\Support\ExceptionMapper;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

final class HttpDriver implements DriverInterface
{
    private PendingRequest $http;

    public function __construct(
        private readonly HttpFactory $httpFactory,
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly int $timeout,
        private readonly string $apiVersion = 'tracking/2026-01',
    ) {
        $this->http = $this->httpFactory
            ->baseUrl(rtrim($this->baseUrl, '/') . '/' . ltrim($this->apiVersion, '/'))
            ->timeout($this->timeout)
            ->withHeaders([
                'as-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]);
    }

    public function createTracking(array $data): array
    {
        return $this->request('POST', '/trackings', $data);
    }

    public function getTracking(string $id): array
    {
        return $this->request('GET', "/trackings/{$id}");
    }

    public function listTrackings(array $query = []): array
    {
        return $this->request('GET', '/trackings', query: $query);
    }

    public function updateTracking(string $id, array $data): array
    {
        return $this->request('PUT', "/trackings/{$id}", $data);
    }

    public function deleteTracking(string $id): array
    {
        return $this->request('DELETE', "/trackings/{$id}");
    }

    public function markTrackingCompleted(string $id, string $reason = 'DELIVERED'): array
    {
        return $this->request('POST', "/trackings/{$id}/mark-as-completed", [
            'reason' => $reason,
        ]);
    }

    public function listCouriers(): array
    {
        return $this->request('GET', '/couriers');
    }

    public function detectCourier(array $data): array
    {
        return $this->request('POST', '/couriers/detect', ['tracking' => $data]);
    }

    public function getCourier(string $slug): array
    {
        return $this->request('GET', '/couriers', query: ['slug' => $slug]);
    }

    public function estimateDeliveryDate(array $data): array
    {
        return $this->request('POST', '/estimated-delivery-date/predict', $data);
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    private function request(string $method, string $uri, array $body = [], array $query = []): array
    {
        $request = $this->http;

        if (!empty($query)) {
            $request = $request->withQueryParameters($query);
        }

        /** @var Response $response */
        $response = match (strtoupper($method)) {
            'GET' => $request->get($uri),
            'POST' => $request->post($uri, $body),
            'PUT' => $request->put($uri, $body),
            'DELETE' => $request->delete($uri),
            default => $request->get($uri),
        };

        $responseData = $response->json() ?? [];

        if ($response->failed()) {
            throw ExceptionMapper::fromHttpResponse($response->status(), $responseData);
        }

        return $responseData;
    }
}
