<?php

namespace Crm\SlspSporopayModule\MailParser;

use Tomaj\BankMailsParser\MailContent;
use Tomaj\BankMailsParser\Parser\ParserInterface;

class SlspNotificationMailParser implements ParserInterface
{
    /**
     * @param $content
     * @return MailContent
     */
    public function parse($content)
    {
        $mailContent = new MailContent();
        $result = [];

        $pattern1 = '/([A-Z]{2}\d{22})/m';
        $res = preg_match($pattern1, $content, $result);
        if ($res) {
            $mailContent->setAccountNumber($result[1]);
        }

        $pattern2 = '/VS:(\d{10})/m';
        $res = preg_match($pattern2, $content, $result);
        if ($res) {
            $mailContent->setVs($result[1]);
        }

        $pattern3 = '/Vklady spolu:.*?([0-9\.]+)/m';
        $res = preg_match($pattern3, $content, $result);
        if ($res) {
            $mailContent->setAmount(floatval($result[1]));
        }

        $pattern4 = '/KS:(\d{10})/m';
        $res = preg_match($pattern4, $content, $result);
        if ($res) {
            $mailContent->setKs($result[1]);
        }

        $pattern5 = '/Èas platnosti:.*?([0-9\. :]+)/m';
        $res = preg_match($pattern5, $content, $result);
        if ($res) {
            $mailContent->setTransactionDate(strtotime($result[1]));
        }

        $pattern5 = '/SS:(\d{10})/m';
        $res = preg_match($pattern5, $content, $result);
        if ($res) {
            $mailContent->setSs($result[1]);
        }

        return $mailContent;
    }
}
