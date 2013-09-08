<?php
namespace spec\rtens\lacarte\fixtures\service;

use rtens\lacarte\utils\MailService;
use rtens\mockster\Mock;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixtures\Fixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

class MailFixture extends Fixture {

    public static $CLASS = __CLASS__;

    /** @var Mock */
    private $service;

    public function __construct(TestCase $test, Factory $factory, UserFixture $user) {
        parent::__construct($test, $factory);
        $this->service = $this->mockFactory->createMock(MailService::$CLASS);
        $factory->setSingleton(MailService::$CLASS, $this->service);
        $this->user = $user;
    }

    public function thenAMailShouldBeSentTo_WithTheSubject_AndTheBody($userName, $subject, $body) {
        $email = $this->user->getUser($userName)->getEmail();
        $send = $this->service->__mock()->method('send');

        for ($i = 0; $i < $send->getCalledCount(); $i++) {
            if ($send->getCalledArgumentAt($i, 'to') == $email) {
                $this->test->assertEquals($subject, $send->getCalledArgumentAt($i, 'subject'));
                $this->test->assertEquals($body, $send->getCalledArgumentAt($i, 'body'));
                return;
            }
        }
        $this->test->fail("No mail was sent to $userName");
    }

    public function then_EmailsShouldBeSent($int) {
        $this->test->assertEquals($int, $this->service->__mock()->method('send')->getCalledCount());
    }

    public function givenAnError_OccursWhileSendingTheMail($string) {
        $this->service->__mock()->method('send')->willThrow(new \Exception($string));
    }


}