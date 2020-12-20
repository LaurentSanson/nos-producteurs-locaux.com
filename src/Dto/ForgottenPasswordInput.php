<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\EmailExists;

/**
 * Class ForgottenPasswordInput
 * @package App\Dto
 */
class ForgottenPasswordInput
{
    /**
     * @Assert\Email
     * @Assert\NotBlank
     * @EmailExists
     */
    private string $email = "";

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
