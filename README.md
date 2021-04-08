## Test backend CBR service.

### Run app
* start docker `docker-compose up`
* start develop web-server `symfony server:start`

### Run tests
* `php vendor/phpunit/phpunit/phpunit --configuration phpunit.xml.dist --teamcity`