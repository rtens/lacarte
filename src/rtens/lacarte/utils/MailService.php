<?php
namespace rtens\lacarte\utils;

class MailService {

    static $CLASS = __CLASS__;

    public function send($from, $to, $subject, $body) {
        mail($to, $subject, $body, "From: $from");
    }

}