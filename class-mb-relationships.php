<?php

use WPGraphQL\Data\DataSource;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;

final class WPGraphQL_MB_Relationships
{

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
  public static function instance()
  {
    if (!isset(self::$instance) && !(self::$instance instanceof WPGraphQL_MB_Relationships)) {
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
  public function __clone()
  {
    // Cloning instances of the class is forbidden.
    _doing_it_wrong(__FUNCTION__, esc_html__('The WPGraphQL_MB_Relationships class should not be cloned.', 'wpgraphql-mb-relationships'), '0.0.1');
  }

  /**
   * Disable unserializing of the class.
   *
   * @since  0.0.1
   * @access protected
   * @return void
   */
  public function __wakeup()
  {
    // De-serializing instances of the class is forbidden.
    _doing_it_wrong(__FUNCTION__, esc_html__('De-serializing instances of the WPGraphQL_MB_Relationships class is not allowed', 'wpgraphql-mb-relationships'), '0.0.1');
  }

  /**
   * Register WPGraphQL MB Relationships config.
   *
   * @access public
   * @since  0.0.1
   * @return void
   */
  public static function register_connection($settings)
  {
    add_action('graphql_register_types', function () use ($settings) {
      $resolver = WPGraphQL_MB_Relationships::instance();

      $from_post_type = $settings['from']['field']['post_type'];
      $from_post_object = get_post_type_object($from_post_type);
      $from_connection_name = $settings['from']['graphql_name'];
      $from_connection_args = $settings['from']['graphql_args'];
      $to_post_type = $settings['to']['field']['post_type'];
      $to_post_object = get_post_type_object($to_post_type);
      $to_connection_name = $settings['to']['graphql_name'];
      $to_connection_args = $settings['to']['graphql_args'];

      if ($from_post_object !== null && $from_post_object->show_in_graphql) {
        register_graphql_connection(
          [
            'fromType'        => $from_post_object->graphql_single_name,
            'toType'          => $to_post_object->graphql_single_name,
            'fromFieldName'   => $from_connection_name,
            'connectionArgs'  => isset($from_connection_args) ? $from_connection_args : [],
            'resolveNode'     => $resolver->get_node_resolver(),
            'resolve'         => $resolver->get_resolver($from_post_type, $settings['id']),
          ]
        );
      }

      if ($to_post_object !== null && $to_post_object->show_in_graphql) {
        register_graphql_connection(
          [
            'fromType'        => $to_post_object->graphql_single_name,
            'toType'          => $from_post_object->graphql_single_name,
            'fromFieldName'   => $to_connection_name,
            'connectionArgs'  => isset($to_connection_args) ? $to_connection_args : [],
            'resolveNode'     => $resolver->get_node_resolver(),
            'resolve'         => $resolver->get_resolver($to_post_type, $settings['id']),
          ]
        );
      }
    });
  }

  /**
   * Get resolve node callback
   *
   * @access private
   * @since  0.1.0
   * @return function
   */
  private function get_node_resolver()
  {
    return function ($id, $args, $context, $info) {
      return !empty($id) ? DataSource::resolve_post_object($id, $context) : null;
    };
  }

  /**
   * Get resolve callback
   *
   * @access private
   * @since  0.1.0
   * @return function
   */
  private function get_resolver($post_type, $id)
  {
    return function ($root, $args, $context, $info) use ($post_type, $id) {
      $resolver = new PostObjectConnectionResolver($root, $args, $context, $info, $post_type);
      $resolver->setQueryArg('relationship', [
        'to' => $root->ID,
        'id' => $id,
      ]);
      // Meta Box does not want post_parent set
      $resolver->setQueryArg('post_parent', null);

      return $resolver->get_connection();
    };
  }

  /**
   * Initialise plugin.
   *
   * @access private
   * @since  0.0.1
   * @return void
   */
  private function init()
  {
    add_action('mb_relationships_registered', ['WPGraphQL_MB_Relationships', 'register_connection'], 1);
  }
}

add_action('mb_relationships_init', 'WPGraphQL_MB_Relationships_init');

if (!function_exists('WPGraphQL_MB_Relationships_init')) {
  /**
   * Function that instantiates the plugins main class
   *
   * @since 0.0.1
   */
  function WPGraphQL_MB_Relationships_init()
  {
    /**
     * Return an instance of the action
     */
    return \WPGraphQL_MB_Relationships::instance();
  }
}
