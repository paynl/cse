<?php
require_once '../config.php';
try {
    $result = null;

    if (isset($_POST) && isset($_POST['pay_encrypted_data'])) {
        /** @var \Paynl\Result\Transaction\Start $result */
        $result = \Paynl\Transaction::start(array(
            'returnUrl' => RETURN_URL,
            'amount' => 0.01,
            'currency' => 'EUR',
            'paymentMethod' => 706,
        ));
        $data = $result->getData();

        $result = \Paynl\Creditcard::cseAuthenticate(
            $result->getTransactionId(),
            $_POST['pay_encrypted_data']
        );
        $result['transaction'] = $data['transaction'];
    }
} catch (\Exception $e) {
    $result  = array(
        'type' => 'error',
        'message' => $e->getMessage()
    );
}

header('content-type: application/json');
echo json_encode($result);

