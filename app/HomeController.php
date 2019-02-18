<?php

use Carbon\Controller\BaseController;

class HomeController extends BaseController {
    public function indexAction() {
        global $wp_the_query;
        $this->setTitle( "Welcome to Carbon" );
        $this->addStyle( "Font Awesome", "https://use.fontawesome.com/releases/v5.7.2/css/all.css" );

        $data = [];
        $data[ 'posts' ] = $wp_the_query->get_posts();

        $this->render( 'home.twig' );
    }
}
