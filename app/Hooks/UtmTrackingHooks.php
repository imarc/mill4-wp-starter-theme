<?php

namespace App\Hooks;

use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;
use Imarc\Millyard\Services\UtmTracker;

class UtmTrackingHooks implements HooksInterface
{
    use RegistersHooks;

    public function __construct(
        private UtmTracker $utmTracker
    ) {
    }

    public function initialize(): void
    {
        // Hook into init to capture UTM parameters early in the request lifecycle
        $this->addAction('init', [$this, 'captureUtmParameters'], 1);
    }

    /**
     * Capture UTM parameters from the URL and store them
     */
    public function captureUtmParameters(): void
    {
        $this->utmTracker->capture();
    }
}
