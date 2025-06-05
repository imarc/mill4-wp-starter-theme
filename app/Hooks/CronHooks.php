<?php

namespace App\Hooks;

use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;

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
        // $this->scheduleJob(MyGreatJob::class, 'hourly', null, ['bar']);

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
        $cronEvent = $hook . '_' . $recurrence;
        $this->validateRecurrence($recurrence);

        if (! wp_next_scheduled($cronEvent)) {
            $this->validateRecurrence($recurrence);
            wp_schedule_event($timestamp, $recurrence, $cronEvent);
        }

        $this->addAction($cronEvent, $callback);
    }

    protected function scheduleJob(string $jobClass, string $recurrence, ?int $timestamp = null, array $args = [])
    {
        $name = (new $jobClass())->getName();

        $this->schedule($name, $recurrence, function () use ($jobClass, $args) {
            $jobClass::dispatch(...$args)
                ->now()
                ->execute(false);
        }, $timestamp);
    }

    private function getSchedules(): array
    {
        $this->schedules = is_null($this->schedules) ? wp_get_schedules() : $this->schedules;

        return $this->schedules;
    }

    private function validateRecurrence($recurrence)
    {
        $schedules = $this->getSchedules();
        print_r($schedules);
        exit();

        if (! isset($schedules[$recurrence])) {
            error_log('CronHooks: Invalid recurrence: ' . $recurrence);
        }
    }

}
