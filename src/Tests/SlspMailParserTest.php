<?php

namespace Crm\SlspSporopayModule\Tests;

use Crm\PaymentsModule\Tests\PaymentsTestCase;
use Crm\SlspSporopayModule\Models\MailParser\SlspMailParser;

class SlspMailParserTest extends PaymentsTestCase
{
    public function testSingleTransferPayment()
    {
        $email = '

000000;0112380000;0900;12.00;EUR;9911922000;0000000910;OK;OK;xDCBmygVheJfzKVXbtwxPSa2l1KYK+dT

';

        $slspMailParser = new SlspMailParser();
        $mailContent = $slspMailParser->parse($email);

        $this->assertEquals('0112380000', $mailContent->getAccountNumber());
        $this->assertEquals('EUR', $mailContent->getCurrency());
        $this->assertEquals(12.00, $mailContent->getAmount());
        $this->assertEquals('9911922000', $mailContent->getVs());
        $this->assertNull($mailContent->getSs());
    }
}
