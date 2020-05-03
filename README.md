# wp-graphql-mb-relationships
WPGraphQL Provider for MB Relationships

This is a simple WPGraphQL integration plugin for Meta Box Relationships. **It only supports post types at the moment.**

There are three new fields to add to the API configuration:
 - `show_in_graphql` - a boolean to show the connection in the schema
 - `graphql_name` - the name of the connection field in the schema
 - `graphql_args` - the connection args (optional)
 - `resolve` - the resolve handler (see WPGraphQL documentation) (experimental)
 - `resolve_node` - the node resolve handler (see WPGraphQL documentaiton) (experimental)

```
MB_Relationships_API::register( [ 
  'id'     => 'linked_posts', 
  'from'   => array(
      'show_in_graphql' => true,
      'graphql_name' => 'goingUp',
      'graphql_args' => [
        'higher' => [
	  'type' => 'Boolean',
	  'description' => 'Field Description',
         ],
       ],
      'object_type' => 'post', 
      'post_type'   => 'mypost', 
      'meta_box'    => [
          'title'         => 'Going Up!', 
          'context'       => 'side', 
      ],
    ], 
    'to'    => [
      'show_in_graphql' => true,
      'graphql_name' => 'goingDown',
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
