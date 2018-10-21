
#  Enginr

(DEV) A lightweight PHP solution for easily creating web applications.

##  Documentation

- [Getting started](#getting-started)
- [HTTP](#http)
	- [Request](#http-request)
	- [Response](#http-response)
- [Routing](#routing)
	- [Methods supported](#routing-methods)
- [Static files](#static-files)
- [View](#view)
- [Middlewares](#middlewares)
	- [Default middlewares used](#middlewares-default)

<h3 id="#getting-started">Getting started</h3>
  
 If you are used to Expressjs, then consider that it is almost the same thing, with a $ at the beginning of your variables...  
If not, this guide will be useful to you.

First, you must instantiate Enginr :

```php
$app  =  new \Enginr\Enginr();
```

Then, declare your first **route** :

```php
$app->get('/', function($req, $res) {
    $res->send('Hello world !');
});
```

Okay, now, specify on which **interface** and **port** you are listening :  

```php
$app->listen('127.0.0.1', 3000);
```

Good! Good! We are almost there...  
Your server is configured, all you need to do is launch it.  
In your **terminal**, go to the **location of your file**, then enter the following command :  

```sh
php app.php
```

Right ! Normally, your server is started and waiting for connections ...  
To test if your application is working, simply make a request to your server in your favorite browser !  
In our case the url will be : `localhost:3000` ...

That's all !

<h3 id="#http">HTTP</h3>

The HTTP module is used to retrieve and process communications between the server and the client.

<h4 id="#http-request">Request</h4>

The Request module processes incoming information (the client request).  
It is this that is found in the first parameter of the callback functions.

```php
$app->get('/', function(Request $req, ...) { ... });
```

**Its structure is as follows :**

- method : *the client request method*
- uri :  *the client request URI (without query)*
- realuri : *the real request uri (with query)*
- version : *the client HTTP version*
- headers : *the HTTP headers*
- body : *the HTTP body message*
- host : *the resource IP address*
- port : *the resource port listening*

<h4 id="#http-response">Response</h4>

The Response module processes outgoing information.  
It is the one that will allow us to send a reply to the customer, and that is also why it is used in our callback functions.

```php
$app->get('/', function(... , Response $res) { ... });
```

**Its structure is as follow :**  

**send**

```php
public function send(mixed $body, [bool $setContentType = TRUE]): void
```
Sends a response as a string to the customer.  
The `$body` is the content that you want to send.  
The `$setContentType` Allows to generate the Content-Type automatically in relation to the type contained in $body.  
*Note :* You can pass an `array` as a parameter. It will automatically be converted to `json` before sending.  

**end**

```php
public function end(mixed $body): void
```
Same as send() method. But its cannot automatically set the HTTP Content-Type header.  

**setHeaders**

```php
public function setHeaders(array $headers): void
```
Set HTTP headers.  
The `$headers` parameter is in this form :
```php
[
    'Content-Type' => 'application/json',
]
```

**setStatus**

```php
public function setStatus(int $status): void
```
Set HTTP status code.  

**render**

```php
public function render(string $pathfile): void
```
Send a view.  
The `$pathfile` is the path to your HTML file.  

cf. : [The View documentation](#view)

<h3 id="#routing">Routing</h3>

Routes allow you to declare an URI on which you want to perform actions.  
They are defined by :
- an **HTTP method**
- an **URI**
- a **callback** function

**Example :**

```php
$app->get('/my/route/to/do/things', function($req, $res) {
    // ...
});
```
- **method :** `get`
- **uri :** `/my/route/to/do/things`
- **callback :** `function($req, $res) { ... }`

<h4 id="#routing-methods">Methods supported</h4>

- **all** : all methods listened
- **get** : retrieve resource representation/information only
- **post** : to create new subordinate resources
- **put** : to update existing resource
- **patch** : to make partial update on a resource
- **delete** : to delete resources

*cf. : [HTTP RESTful methods](https://restfulapi.net/http-methods/)*

<h3 id="#static-files">Static files</h3>

*Soon ...*

<h3 id="#view">View</h3>

*Soon ...*

<h3 id="#middleware">Middlewares</h3>

*Soon ...*

<h4 id="#middlewares-default">Default middlewares used</h4>

*Soon ...*
