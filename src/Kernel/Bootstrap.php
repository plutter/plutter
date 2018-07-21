<?php
namespace Plutter\Kernel;


class Bootstrap {
    protected $config = [];
    public function __construct(){
        $mode = $this->getMode();
        $this->bootstrapMode($mode);
    }
    private function getMode(){
        if(php_sapi_name() === 'cli' OR defined('STDIN')){
            return 'cli';
        }else{
            return 'web';
        }
    }
    private function bootstrapMode($mode){
        if($mode == 'web'){
            $application = new Applications\Web;
        }else{
            $application = new Applications\Cli;
        }
        $application->process();
    }
}