<?php

namespace Crm\SlspSporopayModule;

use Crm\ApplicationModule\Application\CommandsContainerInterface;
use Crm\ApplicationModule\Application\Managers\SeederManager;
use Crm\ApplicationModule\CrmModule;
use Crm\SlspSporopayModule\Commands\SlspMailConfirmationCommand;
use Crm\SlspSporopayModule\Seeders\ConfigsSeeder;
use Crm\SlspSporopayModule\Seeders\PaymentGatewaysSeeder;

class SlspSporopayModule extends CrmModule
{
    public function registerSeeders(SeederManager $seederManager)
    {
        $seederManager->addSeeder($this->getInstance(ConfigsSeeder::class));
        $seederManager->addSeeder($this->getInstance(PaymentGatewaysSeeder::class));
    }

    public function registerCommands(CommandsContainerInterface $commandsContainer)
    {
        $commandsContainer->registerCommand($this->getInstance(SlspMailConfirmationCommand::class));
    }
}
