<?php
// Dummy change to enable Pull Request

function getAllTasks() {
    $file = 'tasks.txt';
    if (!file_exists($file)) {
        file_put_contents($file, '[]');
    }
    $data = json_decode(file_get_contents($file), true);
    return $data ?? [];
}

function saveAllTasks($tasks) {
    file_put_contents('tasks.txt', json_encode($tasks, JSON_PRETTY_PRINT));
}

function generateTaskId() {
    return uniqid(); // creates a unique ID
}

function addTask($task_name) {
    $tasks = getAllTasks();
    foreach ($tasks as $task) {
        if (strcasecmp($task['name'], $task_name) == 0) {
            return; // Duplicate task, do not add
        }
    }

    $new_task = [
        'id' => generateTaskId(),
        'name' => $task_name,
        'completed' => false
    ];
    $tasks[] = $new_task;
    saveAllTasks($tasks);
}

function markTaskAsCompleted($task_id, $is_completed) {
    $tasks = getAllTasks();
    foreach ($tasks as &$task) {
        if ($task['id'] == $task_id) {
            $task['completed'] = $is_completed ? true : false;
            break;
        }
    }
    saveAllTasks($tasks);
}

function deleteTask($task_id) {
    $tasks = getAllTasks();
    $tasks = array_filter($tasks, function($task) use ($task_id) {
        return $task['id'] !== $task_id;
    });
    saveAllTasks(array_values($tasks)); // reindex array
}





function generateVerificationCode() {
    return strval(rand(100000, 999999)); // 6-digit code
}

function subscribeEmail($email) {
    $pending_file = 'pending_subscriptions.txt';

    // Load existing pending list
    if (!file_exists($pending_file)) file_put_contents($pending_file, '{}');
    $pending = json_decode(file_get_contents($pending_file), true);

    // Generate code and save
    $code = generateVerificationCode();
    $pending[$email] = [
        'code' => $code,
        'timestamp' => time()
    ];

    file_put_contents($pending_file, json_encode($pending, JSON_PRETTY_PRINT));

    // Send verification email
    $verification_link = "http://localhost:8000/verify.php?email=" . urlencode($email) . "&code=" . $code;
    $subject = "Verify subscription to Task Planner";
    $message = "
        <p>Click the link below to verify your subscription to Task Planner:</p>
        <p><a id='verification-link' href='$verification_link'>Verify Subscription</a></p>
    ";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";

    mail($email, $subject, $message, $headers);
}

function verifySubscription($email, $code) {
    $pending_file = 'pending_subscriptions.txt';
    $subscribers_file = 'subscribers.txt';

    // Load files
    if (!file_exists($pending_file)) return false;
    $pending = json_decode(file_get_contents($pending_file), true);

    if (!isset($pending[$email])) return false;

    if ($pending[$email]['code'] === $code) {
        // Add to verified subscribers
        if (!file_exists($subscribers_file)) file_put_contents($subscribers_file, '[]');
        $subscribers = json_decode(file_get_contents($subscribers_file), true);
        if (!in_array($email, $subscribers)) {
            $subscribers[] = $email;
            file_put_contents($subscribers_file, json_encode($subscribers, JSON_PRETTY_PRINT));
        }

        // Remove from pending
        unset($pending[$email]);
        file_put_contents($pending_file, json_encode($pending, JSON_PRETTY_PRINT));
        return true;
    }

    return false;
}

function resendVerification($email) {
    $pending_file = 'pending_subscriptions.txt';
    $subscribers_file = 'subscribers.txt';

    // If already verified, skip
    if (file_exists($subscribers_file)) {
        $verified = json_decode(file_get_contents($subscribers_file), true);
        if (in_array($email, $verified)) {
            return false; // already verified
        }
    }

    // Load or create pending list
    if (!file_exists($pending_file)) file_put_contents($pending_file, '{}');
    $pending = json_decode(file_get_contents($pending_file), true);

    // If not already in pending, reject
    if (!isset($pending[$email])) {
        return false; // not found
    }

    // Regenerate code and resend
    $code = generateVerificationCode();
    $pending[$email]['code'] = $code;
    $pending[$email]['timestamp'] = time();
    file_put_contents($pending_file, json_encode($pending, JSON_PRETTY_PRINT));

    // Send verification email again
    $verification_link = "http://localhost:8000/verify.php?email=" . urlencode($email) . "&code=" . $code;
    $subject = "Resend: Verify subscription to Task Planner";
    $message = "
        <p>You requested a new verification link:</p>
        <p><a href='$verification_link'>Verify Subscription</a></p>
    ";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";

    mail($email, $subject, $message, $headers);

    return true;
}

function sendTaskEmail($email, $pending_tasks) {
    $subject = "Task Planner - Pending Tasks Reminder";

    $taskList = '';
    foreach ($pending_tasks as $task) {
        $taskList .= "<li>" . htmlspecialchars($task['name']) . "</li>";
    }

    $unsubscribe_link = "http://localhost:8000/unsubscribe.php?email=" . urlencode($email);

    $message = "
        <html>
        <body>
            <h2>Pending Tasks Reminder</h2>
            <p>Here are the current pending tasks:</p>
            <ul>
                $taskList
            </ul>
            <p><a id='unsubscribe-link' href='$unsubscribe_link'>Unsubscribe from notifications</a></p>
        </body>
        </html>
    ";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    mail($email, $subject, $message, $headers);
}

function sendTaskReminders() {
    $subscribers_file = 'subscribers.txt';
    $subscribers = [];

    if (file_exists($subscribers_file)) {
        $subscribers = json_decode(file_get_contents($subscribers_file), true);
    }

    $tasks = getAllTasks();
    $pending_tasks = array_filter($tasks, function($task) {
        return !$task['completed'];
    });

    if (empty($pending_tasks)) {
        echo "No pending tasks to send.\n";
        return;
    }

    foreach ($subscribers as $email) {
        sendTaskEmail($email, $pending_tasks);
        echo "Reminder sent to: $email\n";
    }
}


function unsubscribeEmail($email) {
    $subscribers_file = 'subscribers.txt';

    if (!file_exists($subscribers_file)) return;

    $subscribers = json_decode(file_get_contents($subscribers_file), true);

    $updated = array_filter($subscribers, function($e) use ($email) {
        return $e !== $email;
    });

    file_put_contents($subscribers_file, json_encode(array_values($updated), JSON_PRETTY_PRINT));
}


?>