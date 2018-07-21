<?php
declare(strict_types=1);
namespace Cellulax;

use Cellulax\Kernel\Bootstrap;
use Cellulax\Kernel\Component;


class Application {
    /**
     * Application components
     *
     * @var array
     */
    protected $components = [];
    public function __construct(){
        $bootstrap = new Bootstrap($this);
    }
    /**
     * Add a component to the app
     *
     * @param string $name
     * @param Component $component
     * @return void
     */
    public function addComponent(string $name, Component $component){
        $this->components[$name] = $component;
    }

    /**
     * Get a component
     *
     * @param string $name
     * @return void
     */
    public function getComponent(string $name){
        return $this->components[$name];
    }
}