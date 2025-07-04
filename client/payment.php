<?php
require 'config.php';
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>

    <h2>Complete Your Payment</h2>

    <button id="payButton">Pay ₹100</button>

    <script>
        var stripe = Stripe("<?= STRIPE_PUBLISHABLE_KEY ?>");

        document.getElementById("payButton").addEventListener("click", function() {
            fetch("create_payment.php", { method: "POST" })
                .then(response => response.json())
                .then(session => {
                    return stripe.redirectToCheckout({ sessionId: session.id });
                })
                .catch(error => console.error("Error:", error));
        });
    </script>

</body>
</html>
