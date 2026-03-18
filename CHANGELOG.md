# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-01-01

### Added

- Initial release
- TrackingService: create, get, list, update, delete, mark completed
- CourierService: list, detect, get courier details
- DeliveryEstimateService: estimated delivery date prediction
- SDK driver (Guzzle HTTP client)
- HTTP driver (Laravel HTTP client)
- Fake driver for testing
- Immutable DTOs: TrackingData, CourierData, DeliveryEstimateData, CheckpointData, TrackingCollection
- Webhook handling with HMAC signature verification
- Laravel service provider with auto-discovery
- AfterShip facade
- Publishable configuration
- Full test suite with Orchestra Testbench
