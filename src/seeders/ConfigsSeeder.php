<?php

namespace Crm\SlspSporopayModule\Seeders;

use Crm\ApplicationModule\Builder\ConfigBuilder;
use Crm\ApplicationModule\Config\ApplicationConfig;
use Crm\ApplicationModule\Config\Repository\ConfigCategoriesRepository;
use Crm\ApplicationModule\Config\Repository\ConfigsRepository;
use Crm\ApplicationModule\Seeders\ISeeder;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigsSeeder implements ISeeder
{
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
        $sorting = 1500;

        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_sporopay_sharedsecret',
            'slsp_sporopay.config.sharedsecret.name',
            'Z3qY08EpvLlAAoMZdnyUdQ==',
            $sorting++,
            'slsp_sporopay.config.sharedsecret.description'
        );
        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_sporopay_accountnumberprefix',
            'slsp_sporopay.config.accountnumberprefix.name',
            '999999',
            $sorting++,
            'slsp_sporopay.config.accountnumberprefix.description'
        );
        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_sporopay_accountnumber',
            'slsp_sporopay.config.accountnumber.name',
            '1111111111',
            $sorting++,
            'slsp_sporopay.config.accountnumber.description'
        );
        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_sporopay_specificsymbol',
            'slsp_sporopay.config.specificsymbol.name',
            '0000000000',
            $sorting++,
            'slsp_sporopay.config.specificsymbol.description'
        );
        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_sporopay_mode',
            'slsp_sporopay.config.mode.name',
            'test',
            $sorting++,
            'slsp_sporopay.config.mode.description'
        );

        $categoryName = 'payments.config.category_confirmation';
        $category = $category = $this->configCategoriesRepository->loadByName($categoryName);
        if (!$category) {
            $category = $category = $this->configCategoriesRepository->add($categoryName, 'fa fa-check-double', 1600);
            $output->writeln('  <comment>* config category <info>Potvrdzovacie e-maily</info> created</comment>');
        } else {
            $output->writeln('  * config category <info>Potvrdzovacie e-maily</info> exists');
        }

        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_confirmation_host',
            'slsp_sporopay.config.slsp_confirmation_host.name',
            '',
            100,
            'slsp_sporopay.config.slsp_confirmation_host.description'
        );

        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_confirmation_port',
            'slsp_sporopay.config.slsp_confirmation_port.name',
            '',
            101,
            'slsp_sporopay.config.slsp_confirmation_port.description'
        );

        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_confirmation_username',
            'slsp_sporopay.config.slsp_confirmation_username.name',
            '',
            102,
            'slsp_sporopay.config.slsp_confirmation_username.description'
        );

        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_confirmation_password',
            'slsp_sporopay.config.slsp_confirmation_password.name',
            '',
            103,
            'slsp_sporopay.config.slsp_confirmation_password.description'
        );

        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_confirmation_processed_folder',
            'slsp_sporopay.config.slsp_confirmation_processed_folder.name',
            '',
            104,
            'slsp_sporopay.config.slsp_confirmation_processed_folder.description'
        );

        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_notification_confirmation_host',
            'slsp_sporopay.config.slsp_notification_confirmation_host.name',
            '',
            105,
            'slsp_sporopay.config.slsp_notification_confirmation_host.description'
        );

        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_notification_confirmation_port',
            'slsp_sporopay.config.slsp_notification_confirmation_port.name',
            '',
            106,
            'slsp_sporopay.config.slsp_notification_confirmation_port.description'
        );

        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_notification_confirmation_username',
            'slsp_sporopay.config.slsp_notification_confirmation_username.name',
            '',
            107,
            'slsp_sporopay.config.slsp_notification_confirmation_username.description'
        );

        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_notification_confirmation_password',
            'slsp_sporopay.config.slsp_notification_confirmation_password.name',
            '',
            108,
            'slsp_sporopay.config.slsp_notification_confirmation_password.description'
        );

        $this->addPaymentConfig(
            $output,
            $category,
            'slsp_notification_confirmation_processed_folder',
            'slsp_sporopay.config.slsp_notification_confirmation_processed_folder.name',
            '',
            109,
            'slsp_sporopay.config.slsp_notification_confirmation_processed_folder.description'
        );
    }

    private function addPaymentConfig(OutputInterface $output, $category, $name, $displayName, $value, $sorting, $description = null)
    {
        $config = $this->configsRepository->loadByName($name);
        if (!$config) {
            $this->configBuilder->createNew()
                ->setName($name)
                ->setDisplayName($displayName)
                ->setDescription($description)
                ->setValue($value)
                ->setType(ApplicationConfig::TYPE_STRING)
                ->setAutoload(true)
                ->setConfigCategory($category)
                ->setSorting($sorting)
                ->save();
            $output->writeln("  <comment>* config item <info>$name</info> created</comment>");
        } else {
            $output->writeln("  * config item <info>$name</info> exists");

            if ($config->has_default_value && $config->value !== $value) {
                $this->configsRepository->update($config, ['value' => $value, 'has_default_value' => true]);
                $output->writeln("  <comment>* config item <info>$name</info> updated</comment>");
            }

            if ($config->category->name != $category->name) {
                $this->configsRepository->update($config, [
                    'config_category_id' => $category->id
                ]);
                $output->writeln("  <comment>* config item <info>$name</info> updated</comment>");
            }
        }
    }
}
