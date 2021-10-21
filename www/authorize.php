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

    $transaction = new Model\Authorize\Transaction();
    $transaction
        ->setOrderId($_POST['transaction_id'])
        ->setEntranceCode($_POST['entrance_code']);

    $cse = new Model\CSE();
    $cse
        ->setIdentifier($payload['identifier'])
        ->setData($payload['data']);

    $auth = new Model\Auth();
    $auth
        ->setPayTdsAcquirerId($_POST['acquirer_id'])
        ->setPayTdsTransactionId($_POST['threeds_transaction_id']);

    $payment = new Model\Payment();
    $payment
        ->setMethod(Model\Payment::METHOD_CSE)
        ->setCse($cse)
        ->setAuth($auth);

    $result = Payment::authorize($transaction, $payment)->getData();

} catch (Exception $e) {
    $result = array(
        'type' => 'error',
        'message' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($result);