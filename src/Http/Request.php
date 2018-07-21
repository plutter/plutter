<?php
namespace Plutter\Http;


class Request {
    protected $location = [];
    protected $data = [];
    public function __construct(){
        $this->data["header"] = new Data\Header($_SERVER);
        $this->location = $this->fetchLocation();
        $this->data["file"] = new Data\File($_FILES);
        $this->data["cookie"] = new Data\Parsed($_COOKIE);
        $this->data["data"] = $this->fetchData();
    }
    public function getData($key){
        return $this->data[$key];
    }
    public function clone(){
        $clone = clone $this;
        return $clone;
    }
    private function fetchData(){
        $method = $this->location->get("method");
        switch($method){
            case "GET":
                return new Data\Parsed($_GET);
            break;
            case "POST":
                return new Data\Parsed($_POST);
            break;
            default:
                $data = new Data\Stream;
                $files = $data->getFiles();
                $this->data["file"] = new Data\File($files);
                return $data;
            break;
        }
    }
    private function fetchLocation() {
        return new Location($this);
    }
}