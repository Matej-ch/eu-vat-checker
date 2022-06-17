<?php

namespace matejch\VatIdChecker;

use SoapClient;
use SoapFault;

class Vies
{
    /**
     * SOAP vat validation service for EU
     * @var string
     */
    private $url = 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    /**
     * Response message from service or when exception is thrown
     * @var string
     */
    private $message = '';

    /**
     * @return array|bool
     */
    public function validateVat(string $vatID)
    {
        try {

            $vatID = str_replace(['-', '.', ' '], '', $vatID);

            if (preg_match($this->getRegexVat(), trim($vatID)) !== 1) {
                throw new InvalidVatException("Invalid Vat ID Format: $vatID");
            }

            $client = new SoapClient($this->url);

            $params = [
                'countryCode' => substr($vatID, 0, 2),
                'vatNumber' => substr($vatID, 2),
            ];

            $result = $client->checkVatApprox($params);
            $this->setMessage('Success');
        } catch (InvalidVatException|SoapFault $e) {
            $this->setMessage(trim($e->getMessage()));
            $result = false;
        }

        return $result;
    }

    /**
     * Set new url for validation service if necessary
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    private function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * Regex for VAT validation, for EU countries
     *
     * @return string
     */
    private function getRegexVat(): string
    {
        return "/^((AT)?U[0-9]{8}|(BE)?0[0-9]{9}|(BG)?[0-9]{9,10}|(CY)?[0-9]{8}L|(CZ)?[0-9]{8,10}|(DE)?[0-9]{9}|(DK)?[0-9]{8}|(EE)?[0-9]{9}|(EL|GR)?[0-9]{9}|(ES)?[0-9A-Z][0-9]{7}[0-9A-Z]|(FI)?[0-9]{8}|(FR)?[0-9A-Z]{2}[0-9]{9}|(GB)?([0-9]{9}([0-9]{3})?|[A-Z]{2}[0-9]{3})|(HU)?[0-9]{8}|(IE)?[0-9]S[0-9]{5}L|(IT)?[0-9]{11}|(LT)?([0-9]{9}|[0-9]{12})|(LU)?[0-9]{8}|(LV)?[0-9]{11}|(MT)?[0-9]{8}|(NL)?[0-9]{9}B[0-9]{2}|(PL)?[0-9]{10}|(PT)?[0-9]{9}|(RO)?[0-9]{2,10}|(SE)?[0-9]{12}|(SI)?[0-9]{8}|(SK)?[0-9]{10})$/i";
    }
}