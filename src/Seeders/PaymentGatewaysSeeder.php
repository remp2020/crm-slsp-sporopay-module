<?php

namespace Crm\SlspSporopayModule\Seeders;

use Crm\ApplicationModule\Seeders\ISeeder;
use Crm\PaymentsModule\Repositories\PaymentGatewaysRepository;
use Crm\SlspSporopayModule\Gateways\SlspSporopay;
use Symfony\Component\Console\Output\OutputInterface;

class PaymentGatewaysSeeder implements ISeeder
{
    private $paymentGatewaysRepository;
    
    public function __construct(PaymentGatewaysRepository $paymentGatewaysRepository)
    {
        $this->paymentGatewaysRepository = $paymentGatewaysRepository;
    }

    public function seed(OutputInterface $output)
    {
        $code = SlspSporopay::GATEWAY_CODE;
        if (!$this->paymentGatewaysRepository->exists($code)) {
            $this->paymentGatewaysRepository->add(
                'SLSP Sporopay',
                $code,
                110,
                true
            );
            $output->writeln("  <comment>* payment type <info>{$code}</info> created</comment>");
        } else {
            $output->writeln("  * payment type <info>{$code}</info> exists");
        }
    }
}
