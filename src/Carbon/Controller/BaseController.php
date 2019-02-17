<?php

namespace Carbon\Controller;

use Carbon\Exceptions\TemplateNotFoundException;
use Timber\Timber;
use Timber\Loader;
use Timber\LocationManager;


abstract class BaseController
{
    protected $title;
    protected $scripts  = [];
    protected $styles   = [];

    /**
     * BaseController constructor.
     *
     * Initializes variables and required hooks
     */
    public function __construct() {
        // Actions
        add_action( 'wp_enqueue_scripts', [ $this, 'processScripts' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'processStyles' ] );
        // Filters
        add_filter( 'document_title_parts', [ $this, 'titleFilter' ], 10, 2 );
    }

    /**
     * Returns the current page title
     * @return string|null
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the title for the page
     * @param $title
     */
    public function setTitle( $title ) {
        $this->title = $title;
    }

    /**
     * Overrides the title parts with the one set on the controller
     *
     * @param array $titleParts an array of the current title parts
     *
     * @return array
     */
    public function titleFilter( $titleParts ) {
        if ( ! empty( $this->title ) ) {
            return [
                $this->title
            ];
        }
        return $titleParts;
    }

    /**
     * Enqueues a script for the current page
     *
     * @param string $name The identifier of this script
     * @param string $src The path to this script
     * @param bool $footer Should be added at the bottom of the page?
     * @param array $deps Dependencies for this scripts
     * @param string|null $ver The script version
     */
    protected function enqueueScript( $name, $src, $footer = true, $deps = [], $ver = null ) {
        $this->scripts[ $name ] = [
            'src'       => $src,
            'footer'    => $footer,
            'deps'      => $deps,
            'ver'       => $ver
        ];
    }

    /**
     * Enqueues a stylesheet for the current page
     *
     * @param string $name The identifier of this script
     * @param string $src The path to this script
     * @param string $media The target media of this stylesheet
     * @param array $deps Dependencies for this scripts
     * @param string|null $ver The script version
     */
    protected function enqueueStyle( $name, $src, $media = 'all', $deps = [], $ver = null ) {
        $this->styles[ $name ] = [
            'src'   => $src,
            'media' => $media,
            'deps'  => $deps,
            'ver'   => $ver
        ];
    }

    /**
     * This functions adds all the enqueued scripts to the head
     */
    public function processScripts() {
        if ( empty( $this->scripts ) ) return;

        foreach ($this->scripts as $handle => $options) {
            wp_enqueue_script( $handle, $options[ 'src' ], $options[ 'deps' ], $options[ 'ver' ], $options[ 'footer' ] );
        }
    }

    /**
     * This functions adds all the enqueued styles to the head
     */
    public function processStyles() {
        if ( empty( $this->styles ) ) return;

        foreach ($this->styles as $handle => $options) {
            wp_enqueue_style( $handle, $options[ 'src' ], $options[ 'deps' ], $options[ 'ver' ], $options[ 'media' ] );
        }
    }

    /**
     * Renders a template file with an specific set of data
     *
     * @param $filenames
     * @param array $context
     * @param bool $expires
     * @param string $cache_mode
     *
     * @throws TemplateNotFoundException
     */
    protected function render( $filenames, $context = [], $expires = false, $cache_mode = Loader::CACHE_USE_DEFAULT ) {
        if ( ! $this->templateExists( $filenames ) ) {
            throw new TemplateNotFoundException( $filenames );
        }

        $default_context = Timber::get_context();
        $final_context = array_merge( $default_context, $context );
        Timber::render( $filenames, $final_context, $expires, $cache_mode );
    }

    /**
     * Verifies if a set of filenames exists
     * @param array|string $filenames an array or string of templates to find
     *
     * @return bool
     */
    private function templateExists( $filenames ) {
        $caller = LocationManager::get_calling_script_dir( 1 );
        $loader = new Loader( $caller );
        $file = $loader->choose_template( $filenames );
        return $file !== false;
    }
}
