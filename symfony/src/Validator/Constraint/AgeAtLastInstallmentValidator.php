<?php

namespace App\Validator\Constraint;

use App\Service\Mortgage\MortgageValidationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AgeAtLastInstallmentValidator extends ConstraintValidator
{
    public function __construct(
        private readonly MortgageValidationService $mortgageValidationService
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof AgeAtLastInstallment) {
            throw new UnexpectedTypeException($constraint, AgeAtLastInstallment::class);
        }

        $object = $this->context->getObject();
        if (null === $object->age || null === $object->period) {
            return;
        }

        $message = $this->mortgageValidationService->validateAgeAtLastInstallment(
            (int) $object->age,
            (int) $object->period
        );

        if (null !== $message) {
            $this->context->buildViolation($message)
                ->atPath('')
                ->addViolation();
        }
    }
}
