<?php
namespace rtens\lacarte;
 
use rtens\lacarte\utils\CsvRenderer;
use watoki\tempan\Renderer;

class Presenter extends \watoki\curir\responder\Presenter {

    const UTF8_BOM = "\xEF\xBB\xBF";

    public function renderHtml($template) {
        $renderer = new Renderer($template);
        return $renderer->render($this->getModel());
    }

    public function renderJson() {
        return json_encode($this->getModel());
    }

    public function renderCsv() {
        $renderer = new CsvRenderer();
        return self::UTF8_BOM . $renderer->render($this->getModel());
    }

}
 