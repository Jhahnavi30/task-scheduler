<?php
include 'functions.php';

$email = $_GET['email'] ?? '';
$code = $_GET['code'] ?? '';
$result = '';

if ($email && $code) {
    if (verifySubscription($email, $code)) {
        $result = "<h2 class='success'>✅ Your email ($email) has been verified!</h2>";
    } else {
        $result = "<h2 class='error'>❌ Verification failed. Link may be invalid or expired.</h2>";
    }
} else {
    $result = "<h2 class='error'>Invalid verification link.</h2>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            text-align: center;
            padding: 100px;
        }
        .message {
            background: white;
            display: inline-block;
            padding: 30px 60px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="message">
        <?= $result ?>
    </div>
</body>
</html>
