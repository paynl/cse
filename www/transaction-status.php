<?php

use Paynl\Result\Transaction\Start;
use Paynl\Payment;

require_once '../config.php';
try {
    if (!isset($_GET['transaction_id'])) {
        throw new Exception('Invalid transaction Id');
    }

    $result = Payment::authenticationStatus(
        filter_var($_GET['transaction_id'], FILTER_SANITIZE_STRING)
    )->getData();

} catch (Exception $e) {
    $result = array('result' => '0', 'errorMessage' => $e->getMessage());
}

header('content-type: application/json');
echo json_encode($result);