<?php
namespace Plutter\Services;

use Plutter\Kernel\Service;
use Plutter\Http\Server as ServerService;

class Server extends Service {
    protected $server;
    public function bootstrap(){
        $this->server = new ServerService;
    }
    public function execute(){
        $this->server->listen();
        print_r($this->server);
    }
}