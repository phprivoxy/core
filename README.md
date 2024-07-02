# phprivoxy/core
## Core library for TCP connections handling.

This PHP package based on Workerman framework (https://github.com/walkor/workerman) and will be useful, mostly, for custom proxy servers creation.

### Requirements 
- **PHP >= 8.1**

### Installation
#### Using composer (recommended)
```bash
composer create phprivoxy/core
```

### Simple TCP server sample

```php
namespace PHPrivoxy\Core;

use Workerman\Connection\TcpConnection;

class HelloWorld implements PHPrivoxy\Core\Tcp\TcpHandlerInterface
{
    public function handle(TcpConnection $connection, ?ConnectionParameters $connectionParameters = null): void
    {
        $connection->send("HTTP/1.1 200 OK\r\ncontent-type: text/html;charset=UTF8\r\n\r\n" . 'Hello, world!');
        $connection->close();
    }
}

$handler = new HelloWorld();
new TcpServer($handler, 1, 8080, '0.0.0.0');
```

This sample you also may find at "tests" directory.

Just run it:
```bash
php tests/tcp_server.php start
```

Configure your browser to work through a proxy server with the IP address 127.0.0.1 and port 8080.

Try to open any site on HTTP protocol. As sample, try to open http://php.net, http://google.com, http://microsoft.com.

Try to open http://not-existing-site/ - it will work! :-)

### Simple HTTP server sample

```php
namespace PHPrivoxy\Core;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Workerman\Connection\TcpConnection;

class Psr7HelloWorld implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Workerman\Psr7\Response(200, ['content-type' => 'text/html'], 'Hello, world!');
    }
}

class ResponseHandler implements PHPrivoxy\Core\Http\ResponseHandlerInterface
{
    public function handle(ResponseInterface $response, TcpConnection $connection): void
    {
        $connection->send($response);
        $connection->close();
    }
}

$requestHandler = new Psr7HelloWorld();
$responseHandler = new ResponseHandler();
new HttpServer($requestHandler, $responseHandler, 1, 8080, '0.0.0.0');
```

This sample you also may find at "tests" directory.

Just run it:
```bash
php tests/http_server.php start

### License
MIT License See [LICENSE](LICENSE)
