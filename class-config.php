<?php

use GraphQL\Error\InvariantViolation;
use WPGraphQL\Data\Connection\PostObjectConnectionResolver;
use WPGraphQL\Data\Connection\TermObjectConnectionResolver;
use WPGraphQL\Data\Connection\UserConnectionResolver;
use WPGraphQL\Data\DataSource;

final class WPGraphQL_MB_Relationships_Config
{
  /**
   * Type name
   */
  public $type_name;

  /**
   * Type object
   */
  public $type_object;

  /**
   * GraphQL type name
   */
  public $graphql_type_name;

  /**
   * GraphQL connection name
   */
  public $connection_name;

  /**
   * GraphQL connection type name
   */
  public $connection_type;

  /**
   * GraphQL connection arguments
   */
  public $connection_args;

  /**
   * GraphQL connection resolve function
   */
  public $resolve = null;

  /**
   * GraphQL connection resolve node function
   */
  public $resolve_node = null;

  /**
   * WordPress object type
   * post | user | term
   */
  public $object_type_name;

  function __construct($settings)
  {
    $this->object_type_name = $settings['object_type'];

    switch ($this->object_type_name) {
      default:
      case 'post':
        $this->type_name = $settings['field']['post_type'];
        $this->type_object = get_post_type_object($this->type_name);
        $this->graphql_type_name = $this->type_object->graphql_single_name;
        break;
        // case 'term':
        //   $this->type_name = $settings['field']['taxonomy'];
        //   $this->type_object = get_taxonomy($this->type_name);
        //   break;
      case 'user':
        $this->type_name = 'user';
        $this->type_object = null;
        $this->graphql_type_name = 'User';
        break;
    }

    $this->connection_name = $settings['graphql_name'];
    $this->connection_args = isset( $settings['graphql_args'] ) ? $settings['graphql_args'] : [];
    $this->type_object = get_post_type_object($this->type_name);
    if (array_key_exists('resolve', $settings)) {
      $this->resolve = $settings['resolve'];
    }
    if (array_key_exists('resolve_node', $settings)) {
      $this->resolve_node = $settings['resolve_node'];
    }
  }

  /**
   * Register WPGraphQL MB Relationships config.
   *
   * @access public
   * @since  0.3.0
   * @return void
   */
  public function should_register()
  {
    return $this->type_name === 'user' ||
      ($this->type_object !== null &&
        $this->type_object->show_in_graphql);
  }

  /**
   * Get applicable connection resolver
   *
   * @access public
   * @since  0.4.0
   * @return void
   */
  public function get_resolver(...$args)
  {
    switch ($this->object_type_name) {
      case 'post':
        $resolver = new PostObjectConnectionResolver(...$args);
        // Meta Box does not want post_parent set
        $resolver->setQueryArg('post_parent', null);
        return $resolver;
        // case 'term':
        //   return new TermObjectConnectionResolver(...$args);
      case 'user':
        return new UserConnectionResolver(...$args);
    }

    throw new InvariantViolation('Unsupported object_type_name for wpgraphql-mb-relationships');
  }
}
