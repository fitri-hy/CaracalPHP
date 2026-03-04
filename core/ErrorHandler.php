<?php
namespace Caracal\Core;

use Throwable;
use ReflectionClass;

class ErrorHandler
{
    public static function handle(Throwable $e, ?string $controller = null, ?string $method = null): Response
    {
        $app = Application::getInstance();
        $debug = $app->config()->get('app_debug', false);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $errorFile = $e->getFile();
        $errorLine = $e->getLine();
        $errorMessage = htmlspecialchars($e->getMessage());
        $errorType = get_class($e);

        if ($controller && $method) {
            try {
                $ref = new ReflectionClass($controller);
                if ($ref->hasMethod($method)) {
                    $refMethod = $ref->getMethod($method);
                    $errorFile = $refMethod->getFileName();
                    $errorLine = $refMethod->getStartLine();
                }
            } catch (\ReflectionException) {}
        }

        $codeSnippet = '';
        if (is_file($errorFile) && $debug) {
            $lines = file($errorFile);
            $start = max(0, $errorLine - 6);
            $end   = min(count($lines), $errorLine + 5);
            $codeSnippet .= '<pre>';
            for ($i = $start; $i < $end; $i++) {
                $lineNumber = $i + 1;
                $lineContent = htmlspecialchars($lines[$i]);
                $highlight = ($lineNumber === $errorLine) ? ' style="background:#f44336;color:#fff;"' : '';
                $codeSnippet .= "<span{$highlight}>$lineNumber: $lineContent</span>";
            }
            $codeSnippet .= '</pre>';
        }

        $noteHtml = '';
        if ($controller && $method && !method_exists($controller, $method)) {
            $noteHtml = <<<HTML
<div class="card">
    <div><strong>Note:</strong> Method <code>{$controller}::{$method}()</code> does not exist.</div>
</div>
HTML;
        }

        $asset = new Asset();
        $html = <<<HTML
<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Application Error</title>
<link rel="shortcut icon" href="{$asset->url('images/favicon.ico')}">
<style>
body { font-family: 'Segoe UI', sans-serif; background: #1e1e1e; color: #dcdcdc; padding: 2rem; }
h1 { color: #f44336; font-size: 2rem; margin-bottom: 1rem; }
.card { background: #2d2d2d; padding: 1rem 1.5rem; border-radius: 6px; margin-bottom: 1rem; }
.card strong { color: #ff7961; }
pre { background: #252526; color: #dcdcdc; padding: 1rem; border-radius: 6px; overflow-x: auto; font-size: 0.9rem; }
.file-info { font-size: 0.9rem; color: #999; margin-top: 0.5rem; }
</style>
</head>
<body>
<h1>Application Error</h1>
<div class="card">
    <div><strong>{$errorType}</strong>: {$errorMessage}</div>
    <div class="file-info">File: {$errorFile} | Line: {$errorLine}</div>
</div>
{$noteHtml}
HTML;

        if ($codeSnippet) {
            $html .= <<<HTML
<div class="card">
    <h2>Snippet</h2>
    {$codeSnippet}
</div>
HTML;
        }

        return new Response($html, 500);
    }

    public static function notFound(): Response
    {
        $app = Application::getInstance();

        $custom404 = $app->path('app/Modules/Error/Views/404.view.php');
        if (is_file($custom404)) {
            $content = file_get_contents($custom404);
            return new Response($content, 404);
        }
		
		$asset = new Asset();
        $html = <<<HTML
<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 Not Found</title>
<link rel="shortcut icon" href="{$asset->url('images/favicon.ico')}">
</head>
<body style="font-family: 'Segoe UI', sans-serif; background:#1e1e1e; color:#dcdcdc; margin:0; display:flex; justify-content:center; align-items:center; height:100vh; text-align:center;">
<div style="display:inline-block; background:#2d2d2d; padding:40px 60px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.3);">
    <img src="{$asset->url('images/logo.png')}" alt="Logo" style="height:200px; width:200px; object-fit:cover; margin-bottom:20px;">
<h1 style="font-size:80px; color:#f44336; margin:0 0 15px 0;">404</h1>
    <p style="font-size:20px; margin:10px 0;">Oops! The page you are looking for does not exist.</p>
    <p><a href="/" style="color:#61dafb; text-decoration:none; font-weight:bold;">Go Home</a></p>
</div>
</body>
</html>
HTML;
        return new Response($html, 404);
    }
}