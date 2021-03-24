<?php
require_once '../config.php';
try {
    $result = null;

    if (isset($_GET) && isset($_GET['transaction_id'])) {
        /** @var \Paynl\Result\Transaction\Start $result */
        $result = \Paynl\Creditcard::cseTdsStatus(filter_var($_GET['transaction_id'], FILTER_SANITIZE_STRING));
    }
} catch (\Exception $e) {
    $result  = array(
        'type' => 'error',
        'message' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($result);
