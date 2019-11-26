<?php

use WPGraphQL\Data\DataSource;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;
use WPGraphQL\Connection\PostObjects;
use WPGraphQL\Type\WPEnumType;

final class WPGraphQL_MB_Relationships {

    /**
     * Stores the instance of the WPGraphQL_MB_Relationships class
     *
     * @var WPGraphQL_MB_Relationships The one true WPGraphQL_MB_Relationships
     * @since  0.0.1
     * @access private
     */
    private static $instance;

    /**
     * The instance of the WPGraphQL_MB_Relationships object
     *
     * @return object|WPGraphQL_MB_Relationships - The one true WPGraphQL_MB_Relationships
     * @since  0.0.1
     * @access public
     */
    public static function instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPGraphQL_MB_Relationships ) ) {
            self::$instance = new WPGraphQL_MB_Relationships();
            self::$instance->init();
        }

        /**
         * Return the WPGraphQL_MB_Relationships Instance
         */
        return self::$instance;
    }

    /**
     * Throw error on object clone.
     * The whole idea of the singleton design pattern is that there is a single object
     * therefore, we don't want the object to be cloned.
     *
     * @since  0.0.1
     * @access public
     * @return void
     */
    public function __clone() {

        // Cloning instances of the class is forbidden.
        _doing_it_wrong( __FUNCTION__, esc_html__( 'The WPGraphQL_MB_Relationships class should not be cloned.', 'wpgraphiql-mb-relationships' ), '0.0.1' );

    }

    /**
     * Disable unserializing of the class.
     *
     * @since  0.0.1
     * @access protected
     * @return void
     */
    public function __wakeup() {

        // De-serializing instances of the class is forbidden.
        _doing_it_wrong( __FUNCTION__, esc_html__( 'De-serializing instances of the WPGraphQL_MB_Relationships class is not allowed', 'wpgraphiql-mb-relationships' ), '0.0.1' );

    }

    /**
     * Register WPGraphQL MB Relationships config.
     *
     * @access public
     * @since  0.0.1
     * @return void
     */
    public static function register( $settings ) {
      // register relationship with MB Relationships
      MB_Relationships_API::register( $settings );

      // register graphql connection
      // TODO register_graphql_connection
    }

    /**
     * Initialise plugin.
     *
     * @access private
     * @since  0.0.1
     * @return void
     */
    private function init() {
        $this->register_output_types();
    }

}

add_action( 'init', 'WPGraphQL_MB_Relationships_init' );

if ( ! function_exists( 'WPGraphQL_MB_Relationships_init' ) ) {
    /**
     * Function that instantiates the plugins main class
     *
     * @since 0.0.1
     */
    function WPGraphQL_MB_Relationships_init() {

        /**
         * Return an instance of the action
         */
        return \WPGraphQL_MB_Relationships::instance();
    }
}

/**
 * Register a WPGraphQL aware MB Relationship
 *
 * @param array $settings Relationship parameters.
 */
function register_graphql_mb_relationship_type( $settings ) {
    \WPGraphQL_MB_Relationships::register( $settings );
}
