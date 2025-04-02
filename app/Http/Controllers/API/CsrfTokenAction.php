<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class CsrfTokenAction extends Controller
{
    public function __invoke()
    {
        return json_response([
            'csrf_token' => wp_create_nonce('ajax_nonce'),
        ]);
    }
}
