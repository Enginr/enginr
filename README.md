# Enginr <sup style="font-size:0.4em;font-style:italic">v1.0.0-Alpha</sup> guide

- [**Getting started**](#gs)
    - [Introduction](#gs-intro) 
	- [Installation](#gs-installation)
    - [<span style="color:#00cc00">Hello world</span>](#gs-hello-world)
- [**Routing**](#route)
    - [Introduction](#route-intro)
    - [Usage](#route-usage)
- [**Static files**](#static)
    - [Introduction](#static-intro)
    - [Usage](#static-usage)
- [**Views**](#views)
    - [Introduction](#views-intro)
    - [Usage](#views-usage)
- [**View engine**](#ve)
    - [Introduction](#ve-intro)
    - [Usage](#ve-usage)
- [**HTTP**](#http)
    - [Introduction](#http-intro)
    - [Request](#http-req)
    - [Response](#http-res)  
- [**Middlewares**](#mid)
    - [Introduction](#mid-intro)
    - [Usage](#mid-usage)
- [**Session**](#session)
    - [Introduction](#session-intro)
    - [Usage](#session-usage)

<h2 id="gs">Getting started</h2>

<h3 id="gs-intro">Introduction</h3>

Enginr is a very lightweight micro-framework for writing Web applications quickly.  

Strongly inspired by  **Node.js** (and more particularly by its micro-framework **Express.js**), learning how to use it is therefore very simplified if you know these technologies.

*The biggest difference is the use of `$` for your variables... Yey...*

<h3 id="gs-installation">Installation</h3>

It is strongly recommended to use `composer` to work with Enginr.

#### Using composer

```bash
composer require enginr/enginr
```

#### Using git

```bash
git clone https://github.com/Enginr/enginr.git \
cd enginr \
composer update
```

<h3 id="gs-hello-world" style="color:#00cc00">Hello world</h3>

Good! The most existing moment... :grin:  
Let's create our first application !

#### 1. Create a new project

```bash
mkdir myApp \
cd myApp
```

#### 2. Deploy Enginr to the project

See [installation](#gs-installation) guide.

#### 3. Create the application file

You could named your **application file** like you want. By convention, I will use `app.php` for this tutorial...

```bash
touch app.php
```

This will be the **main file** of your application.  
By analogy, I would say that it is in a way the **"motherboard"** of your application.  
It will be in this file that you will define the *configuration of your server* and that you will add your different *modules*.

#### 4. Let's code !

Now, let's define what our application needs a minimum to work.  
In your `app.php` file, enter the following code.

```php
require 'vendor/autoload.php';

$app = new \Enginr\Enginr();

$app->get('/', function($req, $res) {
	$res->send('Hello world !');
});

$app->listen('127.0.0.1', 3000);
```

:bulb: Need some explanations ?  

OK. First, we need to instanciate Enginr :

```php
$app = new \Enginr\Enginr();
```

After that, the minimum to do is to declare our first route.
Here, we listening for the root route of our website.

```php
$app->get('/', ...
```

At the same time, we specify the controller (the action we choose to do) when a user goes to this route ...

```php
$app->get('/', function($req, $res) {
	$res->send('Hello world !'); // Send 'Hello world !' to the client
});
```

Now, your application need an host and a port to listening connections ...

```php
$app->listen('127.0.0.1', 3000);
```

#### 5. Run the server

For our application to work, we need to start it.  
To do this, let's go to our terminal (to the root location of our project), then launch the application.

```bash
php app.php
```

#### 6. Test our app

Open our favorite browser, and enter the host:port of our app.
Normaly, we get a great :

> Hello world !

That's it ! :beers: