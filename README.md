# AfterShip Laravel SDK

A modern Laravel wrapper for the [AfterShip Tracking API](https://www.aftership.com/docs/tracking) with driver support, DTOs, and webhook handling.

## Requirements

- PHP >= 8.2
- Laravel 10, 11, or 12

## Installation

```bash
composer require pralhadstha/aftership-laravel
```

Then run the interactive installer:

```bash
php artisan aftership:install
```

The installer will guide you through:
- Selecting a driver (`sdk` or `http`)
- Installing the AfterShip SDK (if the `sdk` driver is selected)
- Configuring your API key
- Publishing the config file

### Manual Setup

If you prefer to configure manually, publish the config and set your `.env` values:

```bash
php artisan vendor:publish --tag=aftership-config
```

## Configuration

Set your API key in `.env`:

```env
AFTERSHIP_API_KEY=your-api-key
AFTERSHIP_DRIVER=sdk
```

Full configuration options in `config/aftership.php`:

```php
return [
    'api_key'        => env('AFTERSHIP_API_KEY', ''),
    'driver'         => env('AFTERSHIP_DRIVER', 'sdk'),   // sdk, http
    'base_url'       => env('AFTERSHIP_BASE_URL', 'https://api.aftership.com'),
    'timeout'        => env('AFTERSHIP_TIMEOUT', 30),
    'webhook_secret' => env('AFTERSHIP_WEBHOOK_SECRET', ''),
];
```

### Drivers

| Driver | Description | Extra dependency |
|--------|-------------|-----------------|
| `sdk`  | Wraps the official [AfterShip Tracking SDK](https://github.com/AfterShip/tracking-sdk-php) (default) | `aftership/tracking-sdk` |
| `http` | Uses Laravel's HTTP client (`Http::`) directly | None |

If you choose the `sdk` driver, install the official SDK:

```bash
composer require aftership/tracking-sdk
```

The `http` driver works out of the box with no extra dependencies.

## Usage

### Facade

```php
use AfterShip\Facades\AfterShip;

// Create a tracking
$tracking = AfterShip::tracking()->create([
    'tracking_number' => '1234567890',
    'slug' => 'dhl',
]);

// Get a tracking
$tracking = AfterShip::tracking()->get('tracking-id');

// List trackings
$collection = AfterShip::tracking()->list(['page' => 1, 'limit' => 10]);

// Update a tracking
$tracking = AfterShip::tracking()->update('tracking-id', [
    'title' => 'My Shipment',
]);

// Delete a tracking
AfterShip::tracking()->delete('tracking-id');

// Mark as completed
AfterShip::tracking()->markCompleted('tracking-id', 'DELIVERED');
```

### Couriers

```php
// List all couriers
$couriers = AfterShip::courier()->list();

// Detect courier
$detected = AfterShip::courier()->detect([
    'tracking_number' => '1234567890',
]);

// Get courier details
$courier = AfterShip::courier()->get('dhl');
```

### Delivery Estimates

```php
$estimate = AfterShip::deliveryEstimate()->estimate([
    'slug' => 'dhl',
    'service_type_name' => 'Express',
    'origin_address' => 'New York, USA',
    'destination_address' => 'London, UK',
]);
```

### Dependency Injection

```php
use AfterShip\Client\AfterShipClient;

class ShipmentController extends Controller
{
    public function __construct(
        private readonly AfterShipClient $aftership,
    ) {}

    public function show(string $id)
    {
        $tracking = $this->aftership->tracking()->get($id);

        return response()->json($tracking->toArray());
    }
}
```

### Webhooks

```php
use AfterShip\Webhooks\WebhookHandler;

class AfterShipWebhookController extends Controller
{
    public function __construct(
        private readonly WebhookHandler $webhook,
    ) {}

    public function handle(Request $request)
    {
        $payload = $this->webhook->handle(
            $request->getContent(),
            $request->header('aftership-hmac-sha256'),
        );

        $event = $this->webhook->getEventName($payload);
        $tracking = $this->webhook->getTrackingData($payload);

        // Handle events: tracking.updated, tracking.delivered, tracking.exception
        match ($event) {
            'tracking.delivered' => $this->handleDelivered($tracking),
            'tracking.exception' => $this->handleException($tracking),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }
}
```

### DTOs

All API responses are mapped to immutable Data Transfer Objects:

- `TrackingData` — tracking information with checkpoints
- `CourierData` — courier details
- `DeliveryEstimateData` — estimated delivery date
- `TrackingCollection` — paginated list of trackings
- `CheckpointData` — individual checkpoint

```php
$tracking = AfterShip::tracking()->get('id');

$tracking->trackingNumber;  // string
$tracking->slug;            // string
$tracking->tag;             // string (e.g., "InTransit", "Delivered")
$tracking->checkpoints;     // array<CheckpointData>
$tracking->toArray();       // array representation
```

### Error Handling

The package throws specific exceptions:

```php
use AfterShip\Exceptions\AuthenticationException;
use AfterShip\Exceptions\RateLimitException;
use AfterShip\Exceptions\ApiException;

try {
    $tracking = AfterShip::tracking()->get('id');
} catch (AuthenticationException $e) {
    // Invalid or missing API key (401)
} catch (RateLimitException $e) {
    // Rate limit exceeded (429)
} catch (ApiException $e) {
    // Other API errors
    $e->getStatusCode();
    $e->getErrorType();
    $e->getMessage();
}
```

### Testing

Use the `FakeDriver` for testing without making API calls:

```php
use AfterShip\AfterShipManager;
use AfterShip\Contracts\ClientInterface;

// In your test
$this->app['config']->set('aftership.driver', 'fake');

$client = $this->app->make(ClientInterface::class);
$tracking = $client->tracking()->create([
    'tracking_number' => 'TEST123',
    'slug' => 'dhl',
]);

// Assert on the FakeDriver
$fakeDriver = $client->driver();
$this->assertTrue($fakeDriver->assertCalled('createTracking'));
```

## Running Tests

```bash
composer install
vendor/bin/phpunit
```

## License

MIT
