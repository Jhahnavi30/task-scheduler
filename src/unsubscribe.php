<?php
$subscribers_file = 'subscribers.txt';
$email = $_GET['email'] ?? '';
$unsubscribed = false;

if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if (file_exists($subscribers_file)) {
        $subscribers = json_decode(file_get_contents($subscribers_file), true);
        $updated = array_filter($subscribers, fn($e) => $e !== $email);
        if (count($updated) !== count($subscribers)) {
            file_put_contents($subscribers_file, json_encode(array_values($updated), JSON_PRETTY_PRINT));
            $unsubscribed = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 100px;
            text-align: center;
            background: #f0f0f0;
        }
        .message {
            background: white;
            padding: 40px;
            display: inline-block;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="message">
        <?php if ($unsubscribed): ?>
            <h2 class="success">✅ You have been unsubscribed.</h2>
        <?php else: ?>
            <h2 class="error">❌ Could not unsubscribe. You may not be subscribed.</h2>
        <?php endif; ?>
    </div>
</body>
</html>
