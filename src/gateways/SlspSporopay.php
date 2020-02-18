<?php

namespace Crm\SlspSporopayModule\Gateways;

use Crm\PaymentsModule\Gateways\GatewayAbstract;
use Omnipay\SporoPay\Gateway;
use Omnipay\Omnipay;
use Omnipay\SporoPay\Message\PurchaseRequest;

class SlspSporopay extends GatewayAbstract
{
    const GATEWAY_CODE = 'slsp_sporopay';

    /** @var Gateway */
    protected $gateway;

    protected function initialize()
    {
        $this->gateway = Omnipay::create('SporoPay');

        $this->gateway->setSharedSecret($this->applicationConfig->get('slsp_sporopay_sharedsecret'));
        $this->gateway->setTestMode(!($this->applicationConfig->get('slsp_sporopay_accountnumber') == 'live'));
    }

    public function begin($payment)
    {
        $this->initialize();

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->gateway->purchase();
        $purchaseRequest->setVs($payment->variable_symbol)
            ->setAmount($payment->amount)
            ->setRurl($this->generateReturnUrl($payment))
            ->setAccountNumberPrefix($this->applicationConfig->get('slsp_sporopay_accountnumberprefix'))
            ->setAccountNumber($this->applicationConfig->get('slsp_sporopay_accountnumber'))
            ->setSs($this->applicationConfig->get('slsp_sporopay_specificsymbol'))
            ->setParam($payment->variable_symbol);

        $this->response = $purchaseRequest->send();
    }

    public function complete($payment): ?bool
    {
        $this->initialize();
        $this->response = $this->gateway->completePurchase()->send();

        return $this->response->isSuccessful();
    }
}