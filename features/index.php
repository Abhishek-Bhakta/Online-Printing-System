<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Razorpay UPI Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <h2>Razorpay UPI Payment</h2>
    <button id="payButton">Pay with Razorpay</button>

    <script>
        document.getElementById("payButton").addEventListener("click", function () {
            fetch("razorpay_payment.php")
            .then(response => response.json())
            .then(data => {
                var options = {
                    "key": data.key,
                    "amount": 10000,
                    "currency": "INR",
                    "name": "Printify",
                    "description": "UPI Payment",
                    "order_id": data.order_id,
                    "handler": function (response) {
                        alert("Payment Successful! Payment ID: " + response.razorpay_payment_id);
                    },
                    "prefill": {
                        "email": "customer@example.com",
                        "contact": "9999999999"
                    },
                    "theme": {
                        "color": "#3399cc"
                    }
                };
                var rzp1 = new Razorpay(options);
                rzp1.open();
            })
            .catch(error => console.error("Error:", error));
        });
    </script>
</body>
</html>
