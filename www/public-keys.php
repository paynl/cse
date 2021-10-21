<?php

use Paynl\Payment;

require_once '../config.php';

$result = Payment::paymentEncryptionKeys()->getKeys();

header('content-type: application/json');
echo json_encode($result);

