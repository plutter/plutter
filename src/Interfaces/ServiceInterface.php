<?php
namespace Plutter\Interfaces;

interface ServiceInterface {
    public static function singleton();
    //public static function __callStatic(string $name, array $arguments);
    public function bootstrap();
    public function configure(array $configuration);
    public function execute();
}