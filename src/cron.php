<?php
// cron.php (final version as per README)

include 'functions.php';

date_default_timezone_set("Asia/Kolkata");

// 1. Get tasks from tasks.txt (in JSON)
$tasks = getAllTasks();
$pending_tasks = array_filter($tasks, fn($t) => !$t['completed']);

// 2. Get subscribers from subscribers.txt
$subscribers_file = __DIR__ . "/subscribers.txt";
if (!file_exists($subscribers_file)) {
    echo "No verified subscribers found.\n";
    exit;
}

$subscribers = json_decode(file_get_contents($subscribers_file), true);
if (!is_array($subscribers) || empty($subscribers)) {
    echo "No valid subscribers.\n";
    exit;
}

// 3. Send email to each verified subscriber
foreach ($subscribers as $email) {
    sendTaskEmail($email, $pending_tasks);
    echo "Reminder sent to: $email\n";

    // Optional: Log it
    file_put_contents(__DIR__ . "/email-log.txt", "[" . date("Y-m-d H:i:s") . "] Reminder sent to: $email\n", FILE_APPEND);
}
?>
