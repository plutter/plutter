<?php
namespace Plutter\Http\Data;

class File extends Data {
    protected $data;
    public function __construct($files){
        $normalized = [];
        foreach ($files as $key => $value) {
            if (is_array($value)) {
                $normalized[$key] = $this->createUploadedFileFromSpec($value);
                continue;
            }
            throw new HttpException(sprintf(
                'Invalid value in files specification, received %s',
                (is_object($method) ? get_class($method) : gettype($method))
            ));
        }

        $this->data = $normalized;
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