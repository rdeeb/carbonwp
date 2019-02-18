<?php

use Carbon\Controller\BaseController;

class PostController extends BaseController {

    public function singleAction() {
        $data = [];
        $post = new Timber\Post();
        $data[ 'post' ] = $post;

        $this->setTitle( $post->title() . " | Welcome to Carbon" );
        $this->addStyle( "Font Awesome", "https://use.fontawesome.com/releases/v5.7.2/css/all.css" );

        $this->render( 'single.twig', $data );
    }

}
