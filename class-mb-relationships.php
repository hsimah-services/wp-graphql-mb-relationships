<?php

use WPGraphQL\Data\DataSource;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;

require_once __DIR__ . '/class-config.php';

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
   * Register single direction
   *
   * @access public
   * @since  0.3.0
   * @return void
   */
  public static function register_connection($id, $to, $from, $direction)
  {
    add_action('graphql_register_types', function () use ($to, $from, $id, $direction) {
      $to_config = new WPGraphQL_MB_Relationships_Config($to);
      $from_config = new WPGraphQL_MB_Relationships_Config($from);

      if ($to_config->should_register() && $from_config->should_register()) {

        $resolver = WPGraphQL_MB_Relationships::instance();

        register_graphql_connection(
          [
            'fromType'        => $from_config->graphql_type_name,
            'toType'          => $to_config->graphql_type_name,
            'fromFieldName'   => $from_config->connection_name,
            'connectionArgs'  => isset($from_config->connection_args) ? $from_config->connection_args : [],
            'resolveNode'     => $to_config->resolve !== null ? $to_config->resolve : $resolver->get_node_resolver($to_config),
            'resolve'         => $to_config->resolve_node !== null ? $to_config->resolve_node : $resolver->get_resolver($to_config, $id, $direction),
          ]
        );
      }
    });
  }

  /**
   * Register WPGraphQL MB Relationships config.
   *
   * @access public
   * @since  0.0.1
   * @return void
   */
  public static function register_connections($settings)
  {
    $to = $settings['to'];
    $from = $settings['from'];

    if (
      (array_key_exists('show_in_graphql', $to) &&
        $to['show_in_graphql'] === true)
    ) {
      WPGraphQL_MB_Relationships::register_connection($settings['id'], $from, $to, 'to');
    }

    if ((array_key_exists('show_in_graphql', $from) &&
      $from['show_in_graphql'] === true)) {
      WPGraphQL_MB_Relationships::register_connection($settings['id'], $to, $from, 'from');
    }
  }

  /**
   * Get resolve node callback
   *
   * @access private
   * @since  0.1.0
   * @return function
   */
  protected function get_node_resolver($config)
  {
    return function ($node, $args, $context, $info) use ($config) {
      return !empty($node) ? $config->get_node_resolver($node, $context) : null;
    };
  }

  /**
   * Get resolve callback
   *
   * @access private
   * @since  0.1.0
   * @return function
   */
  protected function get_resolver($config, $id, $relationship)
  {
    return function ($root, $args, $context, $info) use ($config, $id, $relationship) {
      $resolver = $config->get_resolver($root, $args, $context, $info, $config->type_name);
      $resolver->setQueryArg('relationship', [
        $relationship => $root->ID,
        'id' => $id,
      ]);

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
  protected function init()
  {
    add_action('mb_relationships_registered', function ($settings) {
      WPGraphQL_MB_Relationships::register_connections($settings);
    }, 10, 1);
  }
}
