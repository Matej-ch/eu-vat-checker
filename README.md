![latest_tag](https://badgen.net/github/tag/Matej-ch/eu-vat-checker)

This package uses service https://ec.europa.eu/taxation_customs/vies/

### Install package

```
composer require "1.0.0" 
```

---

### Usage

```PHP 
/** EU VAT checker */
$service = new \matejch\VatIdChecker\Vies();
$result = $service->validateVat('XX1234567890'); //array with data or false

$service->getMessage(); // response message
```

### Remove package

```
composer remove 
```