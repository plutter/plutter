<?php
namespace Cellulax\Kernel;

use Cellulax\Application;

class Component {
    /**
     * Application container
     *
     * @var Application
     */
    protected $app;
    public function __construct(Application $app){
        $this->app = $app;
    }
    /**
     * Delegate getComponent() to app->getComponent()
     *
     * @param string $name
     * @return void
     */
    public function getComponent(string $name){
        return $this->app->getComponent($name);
    }
}