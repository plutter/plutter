<?php
namespace Cellulax\Kernel;

use Symfony\Component\Yaml\Yaml;

use Cellulax\Exception\ConfigException;

class Config {
    /**
     * Loaded config data
     *
     * @var array
     */
    protected static $loaded = [];
    
    /**
     * Base config path
     *
     * @var string
     */
    protected static $path = __DIR__."/../Config/";
    
    /**
     * Get a configuration value from query
     *
     * @param string $query
     * @return mixed
     */
    public static function get(string $query): mixed {
        $keys = explode($query, ".");
        if(count($keys) == 0){
            throw new ConfigException("Invalid get query");
        }
        $name = array_shift($keys);
        self::load($name);
        $node = self::$loaded[$name];
        while(true){
            $key = array_shift($keys);
            if(isset($node[$key]))
                if(count($keys) >= 1)
                    $node = $node[$key];
                else
                    return $node[$key];
            else
                throw new ConfigException("Cannot read key $key from $query");
        }
    }

    /**
     * Load a configuration file
     *
     * @param string $name
     * @return void
     */
    protected static function load(string $name){
        if(!self::preloaded($name)){
            $path = self::path($name);
            if(!file_exists($path))
                throw new ConfigException("Cannot retrieve file $path");
            $file = file_get_contents($path);
            try {
                $parsed = Yaml::parse($file);
            }catch(\Exception $e){
                throw new ConfigException("Cannot parse file $path, ".$e.__toString());
            }
            self::$loaded[$name] = $parsed;
        }
    }

    /**
     * Check if a config file is preloaded
     *
     * @param string $name
     * @return boolean
     */
    protected static function preloaded(string $name): boolean {
        return isset(self::$loaded[$name]);
    }

    /**
     * Get configration file path
     *
     * @param string $name
     * @return string
     */
    protected static function path(string $name): string {
        return self::$path.$name.".yaml";
    }
}