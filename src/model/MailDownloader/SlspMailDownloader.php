<?php

namespace Crm\SlspSporopayModule\MailConfirmation;

use Crm\ApplicationModule\Config\ApplicationConfig;
use Crm\SlspSporopayModule\MailParser\SlspMailParser;
use Tomaj\ImapMailDownloader\Downloader;
use Tomaj\ImapMailDownloader\Email;
use Tomaj\ImapMailDownloader\MailCriteria;

class SlspMailDownloader
{
    private $imapHost;

    private $imapPort;

    private $username;

    private $password;

    private $processedFolder;

    public function __construct(ApplicationConfig $config)
    {
        $this->imapHost = $config->get('slsp_confirmation_host');
        $this->imapPort = $config->get('slsp_confirmation_port');
        $this->username = $config->get('slsp_confirmation_username');
        $this->password = $config->get('slsp_confirmation_password');
        $this->processedFolder = $config->get('slsp_confirmation_processed_folder');
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
        $criteria->setFrom('vypis@slsp.sk');
        $criteria->setSubject('Informacia o realizacii platby');
        $criteria->setUnseen(true);
        $downloader->fetch($criteria, function (Email $email) use ($callback) {
            $slspMailParser = new SlspMailParser();

            $mailContent = $slspMailParser->parse(quoted_printable_decode($email->getBody()));
            if (!empty($mailContent)) {
                return $callback($mailContent);
            }

            return false;
        });
    }
}
