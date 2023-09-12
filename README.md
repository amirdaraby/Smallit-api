# Small it

![Laravel tests](https://github.com/amirdaraby/Smallit-api/actions/workflows/laravel.yml/badge.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Table of contents
- [Introduction](#introduction)
- [Install](#installation) ([Installation notes](#installation-notes))
- [Tests](#tests)
- [Queues](#queues)
- [API Documentation](#api-documentation)
- [Contribution](#contribution)

## Introduction

**Smallit** is an open source api based
web application, my target is having an easy to use url shortener with most features..

---

## Installation notes

The default `www_user` is set as `1000(www-data)` in `.env.example`. feel free to add your own environment.

## Requirements

1. `www-data` user and group available in your machine.
2. `docker and docker compose`
3. `GNU Make` (Optional)


- ### USER and Group
  (If you have problems with user and permissions, read this section)
  #### Note: www-data user and group are typically created and managed automatically when you install web server software like Apache or Nginx. Manually creating them is generally not required.

1. `sudo groupadd www-data` (Creates the www-data group)
2. `sudo useradd -g www-data -s /usr/sbin/nologin -M www-data` (Creates www-data user and add it to www-data group)
3. `id www-data` (Verify that the user and group have been created)
4. Recommended: Logout after this action 

---

## Installation

You can use the `make` command to install Smallit, or just do things manually

- ### Makefile

  Just run `make` command

---

- ### Production
Since there is some differences between development and production in configurations, Smallit has a Specific Docker for production named `docker-compose.prod.yml` in root directory.

run it using the following command: `compose -f docker-compose.prod.yml up --build --force-recreate`

---

- ### Set up manually

1. `cp .env.example .env`
2. `docker compose up --build --force-recreate`
3. `chgrp -R (username) storage bootstrap/cache`
4. `chmod -R ug+rwx storage bootstrap/cache`

#### now run `docker exec -it smallit_php bash` to go in PHP container and run the following commands:
1. `composer install` (installing packages and dependencies)
2. `php artisan key:generate` (generates `APP_KEY`)
3. `php artisan migrate` (run migrations)
4. `php artisan horizon:install` (publish horizon configuration and assets)
5. `php artisan test` (make sure app works fine)
6. `php artisan l5-swagger:generate` (generates API documentations)
7. `php artisan horizon` (start horizon)
---

## Tests
- ### PHPUnit
  #### You can run tests to make sure App works fine. 
  run `dockker exec -t smallit_php bash -c "vendor/bin/phpunit --coverage-text"` to see the tests result and coverage and generate an HTML Result in `public/test-coverage-report`.

---
## Queues

- ### Horizon 

   Smallit is using Horizon to manage Queues.

   run `docker exec -t smallit_php bash -c "php artisan horizon"` to start queues working.


- ### Supervisor
   starting Horizon can be automized using Supervisor, which is available in [Production](#production) Docker environment.

---
## API Documentation

- ### Swagger
   run `docker exec -t smallit_php bash -c "php artisan l5-swagger:generate"`, Api documentations will generate in route `/api/documentation`.

---

## Contribution
The project has a separate contribution file. Please adhere to the steps listed in the separate contributions [file](.github/CONTRIBUTING.md)

### Contact
You can reach me on [Linkedin @amirdaraby](https://www.linkedin.com/in/amirdaraby/)

### License
[![Licence](https://img.shields.io/github/license/Ileriayo/markdown-badges?style=for-the-badge)](LICENSE.md)