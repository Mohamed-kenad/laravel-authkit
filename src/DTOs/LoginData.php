<?php

declare(strict_types=1);

namespace Kenad\AuthKit\DTOs;

final readonly class LoginData
{
    public function __construct(
        public string $email,
        public string $password,
        public ?array $abilities = null,
    ) {}

    /**
     * Build from a validated array (e.g. from a FormRequest).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            abilities: $data['abilities'] ?? null,
        );
    }
}
