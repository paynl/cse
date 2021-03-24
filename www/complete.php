<?php
require_once '../config.php';
try {
    /** @var \Paynl\Result\Transaction\Details $result */
    $result = \Paynl\Transaction::details(filter_var($_GET['orderId'], FILTER_SANITIZE_STRING));
} catch (\Exception $e) {

}
?>
<html>
<body>
    <pre><?php echo print_r($result);?></pre>
</body>
</html>
