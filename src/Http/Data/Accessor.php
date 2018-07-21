<?php
namespace Plutter\Http\Data;

class Accessor {
    public static function parse(string $query){
        if(strpos($query, '.') === false)
            return [$query];
        $keys = [];
        $key = "/^((?:[a-z][a-z_-]+)|\[[0-9]\])(?:(?:[\.\[](.*))|(?:.{0}))$/Di";
        $string = $query;
        while(true){
            if(preg_match($key, $string, $matches)){
                $match = $matches[1];
                $string = substr($string, strlen($match));
                if($match[0] == '['){
                    $match = substr($match, 1, -1);
                    if($string[0] !== '.')
                        throw new HttpException("Can't get \"$query\" as it is invalid");
                    else
                        $string = substr($string, 1);
                }
                $keys[] = $match;
                if(strlen($string) == 0)
                    return $keys;
            }else{
                throw new HttpException("Can't get \"$query\" as it is invalid");
            }
        }
    }
    public static function setter($query, $values, $value){
        self::getter($quer, $values, $reference);
        $reference = $value;
    }
    public static function getter($query, $values, &$reference = -1){
        $keys = parse($query);
        $return_reference = $reference !== -1;
        $node = &$values;
        while(true){
            $key = array_shift($keys);
            if(isset($node[$key])){
                if($return_reference)
                    $reference = &$node[$key];
                if(count($keys) >= 1)
                    $node = $node[$key];
                else
                    return $node[$key];
            } else
                throw new HttpException("Cannot read key $key from $query");
        }
    }
}
?>