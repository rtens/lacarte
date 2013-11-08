<?php


namespace rtens\lacarte\web\export;


use DateTime;
use rtens\lacarte\core\Session;
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\UserInteractor;
use rtens\lacarte\utils\CsvRenderer;
use rtens\lacarte\web\DefaultComponent;
use watoki\curir\controller\Module;
use watoki\curir\Path;
use watoki\curir\Request;
use watoki\factory\Factory;

class IcalComponent extends DefaultComponent {

    static $CLASS = __CLASS__;

    private $orderInteractor;

    public $request;

    function __construct(Factory $factory,
                         Path $route,
                         Module $parent = null,
                         UserInteractor $userInteractor,
                         Session $session,
                         OrderInteractor $orderInteractor,
                         Request $request
    ) {
        parent::__construct($factory, $route, $parent, $userInteractor, $session);
        $this->userInteractor = $userInteractor;
        $this->orderInteractor = $orderInteractor;
        $this->session = $session;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function doGet($icalkey) {
//        $user = $this->userInteractor->readByKey($icalkey);

        $user = $this->userInteractor->readByKey('0ea9356eaf66b98ff1b86defdab8d172');

//        for ($i = 0; $i < 7; $i++ ) {
//            if ($i == 0) {
//                $day = 'today';
//            } else {
//                $day = "+$i days";
//            }
        $date = new DateTime('now');
                $selections = $this->orderInteractor->readByUserAndDate($user->id, $date);
//            }
//            var_dump($user->id, $date, $selections);

//        }

        die();
        return $this->redirect('www.google.de');
    }

}