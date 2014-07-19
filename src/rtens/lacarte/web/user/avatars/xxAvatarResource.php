<?php
namespace rtens\lacarte\web\user\avatars;
 
use watoki\curir\http\MimeTypes;
use watoki\curir\http\Request;
use watoki\curir\http\Response;
use watoki\curir\resource\DynamicResource;
use watoki\curir\resource;
use watoki\curir\Responder;

class xxAvatarResource extends DynamicResource {

    /** @var \rtens\lacarte\core\FileRepository <- */
    private $files;

    protected function getPlaceholderKey() {
        return 'user';
    }

    public function doGet($user) {
        return new FilePresenter($this->files->getFullPath('avatars'), $user);
    }

}

class FilePresenter extends Responder {

    private $directory;

    private $name;

    public function __construct($directory, $name) {
        $this->directory = $directory;
        $this->name = $name;
    }

    /**
     * @param \watoki\curir\http\Request $request
     * @return \watoki\curir\http\Response
     */
    public function createResponse(Request $request) {
        $formats = $request->getFormats();

        $file = $this->directory . DIRECTORY_SEPARATOR . $this->name . '.' . $formats[0];
        if (file_exists($file)) {
            $response = new Response(file_get_contents($file));
            $response->getHeaders()->set(Response::HEADER_CONTENT_TYPE, MimeTypes::getType($formats[0]));
            return $response;
        }

        $response = new Response();
        $response->setStatus(Response::STATUS_NOT_FOUND);
        return $response;
    }
}
