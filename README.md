# wp-graphql-mb-relationships
WPGraphQL Provider for MB Relationships

This is a simple WPGraphQL integration plugin for Meta Box Relationships. **It only supports post types at the moment.**

You will need to get the latest `master` branch from [MB Relationships](https://github.com/wpmetabox/mb-relationships) as there is a new hook to use.

There are three new fields to add to the API configuration:
 - `show_in_graphql` - a boolean to show the connection in the schema
 - `graphql_name` - the name of the connection field in the schema
 - `graphql_args` - the connection args (optional)

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
