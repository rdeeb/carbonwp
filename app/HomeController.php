<?php

use Carbon\Controller\BaseController;

class HomeController extends BaseController {
    public function indexAction() {
        $this->setTitle( "Welcome to Carbon" );
        $this->enqueueStyle( "Font Awesome", "https://use.fontawesome.com/releases/v5.7.2/css/all.css" );
        $this->render( 'home.twig' );
    }
}
