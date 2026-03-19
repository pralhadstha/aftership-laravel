<?php

declare(strict_types=1);

namespace AfterShip\Drivers;

use AfterShip\Contracts\DriverInterface;
use AfterShip\Exceptions\InvalidConfigurationException;
use AfterShip\Support\ExceptionMapper;
use Tracking\Client;
use Tracking\Config;
use Tracking\Exception\AfterShipError;
use Tracking\Model\CreateTrackingRequest;
use Tracking\Model\DetectCourierRequest;
use Tracking\Model\EstimatedDeliveryDateRequest;
use Tracking\Model\GetCouriersQuery;
use Tracking\Model\GetTrackingsQuery;
use Tracking\Model\MarkTrackingCompletedByIdRequest;
use Tracking\Model\MarkTrackingCompletedByIdRequestReason;
use Tracking\Model\UpdateTrackingByIdRequest;

final class SdkDriver implements DriverInterface
{
    private Client $client;

    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly int $timeout,
    ) {
        if (!class_exists(Client::class)) {
            throw new InvalidConfigurationException(
                'The AfterShip SDK driver requires the "aftership/tracking-sdk" package. '
                . 'Install it with: composer require aftership/tracking-sdk'
            );
        }

        $this->client = new Client([
            'apiKey' => $this->apiKey,
            'authenticationType' => Config::AUTHENTICATION_TYPE_API_KEY,
            'domain' => rtrim($this->baseUrl, '/'),
            'timeout' => $this->timeout * 1000, // SDK uses milliseconds
        ]);
    }

    public function createTracking(array $data): array
    {
        try {
            $request = CreateTrackingRequest::fromArray($data, CreateTrackingRequest::class);
            $response = $this->client->tracking->createTracking($request);
            $tracking = $response->getData();

            return ['data' => ['tracking' => $tracking->toArray()]];
        } catch (AfterShipError $e) {
            throw ExceptionMapper::fromSdkException($e);
        }
    }

    public function getTracking(string $id): array
    {
        try {
            $response = $this->client->tracking->getTrackingById($id);
            $tracking = $response->getData();

            return ['data' => ['tracking' => $tracking->toArray()]];
        } catch (AfterShipError $e) {
            throw ExceptionMapper::fromSdkException($e);
        }
    }

    public function listTrackings(array $query = []): array
    {
        try {
            $sdkQuery = null;
            if (!empty($query)) {
                $sdkQuery = new GetTrackingsQuery();
                foreach ($query as $key => $value) {
                    $setter = 'set' . str_replace('_', '', ucwords($key, '_'));
                    if (method_exists($sdkQuery, $setter)) {
                        $sdkQuery->{$setter}((string) $value);
                    }
                }
            }

            $response = $this->client->tracking->getTrackings($sdkQuery);
            $data = $response->getData();

            $trackings = [];
            if ($data->trackings !== null) {
                foreach ($data->trackings as $tracking) {
                    $trackings[] = $tracking->toArray();
                }
            }

            $result = [
                'trackings' => $trackings,
                'count' => $data->pagination?->total ?? count($trackings),
            ];

            return ['data' => $result];
        } catch (AfterShipError $e) {
            throw ExceptionMapper::fromSdkException($e);
        }
    }

    public function updateTracking(string $id, array $data): array
    {
        try {
            $request = UpdateTrackingByIdRequest::fromArray($data, UpdateTrackingByIdRequest::class);
            $response = $this->client->tracking->updateTrackingById($id, $request);
            $tracking = $response->getData();

            return ['data' => ['tracking' => $tracking->toArray()]];
        } catch (AfterShipError $e) {
            throw ExceptionMapper::fromSdkException($e);
        }
    }

    public function deleteTracking(string $id): array
    {
        try {
            $response = $this->client->tracking->deleteTrackingById($id);
            $tracking = $response->getData();

            return ['data' => ['tracking' => $tracking->toArray()]];
        } catch (AfterShipError $e) {
            throw ExceptionMapper::fromSdkException($e);
        }
    }

    public function markTrackingCompleted(string $id, string $reason = 'DELIVERED'): array
    {
        try {
            $request = new MarkTrackingCompletedByIdRequest();
            $request->reason = MarkTrackingCompletedByIdRequestReason::from($reason);

            $response = $this->client->tracking->markTrackingCompletedById($id, $request);
            $tracking = $response->getData();

            return ['data' => ['tracking' => $tracking->toArray()]];
        } catch (AfterShipError $e) {
            throw ExceptionMapper::fromSdkException($e);
        }
    }

    public function listCouriers(): array
    {
        try {
            $response = $this->client->courier->getCouriers();
            $data = $response->getData();

            $couriers = [];
            if ($data->couriers !== null) {
                foreach ($data->couriers as $courier) {
                    $couriers[] = $courier->toArray();
                }
            }

            return ['data' => ['couriers' => $couriers]];
        } catch (AfterShipError $e) {
            throw ExceptionMapper::fromSdkException($e);
        }
    }

    public function detectCourier(array $data): array
    {
        try {
            $request = DetectCourierRequest::fromArray($data, DetectCourierRequest::class);
            $response = $this->client->courier->detectCourier($request);
            $data = $response->getData();

            $couriers = [];
            if ($data->couriers !== null) {
                foreach ($data->couriers as $courier) {
                    $couriers[] = $courier->toArray();
                }
            }

            return ['data' => ['couriers' => $couriers]];
        } catch (AfterShipError $e) {
            throw ExceptionMapper::fromSdkException($e);
        }
    }

    public function getCourier(string $slug): array
    {
        try {
            $query = new GetCouriersQuery();
            $query->setSlug($slug);

            $response = $this->client->courier->getCouriers($query);
            $data = $response->getData();

            $courierData = [];
            if ($data->couriers !== null && count($data->couriers) > 0) {
                $courierData = $data->couriers[0]->toArray();
            }

            return ['data' => ['courier' => $courierData]];
        } catch (AfterShipError $e) {
            throw ExceptionMapper::fromSdkException($e);
        }
    }

    public function estimateDeliveryDate(array $data): array
    {
        try {
            $request = EstimatedDeliveryDateRequest::fromArray($data, EstimatedDeliveryDateRequest::class);
            $response = $this->client->estimated_delivery_date->predict($request);
            $estimate = $response->getData();

            return ['data' => ['estimated_delivery_date' => $estimate->toArray()]];
        } catch (AfterShipError $e) {
            throw ExceptionMapper::fromSdkException($e);
        }
    }
}
