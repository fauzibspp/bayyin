<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/master'): void
    {
        $viewPath = dirname(__DIR__) . '/Views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            Response::abort(404);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutPath = dirname(__DIR__) . '/Views/' . $layout . '.php';

        if (!file_exists($layoutPath)) {
            Response::abort(404);
        }

        require $layoutPath;
    }
}