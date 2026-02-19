<?php

namespace App\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS)]
class Ltv extends Constraint
{
    public function getTargets(): string|array
    {
        return [self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT];
    }

    public function validatedBy(): string
    {
        return LtvValidator::class;
    }
}
