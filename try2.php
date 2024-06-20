<?php
// Start the session
session_start();

// Include the database connection file
include 'db_connection.php';

// Retrieve the username and quiz ID from the previous page
if (isset($_POST['username']) && isset($_POST['quiz_id'])) {
    $username = $_POST['username'];
    $quizId = $_POST['quiz_id'];
    
} else {
    // Handle the case where the username or quiz ID is not set
    exit("Error: Username or quiz ID not provided");
}

// Retrieve questions for the quiz from the database
$stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->bind_param("i", $quizId);
$stmt->execute();
$result = $stmt->get_result();

// Check if questions are available
if ($result->num_rows === 0) {
    // Handle the case where no questions are available for the quiz
    exit("Error: No questions available for the quiz");
}

// Display the quiz questions
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quiz</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8c000;
      color: #000;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    .question-container {
      margin-bottom: 20px;
    }

    .form-group label {
      color: #000;
      text-align: left;
    }

    .btn-primary {
      background-color: #f8c000;
      border-color: #f8c000;
    }

    .btn-primary:hover {
      background-color: #f8a000;
      border-color: #f8a000;
    }

    h1 {
      color: #f8c000;
      margin-bottom: 20px;
    }

    .animate-input {
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    input.form-control,
    textarea.form-control {
      border-radius: 12px;
      margin-bottom: 15px;
      font-size: 16px;
    }
  </style>
</head>
<body>

<div class="container mt-5 animate-input">
  <h1 class="text-center mb-4">Quiz</h1>
  
  <form method="post" action="grade.php">
    <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
    <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quizId); ?>">
    
    <div id="questions">
      <!-- Display quiz questions dynamically -->
      <?php
      $index = 1;
      while ($row = $result->fetch_assoc()) {
          echo '<div class="question-container">';
          echo '<h4>Question ' . $index . '</h4>';
          echo '<p>' . htmlspecialchars($row['question_text']) . '</p>';
          
          // Display options
          echo '<div class="form-check">';
          echo '<input class="form-check-input" type="radio" name="answers[' . $row['question_id'] . ']" id="option1_' . $row['question_id'] . '" value="0" required>';
          echo '<label class="form-check-label" for="option1_' . $row['question_id'] . '">' . htmlspecialchars($row['option1']) . '</label>';
          echo '</div>';
          
          echo '<div class="form-check">';
          echo '<input class="form-check-input" type="radio" name="answers[' . $row['question_id'] . ']" id="option2_' . $row['question_id'] . '" value="1">';
          echo '<label class="form-check-label" for="option2_' . $row['question_id'] . '">' . htmlspecialchars($row['option2']) . '</label>';
          echo '</div>';
          
          echo '<div class="form-check">';
          echo '<input class="form-check-input" type="radio" name="answers[' . $row['question_id'] . ']" id="option3_' . $row['question_id'] . '" value="2">';
          echo '<label class="form-check-label" for="option3_' . $row['question_id'] . '">' . htmlspecialchars($row['option3']) . '</label>';
          echo '</div>';
          
          echo '<div class="form-check">';
          echo '<input class="form-check-input" type="radio" name="answers[' . $row['question_id'] . ']" id="option4_' . $row['question_id'] . '" value="3">';
          echo '<label class="form-check-label" for="option4_' . $row['question_id'] . '">' . htmlspecialchars($row['option4']) . '</label>';
          echo '</div>';
          
          echo '</div>';
          $index++;
      }
      ?>
    </div>
        
    <button type="submit" class="btn btn-primary animate-input">Submit</button>
    <a href="dashboard.php" class="btn btn-secondary mt-4">Back</a>
  </form>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
