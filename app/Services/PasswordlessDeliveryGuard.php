<?php

namespace App\Services;

use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class PasswordlessDeliveryGuard
{
    public function ensureReady(): void
    {
        if (app()->environment('production') && config('mail.default') === 'log') {
            throw new ServiceUnavailableHttpException(null, 'Passwordless email delivery is not configured.');
        }
    }
}
