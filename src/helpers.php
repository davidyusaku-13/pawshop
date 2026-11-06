<?php

if (!function_exists('format_rupiah')) {
    function format_rupiah(float|int|null $amount): string
    {
        return 'Rp ' . number_format((float)($amount ?? 0), 0, ',', '.');
    }
}

if (!function_exists('flash')) {
    function flash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_messages'][] = [
            'type'    => $type, // 'success', 'danger', 'warning', 'info'
            'message' => $message,
        ];
    }
}

if (!function_exists('render_flash')) {
    function render_flash(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['flash_messages'])) {
            return '';
        }

        $html = '';
        foreach ($_SESSION['flash_messages'] as $msg) {
            $type = htmlspecialchars($msg['type']);
            $text = htmlspecialchars($msg['message']);
            $html .= "<div class=\"alert alert-{$type} alert-dismissible fade show my-3\" role=\"alert\">";
            $html .= $text;
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            $html .= "</div>";
        }

        unset($_SESSION['flash_messages']);
        return $html;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        $target = str_starts_with($path, 'http') ? $path : BASE_URL . ltrim($path, '/');
        if (defined('PHPUNIT_RUNNING')) {
            throw new \RuntimeException("Redirected to: {$target}");
        }
        header("Location: {$target}");
        exit();
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return BASE_URL . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return BASE_URL . 'assets/' . ltrim($path, '/');
    }
}

if (!function_exists('sanitize')) {
    function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
