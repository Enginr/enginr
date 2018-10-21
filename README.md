#  Enginr

(DEV) A lightweight PHP solution for easily creating web applications.

## Getting started

If you are used to Expressjs, then consider that it is almost the same thing, with a $ at the beginning of your variables... If not, this guide will be useful to you.

First, you must instantiate Enginr :

```php
$app = new \Enginr\Enginr();
```

Then, declare your first **route** :

```php
$app->get('/', function($req, $res) {
	$res->send('Hello world !');
});
```

Okay, now, specify on which **interface** and **port** you are listening :
**Note :** The *callback function* is **optional**. It can allow you to log in when your server has started.
**Note 2 :** The **System** class allows you to write to the **standard output** of your terminal *(Refer to the [documentation](doc/documentation.md) provided)*.

```php
$app->listen('127.0.0.1', 3000, function() {
	System::log('Server started ...');
});
```

Good! Good! We are almost there...
Your server is configured, all you need to do is launch it.
In your **terminal**, go to the **location of your file**, then enter the following command :
**Note :** in my example, my file is named `app.php`.

```sh
php app.php
```

Right ! Normally, your server is started and waiting for connections ...
To test if your application is working, simply make a request to your server in your favorite browser !
In our case the url will be : `localhost:3000` ...

That's all !