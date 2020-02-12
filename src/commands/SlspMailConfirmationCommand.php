<?php

namespace Crm\SlspSporopayModule\Commands;

use Crm\PaymentsModule\MailConfirmation\MailProcessor;
use Crm\SlspSporopayModule\MailConfirmation\SlspMailDownloader;
use Crm\SlspSporopayModule\MailConfirmation\SlspNotificationMailDownloader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SlspMailConfirmationCommand extends Command
{
    private $notificationMailDownloader;

    private $mailDownloader;

    private $mailProcessor;

    public function __construct(
        SlspNotificationMailDownloader $notificationMailDownloader,
        SlspMailDownloader $mailDownloader,
        MailProcessor $mailProcessor
    ) {
        parent::__construct();
        $this->notificationMailDownloader = $notificationMailDownloader;
        $this->mailDownloader = $mailDownloader;
        $this->mailProcessor = $mailProcessor;
    }

    protected function configure()
    {
        $this->setName('slsp:mail_confirmation')
            ->setDescription('Check notification emails and confirm payments based on SLSP emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->mailDownloader->download(function ($mailContent) use ($output) {
            return $this->markMailProcessed($mailContent, $output);
        });

        $this->notificationMailDownloader->download(function ($mailContent) use ($output) {
            return $this->markMailProcessed($mailContent, $output);
        });

        return 0;
    }

    private function markMailProcessed($mailContent, $output)
    {
        return !$this->mailProcessor->processMail($mailContent, $output);
    }
}
