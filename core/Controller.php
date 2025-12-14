<?php
namespace Core;

class Controller {
    public function view($view, $data = []) {
        extract($data);
        require __DIR__ . '/../app/views/' . $view . '.php';
    }
}
