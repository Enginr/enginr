
# Enginr <sup style="font-size:0.4em;font-style:italic">v1.0.\*-alpha</sup>

![GitHub](https://img.shields.io/github/license/mashape/apistatus.svg?style=for-the-badge)
![PHP](https://img.shields.io/badge/php-%3E%3D7.2.0-blue.svg?&style=for-the-badge)
![RELEASE](https://img.shields.io/badge/pre--release-v1.0.1--alpha-green.svg?style=for-the-badge)

Enginr is a very lightweight micro-framework for writing Web applications or API quickly.  

Strongly inspired by  **Node.js** (and more particularly by its micro-framework **Express.js**), learning how to use it is therefore very simplified if you know these technologies.

The biggest difference is the use of `$` for your variables... Yey...

:triangular_flag_on_post: **Note :** This guide is for the alpha version. Many changes can be made in the near future.

- [**Getting started**](#gs)
    - [Installation](#gs-installation)
    - [Hello world](#gs-hello-world)
        - [Using manager (prototype)](#gs-manager)
        - [From scratch](#gs-from-scratch)
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

This section will show you how to create your first Enginr application.

<h2 id="gs-installation">Installation</h2>

It is strongly recommended to use `composer` to work with Enginr.

#### Using composer

```bash
composer require enginr/enginr:v1.0.*-alpha
```

#### Using git

```bash
git clone https://github.com/Enginr/enginr.git \
cd enginr \
composer update
```

<h2 id="gs-hello-world">Hello world</h2>

<h3 id="gs-manager">Using Manager (prototype)</h3>

To easily create an Enginr project, you can use the manager.  
To use it, start by installing it globally on your machine via composer.  

```bash
composer global require enginr/manager:dev-master
```

Once the manager is installed. We will create our first project...
For this guide, I will call it `myProject`. But you can give it any name you want !

```bash
enginr create myProject
```
You will have to follow a short interactive guide, then once done, your project will be generated !  

Once the generation is complete, go (still in your terminal) to the folder of your project (its name corresponds to the name you gave to your project), then launch the server.

```bash
cd myProject \
php app.php
```
If your server has started correctly, go to your browser, then enter the address of your server.  
**Note :** The alpha version of the manager will have given you the address `localhost:8000`.  

You should see the Enginr welcome page !

<h3 id="gs-from-sratch">From scratch</h3>

The most existing moment... :grin:  
Let's create our first application !

#### 1. Create a new project

```bash
mkdir myApp \
cd myApp
```

#### 2. Deploy Enginr to the project

See [installation](#gs-installation) guide.

#### 3. Create the application file

You could named your **application file** like you want. By convention, I will use `app.php` for this guide...

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
