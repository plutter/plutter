<?php
namespace Plutter\Http\Emitter;

class Html extends Emitter {
    public function process(){
        $this->contentType = "text/html";
    }
}
?>