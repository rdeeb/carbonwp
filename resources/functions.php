<?php
/**
 * Carbon WP | functions.php
 * --------------------------------------------------------------------------------------------------------------------
 * Functions required by the theme
 *
 * @package CarbonWP
 * @author Ramy Deeb <me@ramydeeb.com>
 * --------------------------------------------------------------------------------------------------------------------
 */

require_once dirname( __FILE__ ) . "/../vendor/autoload.php";
require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

// Actions -------------------------------------------------------------------------------------------------------------
add_action( 'tgmpa_register', 'carbonwp_register_required_plugins' );
add_action( 'after_setup_theme', 'carbonwp_theme_support' );

// Filters -------------------------------------------------------------------------------------------------------------

// Functions -----------------------------------------------------------------------------------------------------------
if ( ! function_exists( 'carbonwp_register_required_plugins' ) ) {
    function carbonwp_register_required_plugins() {
        $plugins = [
            [
                'name'      => 'Timber',
                'slug'      => 'timber-library',
                'required'  => true
            ],
            [
                'name'      => 'Timber with Jetpack Photon',
                'slug'      => 'timber-with-jetpack-photon',
                'required'  => false
            ],
            [
                'name'      => 'Timber Debug Bar',
                'slug'      => 'debug-bar-timber',
                'required'  => false
            ],
            [
                'name'      => 'ACF Timber Integration',
                'slug'      => 'acf-timber-integration',
                'required'  => false
            ]
        ];

        $config = [
            'id'           => 'carbonwp',
            'default_path' => '',
            'menu'         => 'carbonwp-install-plugins',
            'parent_slug'  => 'themes.php',
            'capability'   => 'edit_theme_options',
            'has_notices'  => true,
            'dismissable'  => !class_exists( 'Timber' ),
            'dismiss_msg'  => 'CarbonWP require som specific plugins to work correctly, please install them',
            'is_automatic' => true,
            'message'      => '',
            'strings'      => [
                'page_title'    => __( 'Install Required Plugins', 'carbonwp' ),
                'menu_title'    => __( 'Install Plugins', 'carbonwp' ),
                'nag_type'      => 'updated',
            ]
        ];

        tgmpa( $plugins, $config );
    }
}

if ( ! function_exists( 'carbonwp_theme_support' ) ) {
    function carbonwp_theme_support() {
        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );
        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support( 'title-tag' );
        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support( 'post-thumbnails' );
        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
            'html5', [
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ]
        );
        /*
         * Enable support for Post Formats.
         *
         * See: https://codex.wordpress.org/Post_Formats
         */
        add_theme_support(
            'post-formats', [
                'aside',
                'image',
                'video',
                'quote',
                'link',
                'gallery',
                'audio',
            ]
        );
        add_theme_support( 'menus' );
    }
}

// Misc. & Etc. --------------------------------------------------------------------------------------------------------

if ( class_exists( 'Timber' ) ) {
    Timber::$dirname = array( 'templates', 'views' );
}

# This line will start the Carbon Kernel
\Carbon\Kernel::instance()->init();
