<?php

namespace App\Handler;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface HandlerInterface
 * @package App\Handler
 */
interface HandlerInterface
{
    /**
     * @param Request $request
     * @param mixed|null $data
     * @param array $options
     * @return bool
     */
    public function handle(Request $request, $data = null, array $options = []): bool;
}
