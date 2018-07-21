<?php
namespace Plutter\Kernel;

use Plutter\Helper\Singleton;
use Plutter\Interfaces\ServiceInterface;

class Service extends Singleton implements ServiceInterface {
    public function bootstrap(){}
    public function configure(array $configuration) {
        return $this;
    }
    public function execute(){}
}