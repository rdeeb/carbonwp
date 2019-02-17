<?php

namespace Carbon;

use Carbon\Utils\Singleton;

final class Kernel extends Singleton
{
    public function init() {
        add_action( 'wp', [ $this, 'setupRouteMiddleware' ] );
    }

    public function setupRouteMiddleware() {
        if (is_home()) {
            require_once get_stylesheet_directory() . '/../app/HomeController.php';
            $controller = new \HomeController();
            $controller->indexAction();
        }
    }
}
