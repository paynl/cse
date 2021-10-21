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
        ->setStreetName('Minister Treubstraat')
        ->setStreetNumber('10')
        ->setStreetNumberExtension('')
        ->setZipCode('7522BA')
        ->setCity('Enschede')
        ->setRegionCode('OV')
        ->setCountryCode('NL');

    $invoice = new Model\Invoice();
    $invoice
        ->setFirstName('Henk')
        ->setLastName('de Vries')
        ->setGender('M')
        ->setAddress($address);

    $customer = new Model\Customer();
    $customer
        ->setFirstName('Foo')
        ->setLastName('Bar')
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

/*
 * {"result":"0","errorTag":"acquirer_ExpiredCard","errorCode":"2802","errorMessage":"Het is helaas niet mogelijk om de betaling te voltooien. De door u gebruikte kaart is verlopen. Probeer het nogmaals met een andere kaart.","nextAction":"retryChallenge","transactionID":"f4d706dc-7d89-4d48-a757-3f07d0ab5876","orderId":"1602461478Xffc0b","entranceCode":"6af7668093064f35908537a4ebdfd7599318e1a2"}
 */

header('content-type: application/json');
echo json_encode($result);
