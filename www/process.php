<?php

use Paynl\Payment;
use Paynl\Api\Payment\Model;

require_once '../config.php';
try {

    if (!isset($_POST['pay_encrypted_data'])) {
        throw new RuntimeException('Missing payload data');
    }

    $payload = json_decode($_POST['pay_encrypted_data'], true);

    $transaction = new Model\Authenticate\Transaction();
    $transaction
        ->setServiceId(\Paynl\Config::getServiceId())
        ->setDescription('Lorem Ipsum')
        ->setReference('TEST.1234')
        ->setAmount(1)
        ->setCurrency('EUR')
        ->setIpAddress($_SERVER['REMOTE_ADDR'])
        ->setLanguage('NL')
        ->setFinishUrl(RETURN_URL);

    $address = new Model\Address();
    $address
        ->setStreetName('Street')
        ->setStreetNumber('10')
        ->setStreetNumberExtension('')
        ->setZipCode('1234AA')
        ->setCity('Amsterdam')
        ->setRegionCode('OV')
        ->setCountryCode('NL');

    $invoice = new Model\Invoice();
    $invoice
        ->setFirstName('Robert')
        ->setLastName('Van der Werf')
        ->setGender('M')
        ->setAddress($address);

    $customer = new Model\Customer();
    $customer
        ->setFirstName('Robert')
        ->setLastName('Van der Werf')
        ->setEmailAddress('robert.van.der.werf@pay.nl')
        ->setAddress($address)
        ->setInvoice($invoice);

    $cse = new Model\CSE();
    $cse
        ->setIdentifier($payload['identifier'])
        ->setData($payload['data']);

    $browser = new Model\Browser();
    $browser
        ->setJavaEnabled('false')
        ->setJavascriptEnabled('false')
        ->setLanguage('nl-NL')
        ->setColorDepth('24')
        ->setScreenWidth('1920')
        ->setScreenHeight('1080')
        ->setTz('-120');

    $result = Payment::authenticate(
        $transaction,
        $customer,
        $cse,
        $browser
    )->getData();

} catch (Exception $e) {
    $result = array(
        'result' => 0,
        'errorMessage' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($result);
