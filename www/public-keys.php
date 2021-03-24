<?php
require_once '../config.php';
try {
    $result = \Paynl\Creditcard::publicKeys();
} catch (\Exception $e) {
    $result  = array(
        'type' => 'error',
        'message' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($result);

