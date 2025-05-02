<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RegistersAdminPage
{
    public function __construct()
    {
    }
}
