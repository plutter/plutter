<?php
namespace Plutter\Http\Emitter;
function array_to_xml($array, &$response) {
	foreach($array as $key => $value) {
		if(is_array($value)) {
			if(!is_numeric($key)){
				$subnode = $response->addChild("$key");
				array_to_xml($value, $subnode);
			}else{
				$subnode = $response->addChild("item");
				array_to_xml($value, $subnode);
			}
		}else {
			if(is_numeric($key))
				$key = "item";
			if(is_bool($value))
				$response->addChild("$key")->addAttribute("value", $value == true?"true":"false");
			else
				$response->addChild("$key",htmlspecialchars("$value"));
		}
	}
}

class Html extends Emitter {
    public function process(){
        $response = new SimpleXMLElement("<?xml version=\"1.0\"?><XMLResponse></XMLResponse>");
        array_to_xml($output,$response);
        $this->body = (string) $response->asXML();
        $this->contentType = "text/html";
    }
}
?>