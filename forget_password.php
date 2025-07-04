<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>

<h2>Forgot Password</h2>

<!-- Send OTP Form -->
<form action="send_otp.php" method="post">
    <input type="email" name="email" placeholder="Enter your registered email" required>
    <button type="submit">Send OTP</button>
</form>

<!-- Verify OTP & Reset Password -->
<form action="verify_otp.php" method="post">
    <input type="text" name="otp" placeholder="Enter OTP" required><br><br>
    <input type="password" name="new_password" placeholder="Enter New Password" required><br><br>
    <button type="submit">Verify & Reset Password</button>
</form>

</body>
</html>
