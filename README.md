# graph-lvel
Laravel GraphQL implementation

This project was created because the most famous Laravel implementation of graphql is too verbose. While the pro to using is how the library grants its user full control over his data structures and query execution point (via `GraphQL::execute($schema, ...$otherParams)`). PHP-Lighthouse isn't verbose, but its constrictions include using directives for mutation, and providing no entrance point for supplying queries from the server side.

So this project aims to bridge the gap between the pain-points of both projects.
For those unfamiliar with GraphQl, it's a concept that allows you request and receive specific data thereby reducing your payload, with minimal database calls i.e. higher response time. By phasing out RESTful endpoints, you're in a better position to request resources across multiple models/nodes.

In addition, it supports pagination and relationship fetching

# How to use
You can either call it from your web controllers, or post the same payload as you would in a web controller, to a single endpoint. This endpoint then returns all the resources described in your schema
