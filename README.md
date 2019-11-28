# wp-graphql-mb-relationships
WPGraphQL Provider for MB Relationships

This is a simple WPGraphQL integration plugin for Meta Box Relationships. **It only supports post types at the moment.**

You will need to get the latest `master` branch from [MB Relationships](https://github.com/wpmetabox/mb-relationships) as there is a new hook to use.

There are two new fields to add to the API configuration: `show_in_graphql` and `graphql_name`.

```
MB_Relationships_API::register( [ 
  'id'     => 'linked_posts', 
  'from'   => array(
      'show_in_graphql' => true,
      'graphql_name' => 'goingUp',
      'object_type' => 'post', 
      'post_type'   => 'mypost', 
      'meta_box'    => [
          'title'         => 'Going Up!', 
          'context'       => 'side', 
      ], 
    ], 
    'to'    => [
      'show_in_graphql' => true,
      'graphql_name' => 'progressions',
      'object_type' => 'post', 
      'post_type'   => 'mypost', 
      'meta_box'    => [
          'title'         => 'Going Down!', 
          'context'       => 'side', 
          'empty_message' => 'None', 
      ], 
    ], 
  ]
); 
```

`show_in_graphql` will tell the plugin to create the connection.
`graphql_name` sets the connection name in the schema.
