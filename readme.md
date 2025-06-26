# cookbook
"application that functions as an online cookbook"

## Requirements

- MySQL database server version 8.3
- PHP version 8.3
- PhpStorm development environment

## Installation

### Clone

- Clone this repo to your local machine using `https://github.com/fhamowska/cookbook.git`

### Setup

- Change the database connection information in the .env file

> In the app directory:
```shell
$ composer install
$ bin/console make:migration
$ bin/console doctrine:migrations:migrate
$ bin/console doctrine:fixtures:load
```

- The home page is at `/recipe`
