<?php

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
   * GraphQL connection arguments
   */
  public $connection_args;

  function __construct($settings)
  {
    $this->type_name = $settings['field']['post_type'];
    $this->connection_name = $settings['graphql_name'];
    $this->connection_args = $settings['graphql_args'];
    $this->type_object = get_post_type_object($this->type_name);
    $this->graphql_type_name = $this->type_object->graphql_single_name;
  }

  public function should_register()
  {
    return $this->type_object !== null &&
      $this->type_object->show_in_graphql;
  }
}
