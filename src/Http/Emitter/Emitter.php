<?php
namespace Plutter\Http\Emitter;

class Emitter {
    public function emit($request){
        $this->request = $request;
        $this->version = $request->getVersion();
        $this->body = $request->getBody();
        $this->statusCode = $request->getStatusCode();
        $this->reasonPhrase = $request->getReasonPhrase();
        $this->headers = $request->headers->getAll();
        $this->process();
        $this->emitStatusLine();
        $this->emitHeaders();
        $this->emitBody();
    }
    private function emitStatusLine(){
        $reasonPhrase = $this->reasonPhrase;
        header(sprintf(
            'HTTP/%s %d%s',
            $this->version,
            $this->statusCode,
            ($reasonPhrase ? ' ' . $reasonPhrase : '')
        ));
    }
    private function emitHeaders(){
        foreach ($this->headers as $header => $values) {
            $name  = $this->filterHeader($header);
            $first = $name === 'Set-Cookie' ? false : true;
            foreach ($values as $value) {
                header("{$name}: {$value}", $first);
                $first = false;
            }
        }
    }
    private function emitBody(){
        echo $this->body;
    }
    private function filterHeader($header){
        $filtered = str_replace('-', ' ', $header);
        $filtered = ucwords($filtered);
        return str_replace(' ', '-', $filtered);
    }
}
?>