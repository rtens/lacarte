<?php
namespace rtens\lacarte\utils;

use watoki\collections\Map;
use watoki\curir\Renderer;

class CsvRenderer implements Renderer {

    static $CLASS = __CLASS__;

    /**
     * @param array|object|Map $model The view model
     * @throws \InvalidArgumentException
     * @return string The rendered template
     */
    public function render($model) {
        if (!isset($model['content'])) {
            throw new \InvalidArgumentException('Model needs to have a key "content".');
        }

        $content = $model['content'];

        $headers = array();
        foreach ($content as $row) {
            $headers = array_unique(array_merge($headers, array_keys($row)));
        }

        $file = 'file.csv';
        $fp = fopen($file, 'w');

        fputcsv($fp, $headers);

        foreach ($content as $row) {
            $fields = array();
            foreach ($headers as $header) {
                $fields[] = isset($row[$header]) ? $row[$header] : null;
            }
            fputcsv($fp, $fields);
        }

        fclose($fp);

        $content = file_get_contents($file);
        @unlink($file);

        return $content;
    }
}