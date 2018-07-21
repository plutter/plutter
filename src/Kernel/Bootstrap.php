<?php
namespace Cellulax\Kernel;

use Cellulax\Kernel\Configuration;
use Cellulax\Application;

class Bootstrap {
    protected $config = [];
    /**
     * Bootstrap an application
     *
     * @param Application $app
     */
    public function __construct(Application $app){
        $this->readComponents();
        $this->loadComponents($app);
    }

    /**
     * Get components
     *
     * @return void
     */
    private function readComponents(){
        $this->config = Configuration::get("components.components");
    }

    /**
     * Load components
     *
     * @param Application $app
     * @return void
     */
    private function loadComponents(Application $app){
        foreach($this->config as $name => $component){
            $component = "\Cellulax\Component\\".$component;
            $component = new $component($app);
            $app->addComponent($name, $component);
            $component->execute();
        }
    }
}