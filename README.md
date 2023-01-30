# intro-application

## Laravel sail

If you have Docker installed on your machine, you may use [Laravel Sail](https://laravel.com/docs/8.x/sail) to run various Docker containers for local
development. By default, port 80 is used for the web server, making the application reachable on `http://localhost` in your browser.

Commands:

- `./vendor/bin/sail up -d`
  - Starts the services defined in `docker-compose.yml`

- `./vendor/bin/sail down`
  - Stops the aforementioned services

To run Artisan commands, use `./vendor/bin/sail artisan`. For more information, view
the [Sail documentation](https://laravel.com/docs/8.x/sail#executing-sail-commands).

### Setting up
Install Sail without PHP:
```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```
First, start the services by running `./vendor/bin/sail up -d`. Migrate the database by running `./vendor/bin/sail artisan migrate` and finally seed
the database by running `
./vendor/bin/sail artisan db:seed`.
