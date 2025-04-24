<?php

namespace App\AdminPages;

class LogViewer extends AdminPage
{
    protected string $slug = 'logs';

    protected string $title = 'Log Viewer';

    protected string $capability = 'manage_options';

    protected int $menuPosition = 10;

    protected string $icon = 'dashicons-admin-tools';

    protected ?string $template = 'admin/log-viewer.twig';

    protected string $parentSlug = 'options-general.php';

    protected function withContext(): array
    {
        return [
            'logs' => 'foo',
        ];
    }
}
