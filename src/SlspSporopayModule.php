<?php

namespace Crm\SlspSporopayModule;

use Crm\ApplicationModule\CrmModule;
use Crm\ApplicationModule\SeederManager;
use Crm\SlspSporopayModule\Seeders\ConfigsSeeder;
use Crm\SlspSporopayModule\Seeders\PaymentGatewaysSeeder;

class SlspSporopayModule extends CrmModule
{
    public function registerSeeders(SeederManager $seederManager)
    {
        $seederManager->addSeeder($this->getInstance(ConfigsSeeder::class));
        $seederManager->addSeeder($this->getInstance(PaymentGatewaysSeeder::class));
    }
}
