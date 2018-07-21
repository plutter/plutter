<?php
namespace Plutter\Http\Emitter;

class Json extends Emitter {
    public function process(){
        $this->contentType = "application/json";
        $this->body = is_array($this->body)?json_encode($this->body):$this->body;
    }
}
?>