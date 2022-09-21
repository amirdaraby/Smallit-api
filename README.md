# Small it

### latest release: [v1.1.1]

[v1.1.1]:https://github.com/amirdaraby/url-shortener/releases/tag/v1.1.1

## Introduction

**Smallit** is an open source api based
web application which I have developed with Laravel.

my target is having an easy to use url shortener with most features.

---

## Requirements

* PHP
* MySQL
* Composer (to install laravel and dependencies)

---

## How to use ?

* install requirments
* run `php artisan migrate`
* run `php artisan queue:work`
* run `php artisan route:list` to see api addresses

---

## Counting Views (Clicks)

check `apps/Http/Controllers/Api/UrlShortenerController`, `show` method

#### when you are using api to get **Long Url** :

your Http Request must have a header with Key: **uid** with Value of a **fingerprint** unique id

I suggest [fingerprint js]
to generate **uid**

[fingerprint js]:https://github.com/fingerprintjs/fingerprintjs

this is how I make the request to redirect user to Long url with counting clicks

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
