# Budget App

Welcome to the official repository of _Budget App_\*, the leading financial
tracking application for families in the European market.

Our application aims to empower families to manage their finances with ease and
accuracy, offering features such as expense tracking, financial planning, and
budgeting. For details on upcoming features, refer to the User Stories in [our
Asana project](https://app.asana.com/0/1209628343151819/1209628343151834).

\*_Note: "Budget App" is a provisional name. A final name will be chosen with a
marketing focus._

## Setup

To run the project locally, use [Docker](https://www.docker.com/) and [Laravel
Sail](https://laravel.com/docs/12.x/sail). Prerequisites are
[git](https://git-scm.com/) and [Docker](https://www.docker.com/). As an alternative to Docker on macOS, we recommend
[Orbstack](https://orbstack.dev/).

**It is recommended to set up this alias in your system before proceeding with
the following steps: `alias sail='bash vendor/bin/sail'`.**

### Steps

1. Clone the repository:

    `git clone git@github.com:LucaPipolo/budget-app.git && cd budget-app`

2. Install Composer dependencies:

    ````shell
    docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs```
    ````

3. Create .env file:

    `cp .env.example .env`

4. Start Laravel Sail:

    `sail up`

5. Generate an app key

    `sail php artisan key:generate`

6. Install NPM dependencies.

    `sail bun install`

7. Build assets

    `sail bun dev`

8. Run migrations and seed the database

    `sail php artisan migrate:fresh --seed`

## How to Contribute

Contributions are welcome!

Read our [Code of Conduct](https://github.com/LucaPipolo/budget-app/blob/main/CODE_OF_CONDUCT.md) before contributing.
For security vulnerabilities, consult our [Security Policy](https://github.com/LucaPipolo/budget-app/blob/main/SECURITY.md).

## License

Licensed under [GNU AFFERO GENERAL PUBLIC LICENSE](https://github.com/LucaPipolo/budget-app/blob/main/LICENSE)
— Copyright (C) 2025 somoscuatro.
