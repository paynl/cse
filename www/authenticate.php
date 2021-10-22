<?php

use Paynl\Payment;
use Paynl\Api\Payment\Model;

require_once '../config.php';

try {
    if (!isset($_POST['pay_encrypted_data'])) {
        throw new Exception('Missing payload');
    }

    $payload = json_decode($_POST['pay_encrypted_data'], true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid json');
    }

    if (isset($_POST['transaction_id'])) {
        $transaction = new Model\Authenticate\TransactionMethod();
        $transaction
            ->setOrderId($_POST['transaction_id'])
            ->setEntranceCode($_POST['entrance_code']);

    } else {
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
    }

    $cse = new Model\CSE();
    $cse
        ->setIdentifier($payload['identifier'])
        ->setData($payload['data']);

    $payment = new Model\Payment();
    $payment
        ->setMethod(Model\Payment::METHOD_CSE)
        ->setCse($cse);

    if (isset($_POST['threeds_transaction_id'])) {
        $auth = new Model\Auth();
        $auth
            ->setPayTdsAcquirerId(134) //$_POST['acquirer_id'])
            ->setPayTdsTransactionId($_POST['threeds_transaction_id']);

        $payment->setAuth($auth);
    }

    $browser = new Model\Browser();
    $browser
        ->setJavaEnabled('false')
        ->setJavascriptEnabled('false')
        ->setLanguage('nl-NL')
        ->setColorDepth('24')
        ->setScreenWidth('1920')
        ->setScreenHeight('1080')
        ->setTz('-120');

    $result = Payment::authenticateMethod($transaction, $payment)->getData();

} catch (Exception $e) {
    $result = array(
        'result' => 0,
        'errorMessage' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($result);
