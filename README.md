# phprivoxy/core
## Core library for TCP connections handling.

This PHP package based on Workerman framework (https://github.com/walkor/workerman) and will be useful, mostly, for custom proxy servers creation.

### Requirements 
- **PHP >= 8.1**

### Installation
#### Using composer (recommended)
```bash
composer phprivoxy/core
```

### Simple sample

```php
namespace PHPrivoxy\Core;

use Workerman\Connection\TcpConnection;

class HelloWorld implements TcpConnectionHandlerInterface
{
    public function handle(TcpConnection $connection, ?ConnectionParameters $connectionParameters = null): void
    {
        $connection->send("HTTP/1.1 200 OK\r\ncontent-type: text/html;charset=UTF8\r\n\r\n" . 'Hello, world!');
        $connection->close();
    }
}

$handler = new HelloWorld();
new Server($handler); // By default, it listen all connections on 8080 port.
```

This sample you also may find at "tests" directory.

Just run it:
```bash
php tests/test.php start
```

Configure your browser to work through a proxy server with the IP address 127.0.0.1 and port 8080.

Try to open any site on HTTP protocol. As sample, try to open http://php.net, http://google.com, http://microsoft.com.

Try to open http://not-existing-site/ - it will work! :-)

### License
MIT License See [LICENSE.MD](LICENSE.MD)
