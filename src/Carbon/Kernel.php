<?php

namespace Carbon;

use Carbon\Middlewares\Routing;
use Carbon\Utils\Singleton;

final class Kernel extends Singleton
{
    public function init() {
        add_action( 'init', [ $this, 'setupRouteMiddleware' ] );
    }

    public function setupRouteMiddleware() {
        $routing = new Routing();
    }
}
