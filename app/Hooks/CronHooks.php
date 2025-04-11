<?php

namespace App\Hooks;

use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;

class CronHooks implements HooksInterface
{
    use RegistersHooks;

    private ?array $schedules = null;

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerCronJobs']);
    }

    public function registerCronJobs(): void
    {
        // $this->schedule('mill4_my_job', 'hourly', function () {
        //     echo 'Hello, world!';
        // });
    }

    /**
     * Schedule an event
     */
    protected function schedule($hook, $recurrence, callable $callback, ?int $timestamp = null)
    {
        $timestamp = $timestamp ?: time();

        if (! wp_next_scheduled($hook)) {
            $this->validateRecurrence($recurrence);
            wp_schedule_event($timestamp, $recurrence, $hook);
        }

        add_action($hook, $callback);
    }

    private function getSchedules(): array
    {
        $this->schedules = is_null($this->schedules) ? wp_get_schedules() : $this->schedules;

        return $this->schedules;
    }

    private function validateRecurrence($recurrence)
    {
        $schedules = $this->getSchedules();

        if (! isset($schedules[$recurrence])) {
            error_log('CronHooks: Invalid recurrence: ' . $recurrence);
        }
    }

}
