<?php

namespace App\Validator\Constraint;

use App\Service\Mortgage\MortgageValidationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LtvValidator extends ConstraintValidator
{
    public function __construct(
        private readonly MortgageValidationService $mortgageValidationService
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Ltv) {
            throw new UnexpectedTypeException($constraint, Ltv::class);
        }

        $object = $this->context->getObject();
        if ($object->creditValue === null || $object->secureValue === null) {
            return;
        }

        $message = $this->mortgageValidationService->validateLtv(
            (int) $object->creditValue,
            (int) $object->secureValue
        );

        if ($message !== null) {
            $this->context->buildViolation($message)
                ->atPath('')
                ->addViolation();
        }
    }
}
