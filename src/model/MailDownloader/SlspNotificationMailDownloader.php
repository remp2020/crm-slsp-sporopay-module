<?php

namespace Crm\SlspSporopayModule\MailConfirmation;

use Crm\ApplicationModule\Config\ApplicationConfig;
use Crm\SlspSporopayModule\MailParser\SlspNotificationMailParser;
use Nette\Utils\FileSystem;
use Tomaj\ImapMailDownloader\Downloader;
use Tomaj\ImapMailDownloader\Email;
use Tomaj\ImapMailDownloader\MailCriteria;
use Tracy\Debugger;
use Tracy\ILogger;

class SlspNotificationMailDownloader
{
    private $tempDir;

    private $imapHost;

    private $imapPort;

    private $username;

    private $password;

    private $processedFolder;

    public function __construct($tempDir, ApplicationConfig $config)
    {
        $this->tempDir = $tempDir;

        $this->imapHost = $config->get('confirmation_mail_host');
        $this->imapPort = $config->get('confirmation_mail_port');
        $this->username = $config->get('confirmation_mail_username');
        $this->password = $config->get('confirmation_mail_password');
        $this->processedFolder = $config->get('confirmation_mail_processed_folder');
    }

    public function download($callback)
    {
        $downloader = new Downloader(
            $this->imapHost,
            $this->imapPort,
            $this->username,
            $this->password,
            $this->processedFolder
        );

        $criteria = new MailCriteria();
        $criteria->setFrom('notifikacie@slsp.sk');
        $criteria->setSubject('Notifikacia');
        $criteria->setUnseen(true);
        $downloader->fetch($criteria, function (Email $email) use ($callback) {
            $parser = new SlspNotificationMailParser();

            $body = $this->getAttachmentBody($email);
            $mailContent = $parser->parse(quoted_printable_decode($body));
            if (!empty($mailContent) && $body) {
                return $callback($mailContent);
            }

            return false;
        });
    }

    public function validateAttachment(Email $email)
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

    public function getAttachmentBody(Email $email)
    {
        if (!$this->validateAttachment($email)) {
            return false;
        }

        $filePath = $this->tempDir . DIRECTORY_SEPARATOR . 'payments-mail-parser/' . uniqid() . '.zip';

        FileSystem::write($filePath, array_values($email->getAttachments())[0]['attachment']);

        $zip = new \ZipArchive();
        $zip->open($filePath);
        $contents = $zip->getFromIndex(0);

        $contents = iconv("ISO-8859-1", "UTF-8", $contents);

        FileSystem::delete($filePath);

        return $contents;
    }
}
