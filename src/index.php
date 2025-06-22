<?php
include 'functions.php';

// Handle form submissions BEFORE any HTML is sent
if (isset($_POST['task-name'])) {
  addTask($_POST['task-name']);
  header("Location: index.php");
  exit;
}

if (isset($_POST['task_id'])) {
  markTaskAsCompleted($_POST['task_id'], isset($_POST['is_completed']));
  header("Location: index.php");
  exit;
}

if (isset($_POST['delete_id'])) {
  deleteTask($_POST['delete_id']);
  header("Location: index.php");
  exit;
}

if (isset($_POST['subscribe'])) {
    $email = $_POST['email'];
    subscribeEmail($email);
    echo "<p>Verification email sent! Please check your inbox.</p>";
  }
  
?>

<!DOCTYPE html>
<html>
<head>
  <title>Task Planner</title>
  <style>
    .completed {
      text-decoration: line-through;
      color: gray;
    }
  </style>
</head>
<body>

  <h1>Task Planner</h1>

  <!-- Add Task Form -->
  <form method="POST">
    <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
    <button type="submit" id="add-task">Add Task</button>
  </form>

  <!-- Subscribe Email Form -->
<h2>Subscribe to Task Reminders</h2>
<form method="POST">
  <input type="email" name="email" placeholder="Enter your email" required />
  <button id="submit-email" name="subscribe">Subscribe</button>
</form>


  <hr>

  <!-- Tasks List -->
  <ul class="tasks-list">
    <?php
      $tasks = getAllTasks();
      foreach ($tasks as $task) {
        echo '<li class="task-item ' . ($task['completed'] ? 'completed' : '') . '">';

        // Completion form
        echo '<form method="POST" style="display:inline;">';
        echo '<input type="hidden" name="task_id" value="' . $task['id'] . '">';
        echo '<input type="checkbox" class="task-status" name="is_completed" value="1" ' . ($task['completed'] ? 'checked' : '') . ' onchange="this.form.submit();">';
        echo '</form> ';

        echo htmlspecialchars($task['name']);

        // Delete form
        echo '<form method="POST" style="display:inline;margin-left:10px;">';
        echo '<input type="hidden" name="delete_id" value="' . $task['id'] . '">';
        echo '<button class="delete-task">Delete</button>';
        echo '</form>';

        echo '</li>';
      }
    ?>
  </ul>

</body>
</html>
