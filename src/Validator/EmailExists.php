<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class EmailExists
 * @package App\Validator
 * @Annotation
 */
class EmailExists extends Constraint
{
    public string $message = "Cet email n'existe pas";
}
