<?php
namespace rtens\lacarte\web\user\avatars;
 
use rtens\lacarte\core\FileRepository;
use watoki\collections\Map;
use watoki\curir\controller\Module;
use watoki\curir\Path;
use watoki\curir\Request;
use watoki\factory\Factory;

class AvatarsModule extends Module {

    private $files;

    function __construct(Factory $factory, Path $route, Module $parent = null, FileRepository $files) {
        parent::__construct($factory, $route, $parent);
        $this->files = $files;
    }

    public function respond(Request $request) {
        $file = 'avatars/' . $request->getResource()->toString();
        if ($this->files->exists($file)) {
            return $this->createFileResponse($this->files->getFullPath($file),
                $request->getResource()->getLeafExtension());
        }

        return parent::respond($request);
    }

}
