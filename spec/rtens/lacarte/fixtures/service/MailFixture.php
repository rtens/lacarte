<?php
namespace spec\rtens\lacarte\fixtures\service;

use rtens\lacarte\utils\MailService;
use rtens\mockster\Mock;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\Specification;
use watoki\factory\Factory;
use watoki\scrut\Fixture;

class MailFixture extends Fixture {

    public static $CLASS = __CLASS__;

    /** @var UserFixture */
    private $user;

    /** @var Mock */
    private $service;

    public function __construct(Specification $spec, Factory $factory) {
        parent::__construct($spec, $factory);

        $this->service = $spec->mockFactory->createMock(MailService::$CLASS);
        $factory->setSingleton(MailService::$CLASS, $this->service);
        $this->user = $spec->useFixture(UserFixture::$CLASS);
    }

    public function thenAMailShouldBeSentTo_WithTheSubject_AndTheBody($userName, $subject, $body) {
        $email = $this->user->getUser($userName)->getEmail();
        $send = $this->service->__mock()->method('send');

        for ($i = 0; $i < $send->getCalledCount(); $i++) {
            if ($send->getCalledArgumentAt($i, 'to') == $email) {
                $this->spec->assertEquals($subject, $send->getCalledArgumentAt($i, 'subject'));
                $this->spec->assertEquals($body, $send->getCalledArgumentAt($i, 'body'));
                return;
            }
        }
        $this->spec->fail("No mail was sent to $userName");
    }

    public function then_EmailsShouldBeSent($int) {
        $this->spec->assertEquals($int, $this->service->__mock()->method('send')->getCalledCount());
    }

    public function givenAnError_OccursWhileSendingTheMail($string) {
        $this->service->__mock()->method('send')->willThrow(new \Exception($string));
    }


}