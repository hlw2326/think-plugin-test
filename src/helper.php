<?php

declare(strict_types=1);

if (!function_exists('base_admin_view')) {
    function base_admin_view(string $template): string
    {
        return app()->getBasePath() . 'admin' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR .
            str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $template) . '.html';
    }
}
