# Small it

## Introduction

**Smallit** is an open source api based
web application which I have developed with Laravel.

my target is having an easy to use url shortener with most features.

---

## How to use ?

clone project an run `make` command in root directory

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
