<?php

use WPGraphQL\Data\DataSource;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;

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
    public static function register_connection( $settings ) {
      
      $from_post_type = $settings['from']['field']['post_type'];
      $from_post_object = get_post_type_object( $from_post_type );
      $from_connection_name = $settings['from']['graphql_name'];
      $to_post_type = $settings['to']['field']['post_type'];
      $to_post_object = get_post_type_object( $to_post_type );
      $to_connection_name = $settings['to']['graphql_name'];
     
      if ( $settings['from']['show_in_graphql'] ) {
        if ( $from_post_object->show_in_graphql ) {
  
          register_graphql_connection(
            [
              'fromType'      => $from_post_object->graphql_single_name,
              'toType'        => $to_post_object->graphql_single_name,
              'fromFieldName' => $from_connection_name,
              'resolveNode'   => function( $id, $args, $context, $info ) {
                return ! empty( $id ) ? DataSource::resolve_post_object( $id, $context ) : null;
              },
              'resolve'       => function ( $root, $args, $context, $info ) use ( $from_post_type, $settings ) {
                $resolver = new PostObjectConnectionResolver( $root, $args, $context, $info, $from_post_type );
                $resolver->setQueryArg( 'relationship' , [
                  'from'  => $root->ID,
                  'id'    => $settings['id'],
                ] );
                // Meta Box does not want post_parent set
                $resolver->setQueryArg( 'post_parent', null );
                
                return $resolver->get_connection();
              }
            ]
          );
        }
      }

      if ( $settings['to']['show_in_graphql'] ) {
        if ( $to_post_object->show_in_graphql ) {
          register_graphql_connection(
            [
              'fromType'      => $to_post_object->graphql_single_name,
              'toType'        => $from_post_object->graphql_single_name,
              'fromFieldName' => $to_connection_name,
              'resolveNode'   => function( $id, $args, $context, $info ) {
                return ! empty( $id ) ? DataSource::resolve_post_object( $id, $context ) : null;
              },
              'resolve'       => function ( $root, $args, $context, $info ) use ( $to_post_type, $settings ) {
                $resolver = new PostObjectConnectionResolver( $root, $args, $context, $info, $to_post_type );
                $resolver->setQueryArg( 'relationship' , [
                  'to' => $root->ID,
                  'id' => $settings['id'],
                ] );
                // Meta Box does not want post_parent set
                $resolver->setQueryArg( 'post_parent', null );
                
                return $resolver->get_connection();
              }
            ]
          );
        }
      }
    }

    /**
     * Initialise plugin.
     *
     * @access private
     * @since  0.0.1
     * @return void
     */
    private function init() {

      add_action( 'mb_relationships_registered', [ 'WPGraphQL_MB_Relationships', 'register_connection' ], 1 );

    }

}

add_action( 'mb_relationships_init', 'WPGraphQL_MB_Relationships_init' );

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
