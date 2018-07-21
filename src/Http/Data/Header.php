<?php
namespace Plutter\Http\Data;

class Header extends Data {
    protected $data;
    public function __construct($data){
        $headers = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'REDIRECT_') === 0) {
                $key = substr($key, 9);
                if (array_key_exists($key, $data)) {
                    continue;
                }
            }
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = strtr(strtolower(substr($key, 5)), '_', '-');
                $headers[$name] = $value;
                continue;
            }
            if ($value && strpos($key, 'CONTENT_') === 0) {
                $name = 'content-' . strtolower(substr($key, 8));
                $headers[$name] = $value;
                continue;
            }
        }


        $this->data = $headers;
    }
    private function createUploadedFileFromSpec($value){
        if (is_array($value['tmp_name'])) {
            return $this->normalizeNestedFileSpec($value);
        }
        return $value;
    }

    private function normalizeNestedFileSpec(array $files = []){
        $normalizedFiles = [];
        foreach (array_keys($files['tmp_name']) as $key) {
            $spec = [
                'tmp_name' => $files['tmp_name'][$key],
                'size'     => $files['size'][$key],
                'error'    => $files['error'][$key],
                'name'     => $files['name'][$key],
                'type'     => $files['type'][$key],
            ];
            $normalizedFiles[$key] = $this->createUploadedFileFromSpec($spec);
        }
        return $normalizedFiles;
    }
}
?>