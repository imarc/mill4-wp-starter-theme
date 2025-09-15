<?php

namespace App\Hooks;

use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;
use Imarc\Millyard\Services\Cron;

class CronHooks implements HooksInterface
{
    use RegistersHooks;

    private ?array $schedules = null;

    public function __construct(private Cron $cron)
    {
    }

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerCronJobs']);
    }

    public function registerCronJobs(): void
    {
        // $this->cron->scheduleJob(MyGreatJob::class, 'hourly', null, ['bar']);

        // $this->cron->schedule('mill4_my_job', 'hourly', function () {
        //     echo 'Hello, world!';
        // });
    }
}
