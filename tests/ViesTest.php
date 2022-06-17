<?php

namespace matejch\VatIdChecker\tests;

use matejch\VatIdChecker\Vies;
use PHPUnit\Framework\TestCase;

class ViesTest extends TestCase
{
    /** @test
     *
     * @dataProvider incorrectVats
     * @param $vat
     */
    public function it_returns_false_with_incorrect_vat($vat)
    {
        $service = new Vies();
        $this->assertFalse($service->validateVat($vat));
    }

    /** @test
     * @dataProvider incorrectVats
     * @param $vat
     */
    public function it_returns_error_message_with_incorrect_vat($vat)
    {
        $service = new Vies();
        $service->validateVat($vat);

        $this->assertEquals("Invalid Vat ID Format: $vat", $service->getMessage());
    }

    /** @test */
    public function it_returns_false_with_invalid_soap_address()
    {
        $service = new Vies();
        $service->setUrl('https://ec.europa.eu/taxation_customs/vies/checkVervice.wsdl');

        $this->assertFalse($service->validateVat('U12345678'));
    }

    /** @test */
    public function it_returns_error_message_with_invalid_soap_address()
    {
        $service = new Vies();
        $service->setUrl('https://ec.europa.eu/taxation_customs/vies/checkVervice.wsdl');

        $service->validateVat('U12345678');

        $this->assertSame('SOAP-ERROR: Parsing WSDL: Couldn\'t load from \'https://ec.europa.eu/taxation_customs/vies/checkVervice.wsdl\' : failed to load external entity "https://ec.europa.eu/taxation_customs/vies/checkVervice.wsdl"', $service->getMessage());
    }

    /** @test */
    public function it_returns_success_message()
    {
        $service = new Vies();

        $result = $service->validateVat('SK-7020000438');

        $this->assertIsObject($result);

        $this->assertSame('Success', $service->getMessage());
    }

    /** @test */
    public function it_returns_object_with_data_about_vat_holder()
    {
        $service = new Vies();
        $result = $service->validateVat('SK7020000438');

        $this->assertIsObject($result);

        $this->assertTrue($result->valid);
    }

    /** @test */
    public function it_returns_object_with_invalid_attribute()
    {
        $service = new Vies();
        $result = $service->validateVat('DE 123456789');

        $this->assertIsObject($result);

        $this->assertFalse($result->valid);
    }

    public function incorrectVats(): array
    {
        return [
            ['1213354333333'],
            ['AASDSA'],
            ['GGG123456789'],
            ['98765432114A7'],
            ['ASD2AS4D651'],
        ];
    }
}
