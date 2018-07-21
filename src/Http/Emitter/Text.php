<?php
namespace Plutter\Http\Emitter;

class Text extends Emitter {
    public function process(){
        $this->contentType = "text/plain";
    }
}
?>