# Small it

## Introduction

**Smallit** is an open source api based
web application which I have developed with Laravel.

my target is having an easy to use url shortener with most features.

---

## Installation notes

The default `www_user` is set as `1000(www-data)` in `.env.example`. feel free to add your own environment.

## Requirements

1. `www-data` user and group available in your machine.
2. `docker and docker compose`
3. `GNU Make` (Optional)


- ### USER and Group
  (If you have problems with user and permissions, read this section)
  #### Note: www-data user and group are typically created and managed automatically when you install web server software like Apache or Nginx. Manually creating them is generally not required unless you have specific reasons to do so.

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

---

## Horizon 

Currently, Smallit is using Horizon to manage Queues.

run `php artisan horizon` command in PHP container to start queues working.

--- 
## Counting Views (Clicks)

check `apps/Http/Controllers/Api/UrlShortenerController`, `show` method

#### when you are using api to get **Long Url** :

your Http Request can have a header with Key: **uid** with Value of a unique id.

I suggest [fingerprint js]
to generate **uid**

[fingerprint js]:https://github.com/fingerprintjs/fingerprintjs

this is how I make the request to redirect user to Long url with counting clicks (in client side)

     <script>
        let queryString = '{{$url}}' // $url is short url (querystring)
        const fpPromise = import('https://openfpcdn.io/fingerprintjs/v3')
            .then(FingerprintJS => FingerprintJS.load())

        fpPromise
            .then(fp => fp.get())
            .then(result => {

                // This is the visitor identifier:
                return result.visitorId
            }).then(function (visitorId) {

       
            fetch(`http://localhost:8088/api/v1/show/${queryString}`, { 
                method: "GET",
                headers: {
                    'uid': visitorId,
                    'user_agent': navigator.userAgent
                }
            }).then(response => response.json()).then(function (data) {
                // console.log(data.data)
                data.data ? window.location.replace(`${data.data}`.replace('_var_', `${queryString}`)+'&') : document.getElementById("error").innerHTML = "Not Found";
            })
         })
     </script>
