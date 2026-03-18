<?php

declare(strict_types=1);

namespace AfterShip\DTO;

final class CourierData
{
    /**
     * @param array<int, string> $requiredFields
     * @param array<int, string> $optionalFields
     */
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly ?string $phone = null,
        public readonly ?string $otherName = null,
        public readonly ?string $webUrl = null,
        public readonly array $requiredFields = [],
        public readonly array $optionalFields = [],
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            slug: $data['slug'] ?? '',
            name: $data['name'] ?? '',
            phone: $data['phone'] ?? null,
            otherName: $data['other_name'] ?? null,
            webUrl: $data['web_url'] ?? null,
            requiredFields: $data['required_fields'] ?? [],
            optionalFields: $data['optional_fields'] ?? [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'phone' => $this->phone,
            'other_name' => $this->otherName,
            'web_url' => $this->webUrl,
            'required_fields' => $this->requiredFields,
            'optional_fields' => $this->optionalFields,
        ];
    }
}
