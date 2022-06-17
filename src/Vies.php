<?php

namespace matejch\VatIdChecker;

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
     * Regex for VAT validation, for EU countries
     * @return bool
     * @var string
     */
    private $vatIdRegex = "/^[a-z]{2}[a-z0-9]{0,12}$/i";

    public function validateVat($vatID): bool
    {
        $result = false;
        try {

            if (preg_match($this->vatIdRegex, $vatID) !== 1) {
                throw new InvalidVatException("Invalid Vat ID Format: $vatID");
            }

            $client = new \SoapClient($this->url);

            $params = [
                'countryCode' => substr($vatID, 0, 2),
                'vatNumber' => substr($vatID, 2),
            ];

            $result = $client->checkVatApprox($params);

            $result = true;
        } catch (InvalidVatException $e) {
            $this->setMessage('Invalid VAT number format');
            return false;
        } catch (SoapFault $e) {
            $this->setMessage("ec.europa.eu Vies service is currently unavailable: " . $e->getMessage());
            return false;
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
}