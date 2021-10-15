<?php

use Paynl\Config;
use Paynl\Payment;

require_once '../config.php';
try {
    $result = null;

    if (isset($_POST) && isset($_POST['pay_encrypted_data'])) {
        $payload = json_decode($_POST['pay_encrypted_data'], true);

        Config::setPaymentApiBase('https://api.card.maurice.dev.pay.nl');
        $arrResult = Payment::paymentAuthenticate(array(
            'returnUrl' => RETURN_URL,
            'amount' => 0.01,
            'currency' => 'EUR',
            'description' => "Lorem Ipsum",
            'identifier' => $payload['identifier'],
            'data' => $payload['data'],
        ));

        $result = array(
            'result' => !empty($arrResult['request']['result']) ? (int)$arrResult['request']['result'] : 0,
        );

        if ($result['result'] > 0) {
            if (isset($arrResult['transaction']) && is_array($arrResult['transaction'])) {
                $result['orderId'] = $arrResult['transaction']['orderId'];
                $result['entranceCode'] = $arrResult['transaction']['entranceCode'];
                $result['transaction']['transactionId'] = $arrResult['transaction']['orderId'];
                $result['transaction']['entranceCode'] = $arrResult['transaction']['entranceCode'];
            }
            if (isset($arrResult['threeDs']) && is_array($arrResult['threeDs'])) {
                $result = array_merge($result, $arrResult['threeDs']);
                $result['transactionID'] = $arrResult['threeDs']['transactionID'];
                $result['acquirerID'] = $arrResult['threeDs']['acquirerID'];
            }
        } else {
            if (isset($arrResult['request'])) {
                $result['errorId'] = ! empty($arrResult['request']['errorId'])
                    ? $arrResult['request']['errorId']
                    : '';
                $result['errorMessage'] = ! empty($arrResult['request']['errorMessage'])
                    ? $arrResult['request']['errorMessage']
                    : '';
            } else {
                $result['errorMessage'] = isset($arrResult['message']) && ! empty($arrResult['message'])
                    ? $arrResult['message']
                    : '';
            }
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
