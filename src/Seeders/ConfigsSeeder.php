<?php

namespace Crm\SlspSporopayModule\Seeders;

use Crm\ApplicationModule\Builder\ConfigBuilder;
use Crm\ApplicationModule\Models\Config\ApplicationConfig;
use Crm\ApplicationModule\Repositories\ConfigCategoriesRepository;
use Crm\ApplicationModule\Repositories\ConfigsRepository;
use Crm\ApplicationModule\Seeders\ConfigsTrait;
use Crm\ApplicationModule\Seeders\ISeeder;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigsSeeder implements ISeeder
{
    use ConfigsTrait;

    private $configCategoriesRepository;

    private $configsRepository;

    private $configBuilder;

    public function __construct(
        ConfigCategoriesRepository $configCategoriesRepository,
        ConfigsRepository $configsRepository,
        ConfigBuilder $configBuilder
    ) {
        $this->configCategoriesRepository = $configCategoriesRepository;
        $this->configsRepository = $configsRepository;
        $this->configBuilder = $configBuilder;
    }

    public function seed(OutputInterface $output)
    {
        $category = $this->configCategoriesRepository->findBy('name', 'payments.config.category');
        $sorting = 1501;

        $this->addConfig(
            $output,
            $category,
            'slsp_sporopay_sharedsecret',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.sharedsecret.name',
            'slsp_sporopay.config.sharedsecret.description',
            'Z3qY08EpvLlAAoMZdnyUdQ==',
            $sorting++
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_sporopay_accountnumberprefix',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.accountnumberprefix.name',
            'slsp_sporopay.config.accountnumberprefix.description',
            '999999',
            $sorting++
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_sporopay_accountnumber',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.accountnumber.name',
            'slsp_sporopay.config.accountnumber.description',
            '1111111111',
            $sorting++
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_sporopay_specificsymbol',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.specificsymbol.name',
            'slsp_sporopay.config.specificsymbol.description',
            '0000000000',
            $sorting++
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_sporopay_mode',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.mode.name',
            'slsp_sporopay.config.mode.description',
            'test',
            $sorting++
        );

        $category = $this->configCategoriesRepository->loadByName('payments.config.category_confirmation');
        if (!$category) {
            $category = $this->configCategoriesRepository->add('payments.config.category_confirmation', 'fa fa-check-double', 1600);
            $output->writeln('  <comment>* config category <info>Potvrdzovacie e-maily</info> created</comment>');
        } else {
            $output->writeln('  * config category <info>Potvrdzovacie e-maily</info> exists');
        }

        $this->addConfig(
            $output,
            $category,
            'slsp_confirmation_host',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.slsp_confirmation_host.name',
            'slsp_sporopay.config.slsp_confirmation_host.description',
            null,
            1101
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_confirmation_port',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.slsp_confirmation_port.name',
            'slsp_sporopay.config.slsp_confirmation_port.description',
            null,
            1102
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_confirmation_username',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.slsp_confirmation_username.name',
            'slsp_sporopay.config.slsp_confirmation_username.description',
            null,
            1103
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_confirmation_password',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.slsp_confirmation_password.name',
            'slsp_sporopay.config.slsp_confirmation_password.description',
            null,
            1104
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_confirmation_processed_folder',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.slsp_confirmation_processed_folder.name',
            'slsp_sporopay.config.slsp_confirmation_processed_folder.description',
            null,
            1105
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_notification_confirmation_host',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.slsp_notification_confirmation_host.name',
            'slsp_sporopay.config.slsp_notification_confirmation_host.description',
            null,
            1201
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_notification_confirmation_port',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.slsp_notification_confirmation_port.name',
            'slsp_sporopay.config.slsp_notification_confirmation_port.description',
            null,
            1202
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_notification_confirmation_username',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.slsp_notification_confirmation_username.name',
            'slsp_sporopay.config.slsp_notification_confirmation_username.description',
            null,
            1203
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_notification_confirmation_password',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.slsp_notification_confirmation_password.name',
            'slsp_sporopay.config.slsp_notification_confirmation_password.description',
            null,
            1204
        );

        $this->addConfig(
            $output,
            $category,
            'slsp_notification_confirmation_processed_folder',
            ApplicationConfig::TYPE_STRING,
            'slsp_sporopay.config.slsp_notification_confirmation_processed_folder.name',
            'slsp_sporopay.config.slsp_notification_confirmation_processed_folder.description',
            null,
            1205
        );
    }
}
