<?php

use Paynl\Error\Api;
use Paynl\Payment;

require_once '../config.php';

try {

    $publicEncryptionKeys = Payment::paymentEncryptionKeys()->getKeys();
} catch (Api $exception) {
    die ('<h1>Unexpected response from server</h1><p>' . $exception->getMessage() . '</p>');
} catch (Exception $exception) {
    die ('<h1>Unexpected error</h1><p>' . $exception->getMessage() . '</p>');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit of debit kaartbetaling</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="cryptography-demo.css">
    <script>
        var keyUrl = 'public-keys.php';
        var keyPairs = '<?php echo json_encode($publicEncryptionKeys); ?>';
    </script>
</head>
<body>
<main class="rounded-box">
    <header>

    </header>
    <aside>
        <h2>Bestelgegevens</h2>
        <dl>
            <dt>Bedrag</dt>
            <dd>€ 0,01</dd>
            <dt>Begunstigde</dt>
            <dd>Classic Carparts</dd>
            <dt>Verloopt op</dt>
            <dd>19-02-2021 15:23</dd>
        </dl>
    </aside>

    <form action="process.php" method="post" data-pay-encrypt-form>
        <fieldset>
            <legend>Betaalgegevens</legend>
            <label for="card-holder">Kaarthouder</label>
            <input id="card-holder" type="text" placeholder="Naam van de kaarthouder" name="cardholder" value="" required data-pay-encrypt-field />
        </fieldset>

        <fieldset>
            <div class="row-pan-cvc-group">
                <div class="column-pan">
                    <div class="form-element-container has-icon-container">
                        <label for="cardnumber">Kaartnummer</label>
                        <div class="has-icon-wrap">
                            <span class="input-container">
                                <input id="cardnumber" type="text" name="cardnumber" placeholder="Het nummer van uw credit- of debitkaart" value="" required data-pay-encrypt-field />
                            </span>

                            <div class="icon-container">
                                <img src="img/creditcard/cc-front.svg" data-credit-card-type alt="Card type" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="column-cvc">
                    <div class="form-element-container">
                        <label for="cvc" data-cvc-label>CVC</label>
                        <span class="input-container">
                            <input id="cvc" type="text" name="cardcvc" placeholder="123" required value=""  data-pay-encrypt-field />
                        </span>
                    </div>
                </div>
            </div>

            <div class="row-expiry-group">
                <div class="column-month">
                    <div class="form-element-container">
                        <label for="month">Geldig tot maand</label>
                        <select name="valid_thru_month" id="month" data-pay-encrypt-field>
                            <option value="" disabled selected>Kies</option>
                            <option value="01">01 - Januari</option>
                            <option value="02">02 - Februari</option>
                            <option value="03">03 - Maart</option>
                            <option value="04">04 - April</option>
                            <option value="05">05 - Mei</option>
                            <option value="06">06 - Juni</option>
                            <option value="07">07 - Juli</option>
                            <option value="08">08 - Augustus</option>
                            <option value="09">09 - September</option>
                            <option value="10">10 - Oktober</option>
                            <option value="11">11 - November</option>
                            <option value="12">12 - December</option>
                        </select>
                    </div>
                </div>
                <div class="column-year">
                    <div class="form-element-container">
                        <label for="year">Jaar</label>
                        <select name="valid_thru_year" id="year"  data-pay-encrypt-field>
                            <option value="" disabled selected>Kies</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-element-container">
                <button type="submit" data-loading-state="0" disabled="disabled">
                    <span class="state-not-loading">Doorgaan</span>
                    <span class="state-loading">
                        <img src="img/spinner.gif" width="30" height="30" alt="Loading" />
                    </span>
                </button>
            </div>
        </fieldset>
    </form>
</main>

<script type="module">
    import MyForm from './merchant.js';

    window.addEventListener("DOMContentLoaded", () => {
        let form = new MyForm;
        form.init();
    });
</script>
<div id="payment-modal" class="modal micromodal-slide" aria-hidden="true"></div>
</body>
</html>
