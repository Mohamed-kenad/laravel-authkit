<?php

declare(strict_types=1);

namespace Kenad\AuthKit\DTOs;

final readonly class RegisterData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    /**
     * Build from a validated array (e.g. from a FormRequest).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
        );
    }
}
