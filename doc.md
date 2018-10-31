## Table of contents

- [\Enginr\Enginr](#class-enginrenginr)
- [\Enginr\Router](#class-enginrrouter)
- [\Enginr\Socket](#class-enginrsocket)
- [\Enginr\Http\Http](#class-enginrhttphttp)
- [\Enginr\Http\Request](#class-enginrhttprequest)
- [\Enginr\Http\Response](#class-enginrhttpresponse)
- [\Enginr\Middleware\BodyParser\BodyParser](#class-enginrmiddlewarebodyparserbodyparser)
- [\Enginr\Middleware\Cookie\Cookie](#class-enginrmiddlewarecookiecookie)
- [\Enginr\Middleware\Session\Session](#class-enginrmiddlewaresessionsession)
- [\Enginr\Middleware\Session\UUID](#class-enginrmiddlewaresessionuuid)
- [\Enginr\System\System](#class-enginrsystemsystem)

<hr />

### Class: \Enginr\Enginr

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct()</strong> : <em>void</em><br /><em>The Enginr constructor</em> |
| public | <strong>listen(</strong><em>\string</em> <strong>$host</strong>, <em>int/\integer</em> <strong>$port</strong>, <em>\callable</em> <strong>$handler=null</strong>)</strong> : <em>void</em><br /><em>Listen client incomming connection an process it</em> |
| public | <strong>set(</strong><em>\string</em> <strong>$prop</strong>, <em>\string</em> <strong>$value</strong>)</strong> : <em>void</em><br /><em>Set propterties Enginr</em> |
| public static | <strong>static(</strong><em>\string</em> <strong>$path</strong>)</strong> : <em>Router The Router created</em><br /><em>Create a Router with all files that are in the directory specified</em> |

*This class extends [\Enginr\Router](#class-enginrrouter)*

<hr />

### Class: \Enginr\Router

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct()</strong> : <em>void</em><br /><em>The Router constructor</em> |
| public | <strong>all(</strong><em>\string</em> <strong>$uri</strong>, <em>callable[]</em> <strong>$handlers</strong>)</strong> : <em>\Enginr\self</em><br /><em>Add a route of fictive ALL method for listening all methods</em> |
| public | <strong>delete(</strong><em>\string</em> <strong>$uri</strong>, <em>callable[]</em> <strong>$handlers</strong>)</strong> : <em>\Enginr\self</em><br /><em>Add a DELETE route to the collection</em> |
| public | <strong>get(</strong><em>\string</em> <strong>$uri</strong>, <em>callable[]</em> <strong>$handlers</strong>)</strong> : <em>\Enginr\self</em><br /><em>Add a GET route to the collection</em> |
| public | <strong>patch(</strong><em>\string</em> <strong>$uri</strong>, <em>callable[]</em> <strong>$handlers</strong>)</strong> : <em>\Enginr\self</em><br /><em>Add a PATCH route to the collection</em> |
| public | <strong>post(</strong><em>\string</em> <strong>$uri</strong>, <em>callable[]</em> <strong>$handlers</strong>)</strong> : <em>\Enginr\self</em><br /><em>Add a POST route to the collection</em> |
| public | <strong>put(</strong><em>\string</em> <strong>$uri</strong>, <em>callable[]</em> <strong>$handlers</strong>)</strong> : <em>\Enginr\self</em><br /><em>Add a PUT route to the collection</em> |
| public | <strong>use()</strong> : <em>\Enginr\self</em><br /><em>Implement middlewares This method accept 2 types of middlewares : 1. A peer of [root uri] > [Router] A root uri is combined with the uris specified in the Router 2. An array of callables Before any request process, this handlers will be called first. Then, the peer of Request/Response will be transfered to classical process route The first type of middleware accepted : The second type of middleware accepted :</em> |
| protected | <strong>_process(</strong><em>[\Enginr\Http\Request](#class-enginrhttprequest)</em> <strong>$req</strong>, <em>[\Enginr\Http\Response](#class-enginrhttpresponse)</em> <strong>$res</strong>, <em>array/bool</em> <strong>$route</strong>, <em>\boolean</em> <strong>$found=false</strong>)</strong> : <em>void</em><br /><em>Process the HTTP request Tries to match a route and calls the corresponding handlers. If the 'next' function is called, the next route will be tested.</em> |

<hr />

### Class: \Enginr\Socket

| Visibility | Function |
|:-----------|:---------|
| public | <strong>bind(</strong><em>\string</em> <strong>$host</strong>, <em>int/\integer</em> <strong>$port</strong>)</strong> : <em>void</em><br /><em>Bind a host and port to the main socket then listen it</em> |
| public | <strong>close(</strong><em>mixed</em> <strong>$socket=null</strong>)</strong> : <em>void</em><br /><em>Close a socket If not socket specified, then close the main socket</em> |
| public | <strong>create()</strong> : <em>void</em><br /><em>Create a new socket</em> |
| public static | <strong>getPeerName(</strong><em>\Enginr\resource</em> <strong>$socket</strong>)</strong> : <em>object An object of [host, port]</em><br /><em>Return a strucutre of host and port of the socket speficied</em> |
| public | <strong>set(</strong><em>int/\integer</em> <strong>$optlvl</strong>, <em>int/\integer</em> <strong>$optname</strong>, <em>int/\integer</em> <strong>$optval</strong>)</strong> : <em>void</em><br /><em>Set option to the main socket</em> |
| public | <strong>watch(</strong><em>\callable</em> <strong>$handler</strong>, <em>array</em> <strong>$queue=array()</strong>)</strong> : <em>void</em><br /><em>Watch incomming connection and process it</em> |
| public static | <strong>write(</strong><em>\Enginr\resource</em> <strong>$socket</strong>, <em>\string</em> <strong>$buffer</strong>)</strong> : <em>void</em><br /><em>Write to a socket</em> |

<hr />

### Class: \Enginr\Http\Http

| Visibility | Function |
|:-----------|:---------|

<hr />

### Class: \Enginr\Http\Request

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>\Enginr\Http\resource</em> <strong>$client</strong>, <em>\string</em> <strong>$buffer</strong>)</strong> : <em>void</em><br /><em>The Request constructor Hydrate the Request properties</em> |

<hr />

### Class: \Enginr\Http\Response

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>\Enginr\Http\resource</em> <strong>$client</strong>, <em>\string</em> <strong>$view</strong>, <em>\string</em> <strong>$template</strong>)</strong> : <em>void</em><br /><em>The response constructor</em> |
| public | <strong>end(</strong><em>string</em> <strong>$body=null</strong>)</strong> : <em>void</em><br /><em>Send a response to the client, then close the socket</em> |
| public | <strong>getExt(</strong><em>mixed</em> <strong>$body</strong>)</strong> : <em>string The extension evaluated</em><br /><em>Get the appropriate extension of the body</em> |
| public | <strong>render(</strong><em>\string</em> <strong>$pathfile</strong>, <em>array</em> <strong>$vars=array()</strong>)</strong> : <em>void</em><br /><em>Send an html file content</em> |
| public | <strong>send(</strong><em>mixed</em> <strong>$body=null</strong>)</strong> : <em>void</em><br /><em>Prepare the response and send it</em> |
| public | <strong>setHeaders(</strong><em>array</em> <strong>$headers</strong>)</strong> : <em>void</em><br /><em>Initialize the HTTP headers propertie If HTTP headers already exists, merge it with the headers specified</em> |
| public | <strong>setStatus(</strong><em>int/\integer</em> <strong>$status</strong>)</strong> : <em>void</em><br /><em>Set the first line of HTTP response</em> |

<hr />

### Class: \Enginr\Middleware\BodyParser\BodyParser

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>init()</strong> : <em>object The middleware function</em><br /><em>Return the middleware</em> |

<hr />

### Class: \Enginr\Middleware\Cookie\Cookie

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>init()</strong> : <em>object A callable to use</em><br /><em>The middleware initializer</em> |

<hr />

### Class: \Enginr\Middleware\Session\Session

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>init()</strong> : <em>object A callable for use</em><br /><em>The middleware initializer</em> |

<hr />

### Class: \Enginr\Middleware\Session\UUID

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>v4()</strong> : <em>string A random unique ID</em><br /><em>Generate a random unique ID</em> |

<hr />

### Class: \Enginr\System\System

| Visibility | Function |
|:-----------|:---------|
| public static | <strong>log(</strong><em>mixed</em> <strong>$data</strong>)</strong> : <em>void</em> |
| public static | <strong>out(</strong><em>mixed</em> <strong>$data</strong>, <em>\boolean</em> <strong>$crlf=true</strong>)</strong> : <em>void</em><br /><em>Writes data to the standard output</em> |

