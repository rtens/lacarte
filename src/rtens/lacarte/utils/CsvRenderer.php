<?php
namespace rtens\lacarte\utils;

use watoki\collections\Map;

class CsvRenderer {

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

        $fp = tmpfile();
        $delimiter = $this->delimiter();

        fputcsv($fp, $headers, $delimiter);

        foreach ($content as $row) {
            $fields = array();
            foreach ($headers as $header) {
                $fields[] = isset($row[$header]) ? $row[$header] : null;
            }
            fputcsv($fp, $fields, $delimiter);
        }

        fseek($fp, 0);
        $content = stream_get_contents($fp);

        fclose($fp);
        return $content;
    }

    /**
     * @return string
     */
    protected function delimiter() {
        return ';';
    }
}