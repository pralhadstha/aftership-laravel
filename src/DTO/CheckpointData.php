<?php

declare(strict_types=1);

namespace AfterShip\DTO;

final class CheckpointData
{
    public function __construct(
        public readonly string $tag,
        public readonly string $subtag,
        public readonly ?string $message = null,
        public readonly ?string $location = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $zip = null,
        public readonly ?string $countryIso3 = null,
        public readonly ?string $checkpointTime = null,
        public readonly ?string $slug = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            tag: $data['tag'] ?? '',
            subtag: $data['subtag'] ?? '',
            message: $data['message'] ?? null,
            location: $data['location'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            zip: $data['zip'] ?? null,
            countryIso3: $data['country_iso3'] ?? null,
            checkpointTime: $data['checkpoint_time'] ?? null,
            slug: $data['slug'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'tag' => $this->tag,
            'subtag' => $this->subtag,
            'message' => $this->message,
            'location' => $this->location,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country_iso3' => $this->countryIso3,
            'checkpoint_time' => $this->checkpointTime,
            'slug' => $this->slug,
        ];
    }
}
