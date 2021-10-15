<?php
use Paynl\Config;
use Paynl\Payment;

require_once '../config.php';
try {
    $result = null;

    if (isset($_POST) && isset($_POST['pay_encrypted_data'])) {
        $payload = json_decode($_POST['pay_encrypted_data'], true);

        Config::setPaymentApiBase('https://api.card.maurice.dev.pay.nl');
        $arrResult = Payment::paymentAuthorize(
                $_POST['transaction_id'],
                $_POST['entrance_code'],
                $_POST['threeds_transaction_id'],
                $_POST['acquirer_id'],
                $payload
        );

        $result = array(
            'result' => 0,
        );

        if (!empty($arrResult['request']['result']) && $arrResult['request']['result'] == 1) {
            if (!empty($arrResult['payment']['bankCode']) && $arrResult['payment']['bankCode'] == "00") {
                if (!empty($arrResult['transaction']['state']) && in_array(
                    $arrResult['transaction']['state'],
                    array(85, 95, 100)
                )) {
                    $result['result'] = 1;
                }
            }
        }

        if ($result['result'] > 0) {
            $result['nextAction'] = !empty($arrResult['transaction']['stateName']) ? strtolower(
                    $arrResult['transaction']['stateName']
            ) : '';
            $result['orderId'] = !empty($arrResult['transaction']['orderId']) ? $arrResult['transaction']['orderId'] : "";
            $result['entranceCode'] = !empty($arrResult['transaction']['entranceCode']) ? $arrResult['transaction']['entranceCode'] : "";
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