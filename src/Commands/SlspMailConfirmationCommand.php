<?php

namespace Crm\SlspSporopayModule\Commands;

use Crm\ApplicationModule\Models\Config\ApplicationConfig;
use Crm\PaymentsModule\Models\MailConfirmation\EmailInterface;
use Crm\PaymentsModule\Models\MailConfirmation\MailDownloaderInterface;
use Crm\PaymentsModule\Models\MailConfirmation\MailProcessor;
use Crm\SlspSporopayModule\Models\MailParser\SlspMailParser;
use Crm\SlspSporopayModule\Models\MailParser\SlspNotificationMailParser;
use Nette\Utils\FileSystem;
use Nette\Utils\Random;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tomaj\ImapMailDownloader\MailCriteria;
use Tracy\Debugger;
use Tracy\ILogger;

class SlspMailConfirmationCommand extends Command
{
    private OutputInterface $output;

    public function __construct(
        private string $confirmationTmpDir,
        private MailDownloaderInterface $mailDownloader,
        private MailProcessor $mailProcessor,
        private ApplicationConfig $applicationConfig,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('slsp:mail_confirmation')
            ->setDescription('Check notification emails and confirm payments based on SLSP emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $connectionOptions = [
            'imapHost' => $this->applicationConfig->get('slsp_confirmation_host'),
            'imapPort' => $this->applicationConfig->get('slsp_confirmation_port'),
            'username' => $this->applicationConfig->get('slsp_confirmation_username'),
            'password' => $this->applicationConfig->get('slsp_confirmation_password'),
            'processedFolder' => $this->applicationConfig->get('slsp_confirmation_processed_folder'),
        ];

        $criteria = new MailCriteria();
        $criteria->setFrom('vypis@slsp.sk');
        $criteria->setSubject('Informacia o realizacii platby');
        $criteria->setUnseen(true);
        $connectionOptions['criteria'] = $criteria;

        $this->mailDownloader->download($connectionOptions, function (EmailInterface $email) {
            $slspMailParser = new SlspMailParser();

            $mailContent = $slspMailParser->parse(quoted_printable_decode($email->getBody()));
            if ($mailContent !== null) {
                $this->mailProcessor->processMail($mailContent, $this->output);
            }
        });

        $connectionOptions = [
            'imapHost' => $this->applicationConfig->get('slsp_notification_confirmation_host'),
            'imapPort' => $this->applicationConfig->get('slsp_notification_confirmation_port'),
            'username' => $this->applicationConfig->get('slsp_notification_confirmation_username'),
            'password' => $this->applicationConfig->get('slsp_notification_confirmation_password'),
            'processedFolder' => $this->applicationConfig->get('slsp_notification_confirmation_processed_folder'),
        ];

        $criteria = new MailCriteria();
        $criteria->setFrom('notifikacie@slsp.sk');
        $criteria->setSubject('Notifikacia');
        $criteria->setUnseen(true);

        $options = array_merge($connectionOptions, ['criteria' => $criteria]);
        $this->mailDownloader->download($options, function (EmailInterface $email) {
            $parser = new SlspNotificationMailParser();

            $body = $this->getAttachmentBody($email);
            $mailContent = $parser->parse(quoted_printable_decode($body));
            if ($mailContent !== null && $body) {
                $this->mailProcessor->processMail($mailContent, $this->output);
            }
        });

        return Command::SUCCESS;
    }

    private function validateAttachment(EmailInterface $email): bool
    {
        $attachments = $email->getAttachments();
        if (empty($attachments)) {
            Debugger::log(
                'missing slsp notification mail attachment for payment sent on: ' . $email->getDate(),
                ILogger::WARNING
            );
            return false;
        }
        return true;
    }

    private function getAttachmentBody(EmailInterface $email): bool|string
    {
        if (!$this->validateAttachment($email)) {
            return false;
        }

        $filePath = $this->confirmationTmpDir . DIRECTORY_SEPARATOR . 'payments-mail-parser/' . Random::generate() . '.zip';

        FileSystem::write($filePath, array_values($email->getAttachments())[0]['attachment']);

        $zip = new \ZipArchive();
        $zip->open($filePath);
        $contents = $zip->getFromIndex(0);

        $contents = iconv("ISO-8859-1", "UTF-8", $contents);

        FileSystem::delete($filePath);

        return $contents;
    }
}
