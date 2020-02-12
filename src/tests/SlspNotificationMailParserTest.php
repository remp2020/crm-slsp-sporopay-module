<?php

namespace Crm\SlspSporopayModule\Tests;

use Crm\PaymentsModule\Tests\PaymentsTestCase;
use Crm\SlspSporopayModule\MailParser\SlspNotificationMailParser;

class SlspNotificationMailParserTest extends PaymentsTestCase
{
    public function testSingleTransferPayment()
    {
        $email = '

          ____________________________________________________________________
        1 Bezhotovostný vklad                    101219 101219         12.00
         SK0409000000000099123376 Novaj Jozef
         VS:9911929200 KS:0000000308 SS:0000000910
           
          ____________________________________________________________________
          Názov úètu: BU 7851276092
          Èas platnosti: 10.12.2019 17:50:29
          Vklady spolu: 12.00
          Výbery spolu: 0.00
          ____________________________________________________________________
          Zostatok: 168505.90
          Dispozostatok: 168505.90
          Vlastné zdroje: 168505.90
          ____________________________________________________________________

';

        $parser = new SlspNotificationMailParser();
        $mailContent = $parser->parse($email);

        $this->assertEquals('SK0409000000000099123376', $mailContent->getAccountNumber());
        $this->assertEquals(12.00, $mailContent->getAmount());
        $this->assertEquals('9911929200', $mailContent->getVs());
        $this->assertEquals('0000000308', $mailContent->getKs());
        $this->assertEquals('0000000910', $mailContent->getSs());
        $this->assertEquals(strtotime('10.12.2019 17:50:29'), $mailContent->getTransactionDate());
    }
}
