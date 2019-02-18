<?php
/**
 * Roting middleware for the Carbon WP theme
 *
 * This file will load the correct frontend controller to display.
 *
 * @package CarbonWP
 * @author Ramy Deeb <me@ramydeeb.com>
 */
namespace Carbon\Middlewares;

use Carbon\Exceptions\FileNotFoundException;

class Routing
{
    protected $rules;
    protected $matchedOptions;

    public function __construct( array $rules = [] )
    {
        $this->rules = $rules;
        add_action( 'do_parse_request', [ $this, 'match' ], 30, 2 );
        add_action( 'carbon_routing_matched', [ $this, 'removeQueryRouting' ], 30 );
    }

    public function match( $do_parse, $wp )
    {
        $allowed = !is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX );
        if ( ! $allowed ) return $do_parse;

        $current_url = $this->getCurrentUrl();

        $routes = apply_filters( 'carbon_add_route', $this->rules, $current_url );

        if (empty( $routes ) || !is_array( $routes ) ) {
            return $do_parse;
        }

        $urlParts = explode( '?', $current_url, 2 );
        $urlPath = trim( $urlParts[0], '/' );
        $urlVars = [];

        if ( isset( $urlParts[1] ) ) {
            parse_str( $urlParts[1], $urlVars );
        }

        $query_vars = null;
        $is_matched = false;

        // We are on the homepage
        if ( empty( $urlPath ) && isset( $routes[ '/' ] ) ) {
            $options = $routes[ '/' ];
            $routeVars = $this->parseMatch( $options, [] );
            if ( is_array( $routeVars ) ) {
                $query_vars = array_merge( $routeVars, $urlVars );
            }
            $is_matched = $options;
        } else {
            // Not home so do some searching
            foreach( $routes as $pattern => $options ) {
                $pattern = trim( $pattern, '/' );

                if ( ! empty( $pattern ) && preg_match( '~' . $pattern . '~', $urlPath, $matches ) ) {
                    $routeVars = $this->parseMatch( $options, $matches );
                    if ( is_array( $routeVars ) ) {
                        $query_vars = array_merge( $routeVars, $urlVars );
                    }
                    $is_matched = $options;
                    break;
                }
            }
        }

        if ( $is_matched !== false ) {
            if ( is_array( $query_vars ) ) {
                $wp->query_vars = $query_vars;
                do_action( 'carbon_routing_matched', $query_vars );
            }
            $this->matchedOptions = $is_matched;
            add_action( 'wp', [ $this, 'executeControllerAction' ] );
            return false;
        }

        return $do_parse;
    }

    public function removeQueryRouting() {
        remove_action( 'template_redirect', 'redirect_canonical' );
    }

    private function getCurrentUrl() {
        $current_url = trim( esc_url_raw( add_query_arg( [] ) ), '/' );
        $home_path = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );
        if ( $home_path && strpos( $current_url, $home_path ) === 0 ) {
            $current_url = trim( substr( $current_url, strlen( $home_path ) ), '/' );
        }
        return $current_url;
    }

    private function parseMatch( $options, $matches ) {
        if ( ! isset( $options[ 'query' ] ) ) return false;

        $query = $options[ 'query' ];
        // Translate %%matches_i%% to $matches[$i]
        foreach ($query as $key => $value) {
            $query[ $key ] = $this->translateMatches( $value, $matches );
        }
        return $query;
    }

    private function translateMatches( $value, $matches ) {
        if ( strpos( $value, '%%matches_' ) !== false ) {
            $value_to_match = trim( $value, '%%' );
            $parts = explode( '_', $value_to_match, 2 );
            $value = $matches[ $parts[1] ];
        }
        return $value;
    }

    private function includeController( $controller ) {
        $paths = [];
        $paths[] = get_template_directory() . '/../app';
        if ( is_child_theme() ) {
            $paths[] = get_stylesheet_directory() . '/app';
        }

        foreach ( $paths as $path ) {
            if (file_exists( "$path/$controller.php" )) {
                return require_once "$path/$controller.php";
            }
        }

        throw new FileNotFoundException( "$controller.php" );
    }

    public function executeControllerAction( $wp ) {
        $className = $this->matchedOptions[ 'controller' ];
        $actionName = $this->matchedOptions[ 'action' ] . 'Action';

        $this->includeController( $className );

        $controller = new $className( $wp );
        call_user_func( [ $controller, $actionName ] );
    }
}
