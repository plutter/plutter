<?php
namespace Plutter\Http\Data;

class Parsed extends Data {
    protected $data;
    public function __construct($data){
        $this->data = $data;
    }
}
?>