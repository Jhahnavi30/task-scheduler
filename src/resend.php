<?php
include 'functions.php';

$email = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (resendVerification($email)) {
            $message = "<p class='success'>✅ Verification email resent to <strong>$email</strong>.</p>";
        } else {
            $message = "<p class='error'>❌ This email is either already verified or not found.</p>";
        }
    } else {
        $message = "<p class='error'>❌ Please enter a valid email address.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resend Verification</title>
    <style>
        body {
            font-family: Arial;
            background: #f0f0f0;
            padding: 80px;
            text-align: center;
        }
        form {
            background: #fff;
            display: inline-block;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        input[type="email"] {
            padding: 10px;
            width: 260px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            padding: 10px 25px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .success {
            color: green;
            margin-top: 20px;
        }
        .error {
            color: red;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <form method="POST">
        <h2>🔁 Resend Verification Email</h2>
        <input type="email" name="email" placeholder="Enter your email" required><br>
        <input type="submit" value="Resend Email">
        <?= $message ?>
    </form>

</body>
</html>
