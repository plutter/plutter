<?php
namespace Plutter\Http;

class Server {
    protected $request;
    protected $response;
    public function listen(){
        $this->request = new Request;
        $this->response = new Response;
    }
    public function setRequest(Request $request){
        $this->request = $request;
    }
    public function getRequest(){
        return $this->request;
    }
    public function setResponse(Response $Response){
        $this->response = $Response;
    }
    public function getResponse(){
        return $this->response;
    }
}