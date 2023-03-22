<?php

namespace App\ApiRequest;

use Symfony\Component\Validator\Constraints as Assert;

class MortgageRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly ?int $creditValue = null,
        #[Assert\NotBlank]
        public readonly ?int $secureValue = null,
        #[Assert\NotBlank]
        public readonly ?float $provision = null,
        #[Assert\NotBlank]
        public readonly ?float $margin = null,
        #[Assert\NotBlank]
        public readonly ?int $period = null,
        #[Assert\NotBlank]
        public readonly ?int $age = null,
        public readonly ?string $email = null
    )
    {}
}