<?php
/**
 * Base Controller
 * Loads Models and Views
 */

class Controller
{
    public function view($view, $data = [])
    {
        // Extract data array into variables for view template
        extract($data);

        $viewFile = __DIR__ . '/../app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View <strong>" . htmlspecialchars($view) . "</strong> does not exist.");
        }
    }

    public function model($model)
    {
        $modelFile = __DIR__ . '/../app/models/' . $model . '.php';

        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die("Model <strong>" . htmlspecialchars($model) . "</strong> does not exist.");
        }
    }
}
