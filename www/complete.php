<?php

use Paynl\Transaction;

require_once '../config.php';

try {
    $result = Transaction::details(filter_var($_GET['orderId'], FILTER_SANITIZE_STRING))->getData();
} catch (\Exception $e) {
    $result = array(
        'type' => 'error',
        'message' => $e->getMessage()
    );
}

?>
<html>
<body>
    <pre><?php echo print_r($result);?></pre>
</body>
</html>
