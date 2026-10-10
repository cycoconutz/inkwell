<?php

declare(strict_types=1);

namespace App\Core;

use App\Support\Env;
use Throwable;

/**
 * Application entry point: load config, start the session, register routes,
 * dispatch, and render a friendly error page when nothing matches or something
 * throws.
 */
final class App
{
    private Router $router;

    public function __construct(private readonly string $basePath)
    {
        $this->router = new Router();
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function bootstrap(): void
    {
        Env::load($this->basePath . '/.env');

        /** @var array<string, mixed> $config */
        $config = require $this->basePath . '/config/config.php';
        Config::load($config);

        date_default_timezone_set('UTC');

        $debug = (bool) Config::get('app.debug', false);
        ini_set('display_errors', $debug ? '1' : '0');
        error_reporting($debug ? E_ALL : 0);

        Session::start((string) Config::get('session.name', 'inkwell_session'));

        /** @var array<string, mixed> $db */
        $db = Config::get('db', []);
        Database::configure($db);
    }

    public function run(): void
    {
        $this->bootstrap();

        $register = require $this->basePath . '/config/routes.php';
        $register($this->router);

        $request = Request::fromGlobals();

        try {
            if (!$this->router->dispatch($request)) {
                $this->render404($request);
            }
        } catch (Throwable $e) {
            $this->render500($e);
        } finally {
            // Old form input is a one-request affordance; drop it once rendered.
            Session::remove('_old');
        }
    }

    private function render404(Request $request): void
    {
        http_response_code(404);
        View::render('errors/404', ['request' => $request], 'layouts/app');
    }

    private function render500(Throwable $e): void
    {
        http_response_code(500);

        if (Config::get('app.debug')) {
            echo '<pre style="padding:1rem;font:14px/1.5 monospace;color:#b00020;white-space:pre-wrap">'
                . e($e->getMessage()) . "\n\n" . e($e->getTraceAsString())
                . '</pre>';
            return;
        }

        View::render('errors/500', [], 'layouts/app');
    }
}
