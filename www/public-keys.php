<?php

use Paynl\Payment;

require_once '../config.php';
try {
    $result = Payment::paymentEncryptionKeys();
} catch (Exception $e) {
    $result = array(
        'type' => 'error',
        'message' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($result);

