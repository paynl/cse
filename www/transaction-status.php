<?php

use Paynl\Config;
use Paynl\Result\Transaction\Start;

require_once '../config.php';
try {
    $result = null;

    if (isset($_GET) && isset($_GET['transaction_id'])) {
        /** @var Start $result */
        Config::setPaymentApiBase('https://api.card.maurice.dev.pay.nl');
        $arrResult = Paynl\Payment::paymentAuthenticationStatus(
            filter_var(
                $_GET['transaction_id'],
                FILTER_SANITIZE_STRING
            )
        );

        $result = array(
            'result' => !empty($arrResult['request']['result']) ? (string)$arrResult['request']['result'] : "0"
        );

        if ($result['result'] > 0) {
            $result['transactionID'] = $arrResult['threeDs']['transactionID'];
            $result['transactionStatusCode'] = $arrResult['threeDs']['transactionStatusCode'];
        }
    }
} catch (Exception $e) {
    $result = array(
        'type' => 'error',
        'message' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($result);