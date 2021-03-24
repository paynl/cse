<?php
require_once '../config.php';
try {
    $result = null;

    if (isset($_POST) && isset($_POST['pay_encrypted_data'])) {
        $result = \Paynl\Creditcard::cseAuthorize(
            $_POST['transaction_id'],
            $_POST['threeds_transaction_id'],
            $_POST['pay_encrypted_data']
        );
    }
} catch (\Exception $e) {
    $result  = array(
        'type' => 'error',
        'message' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($result);
