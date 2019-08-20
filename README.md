# Code Review Robot

## Usage
```shell
# Review by file path
php robot.php path/to

# Review by merge request url
php mr.php {merge_request_url}
```

## Supported Rules
+ Echo in controller, logic
+ Exit in controller, logic
+ Public controller or logic method without comments
+ Use repo in controller, command
+ Use model in controller, logic, command
+ Method arguments with default values MUST go at the end of the argument list
+ Return api format data in logic