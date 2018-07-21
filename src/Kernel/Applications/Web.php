<?php
namespace Plutter\Kernel\Applications;

use Plutter\Services\Server;
use Plutter\Interfaces\ApplicationInterface;

class Web implements ApplicationInterface {
    public function process(){
        print_r(Server::singleton()
        ->configure([])
        ->execute());
    }
}