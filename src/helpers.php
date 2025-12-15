<?php

/**
 * URL Helper Functions
 * These functions help generate URLs throughout the application
 */

/**
 * Generate a URL for a given path
 */
function url(string $path = ''): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Auto-detect base path from script name
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $base = $scriptName !== '/' ? $scriptName : '';
    
    $path = ltrim($path, '/');
    $url = $protocol . '://' . $host . $base;
    
    if ($path) {
        $url .= '/' . $path;
    }
    
    return $url;
}

/**
 * Generate an asset URL
 */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    return url('public/assets/' . $path);
}

/**
 * Redirect to a URL
 */
function redirect(string $path, int $statusCode = 302): void
{
    header('Location: ' . url($path), true, $statusCode);
    exit;
}

/**
 * Redirect back to previous page
 */
function back(): void
{
    $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
    header('Location: ' . $referer);
    exit;
}

/**
 * Get current URL
 */
function current_url(): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Check if current route matches
 */
function is_route(string $path): bool
{
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Auto-detect base path
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $base = $scriptName !== '/' ? $scriptName : '';
    
    if ($base && strpos($currentPath, $base) === 0) {
        $currentPath = substr($currentPath, strlen($base));
    }
    
    $currentPath = rtrim($currentPath, '/') ?: '/';
    $path = rtrim($path, '/') ?: '/';
    
    return $currentPath === $path;
}

/**
 * Generate route with parameters
 */
function route(string $name, array $params = []): string
{
    // Simple route generation - can be extended with named routes
    $path = $name;
    
    foreach ($params as $key => $value) {
        $path = str_replace('{' . $key . '}', $value, $path);
    }
    
    return url($path);
}
