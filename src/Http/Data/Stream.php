<?php
namespace Plutter\Http\Data;

class Stream extends Data {
    protected $data;
    protected $files= [];
    public function __construct(){
        $this->data = file_get_contents('php://input');
        $boundary = $this->boundary();
		if (!count($boundary)) {
            $data = $this->parse();
            $files = [];
            return ;
		}
		$blocks = $this->split($boundary);
        list($data, $files) = $this->blocks($blocks);
        $this->data = $data;
        $this->files = $files;
    }
    public function getFiles(){
        return $this->files;
    }
    private function split($boundary){
		$result = preg_split("/-+$boundary/", $this->data);
        array_pop($result);
		return $result;
	}
    private function parse(){
		parse_str(urldecode($this->data), $result);
		return $result;
	}
    private function boundary(){
		preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
		return @$matches[1];
    }
    private function blocks($array){
		$results = [[], []];
		foreach($array as $key => $value){
			if (empty($value))
				continue;
			$block = $this->decide($value);
			if (count($block['post']) > 0)
				array_push($results[0], $block['post']);
			if (count($block['file']) > 0)
				array_push($results[1], $block['file']);
		}
		return $results;
	}
	private function decide($string){
		if (strpos($string, 'application/octet-stream') !== FALSE){
			return array(
				'post' => $this->file($string),
				'file' => array()
			);
		}
		if (strpos($string, 'filename') !== FALSE){
			return array(
				'post' => array(),
				'file' => $this->file_stream($string)
			);
		}
		return array(
			'post' => $this->post($string),
			'file' => array()
		);
	}
	private function file($string){
		preg_match('/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s', $string, $match);
		return array(
			$match[1] => $match[2]
		);
	}
	private function file_stream($string){
		$data = array();
		preg_match('/name=\"([^\"]*)\"; filename=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $string, $match);
		preg_match('/Content-Type: (.*)?/', $match[3], $mime);
		$image = preg_replace('/Content-Type: (.*)[^\n\r]/', '', $match[3]);
		$path = sys_get_temp_dir().'/php'.substr(sha1(rand()), 0, 6);
		$err = file_put_contents($path, $image);
		if (preg_match('/^(.*)\[\]$/i', $match[1], $tmp)) {
			$index = $tmp[1];
		} else {
			$index = $match[1];
		}
		$data[$index]['name'][] = $match[2];
		$data[$index]['type'][] = $mime[1];
		$data[$index]['tmp_name'][] = $path;
		$data[$index]['error'][] = ($err === FALSE) ? $err : 0;
		$data[$index]['size'][] = filesize($path);
		return $data;
	}
	private function post($string){
		$data = array();
		preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $string, $match);
		if (preg_match('/^(.*)\[\]$/i', $match[1], $tmp)) {
			$data[$tmp[1]][] = $match[2];
		} else {
			$data[$match[1]] = $match[2];
		}
		return $data;
	}
}
?>