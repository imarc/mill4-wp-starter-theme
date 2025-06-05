<?php

namespace App\Hooks;

use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;

class SessionHooks implements HooksInterface
{
    use RegistersHooks;

    public function initialize(): void
    {
        if (config('sessions.enabled')) {
            session_name(config('sessions.cookie'));
            session_set_cookie_params(
                config('sessions.lifetime'),
                config('sessions.path'),
                config('sessions.domain'),
                config('sessions.secure'),
                config('sessions.httponly')
            );
            session_start();
        }
    }
}
