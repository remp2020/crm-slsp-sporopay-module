<?php

namespace Crm\SlspSporopayModule\MailParser;

use Tomaj\BankMailsParser\MailContent;
use Tomaj\BankMailsParser\Parser\ParserInterface;

class SlspMailParser implements ParserInterface
{
    public function parse(string $content): ?MailContent
    {
        $results = [];
        $res = preg_match('/\d{6};(\d{10});\d{4};([0-9.]+);([A-Z]+);(\d{10});(\d{10})/m', $content, $results);
        if ($res) {
            $mailContent = new MailContent();
            $mailContent->setAccountNumber($results[1]);
            $mailContent->setAmount(floatval($results[2]));
            $mailContent->setCurrency($results[3]);
            $mailContent->setVs($results[4]);
            $mailContent->setTransactionDate(time());

            return $mailContent;
        }

        return null;
    }
}
