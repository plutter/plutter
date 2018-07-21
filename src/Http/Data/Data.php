<?php
namespace Plutter\Http\Data;

class Data {
    public function clone(){
        $clone = clone $this;
        return $clone;
    }
    public function getAll(){
        return $this->data;
    }
    public function get(string $query){
        $data = $this->data;
        return Accesor::getter($query, $data);
    }
    public function set(string $query, $value){
        $data = $this->data;
        return Accesor::setter($query, $data, $value);
    }
}
?>