<?php
namespace Plutter\Helper;

abstract class Singleton {
    /**
     * Sigleton instance
     *
     * @var mixed $instance
     */
    protected static $instance = null;

    /**
     * Get singleton instance
     *
     * @return object
     */
    final public static function singleton() {
        $class = get_called_class();
        if($class::$instance === null){
            $instance = $class::$instance = new $class;
            $instance->bootstrap();
        }
        return $class::$instance;
    }
}
?>