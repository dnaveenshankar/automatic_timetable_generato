<?php
// Start the session
session_start();

// Include the database connection file
include 'db_connection.php';

// Retrieve the username from the session
if (isset($_SESSION['username'])) {
  $creatorUsername = $_SESSION['username'];
} else {
  // Handle the case where the username is not set in the session
  exit("Error: User not logged in");
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate a random 6-digit quiz ID
    $quizId = generateUniqueQuizId($conn);

    // Get data from the form
    $quizName = $_POST['quiz-name'];
    $quizDescription = $_POST['quiz-description'];
    $questions = $_POST['question'];
    $totalMarks = 0;

    // Calculate total marks
    foreach ($questions as $question) {
        $totalMarks += $question['marks'];
    }

    // Verify if the creator username exists in the users table
    $stmtVerifyUser = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmtVerifyUser->bind_param("s", $creatorUsername);
    $stmtVerifyUser->execute();
    $resultVerifyUser = $stmtVerifyUser->get_result();

    if ($resultVerifyUser->num_rows === 0) {
        // Handle the case where the creator username does not exist
        exit("Error: Creator username does not exist in the users table");
    }

    $stmt = $conn->prepare("INSERT INTO quizzes (quiz_id, quiz_name, quiz_description, total_marks, creator_username) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $quizId, $quizName, $quizDescription, $totalMarks, $creatorUsername);
    $stmt->execute();

    // Insert questions into the database
    foreach ($questions as $question) {
        $questionText = $question['question'];
        $option1 = $question['options'][0]; 
        $option2 = $question['options'][1];
        $option3 = $question['options'][2];
        $option4 = $question['options'][3];
        $correctAnswer = $question['correct_answer'];
        $marks = $question['marks'];

        $stmtInsertQuestion = $conn->prepare("INSERT INTO questions (quiz_id, question_text, option1, option2, option3, option4, correct_answer, marks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtInsertQuestion->bind_param("isssssii", $quizId, $questionText, $option1, $option2, $option3, $option4, $correctAnswer, $marks);
        $stmtInsertQuestion->execute();
    }

    // Redirect to a success page
    $_SESSION['quiz_created_username'] = $creatorUsername;
    header('Location: my_quiz.php?quiz_created=1&username=' . urlencode($creatorUsername));
    exit();
}

// Function to generate a unique random 6-digit quiz ID
function generateUniqueQuizId($conn) {
    $quizId = rand(100000, 999999);

    // Check if the generated quiz ID already exists in the database
    $stmtCheckId = $conn->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
    $stmtCheckId->bind_param("i", $quizId);
    $stmtCheckId->execute();
    $resultCheckId = $stmtCheckId->get_result();

    if ($resultCheckId->num_rows > 0) {
        // If the ID exists, generate a new one recursively
        return generateUniqueQuizId($conn);
    } else {
        // If the ID is unique, return it
        return $quizId;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quiz Creator</title>
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
      box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.2);
      padding: 40px;
      max-width: 1200px; /* Increased the width of the container */
      text-align: center;
      overflow: auto;
      max-height: 100vh; 
    }

    .quiz-form {
      margin-top: 20px;
    }

    .form-group label {
      color: #000;
      text-align: left;
    }

    .center-vertically {
      display: flex;
      flex-direction: column;
      justify-content: center;
      height: 100%;
    }

    .btn-primary,
    .btn-success,
    .btn-warning,
    .btn-danger {
      background-color: #f8c000;
      border-color: #f8c000;
    }

    .btn-primary:hover,
    .btn-success:hover,
    .btn-warning:hover,
    .btn-danger:hover {
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

    .question-container input.form-control,
    .question-container textarea.form-control {
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

<div class="container mt-5 animate-input">
  <h1 class="text-center mb-4">Create Your Quiz</h1>
  
  <form class="quiz-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="form-group animate-input">
      <label for="quiz-name">Quiz Name:</label>
      <input type="text" class="form-control" id="quiz-name" name="quiz-name" placeholder="Enter quiz name" required>
    </div>
    <input type="hidden" name="username" value="<?php echo htmlspecialchars($creatorUsername); ?>">

    <div class="form-group animate-input">
      <label for="quiz-description">Quiz Description:</label>
      <textarea class="form-control" id="quiz-description" name="quiz-description" placeholder="Enter quiz description"></textarea>
    </div>

    <div id="questions">
      <!-- Question containers will be dynamically added here -->
    </div>
        
    <button type="button" class="btn btn-primary animate-input" onclick="addQuestion()">Add Question</button>
    <button type="submit" class="btn btn-danger animate-input">Next</button>
    <div id="total-marks" class="mt-4 animate-input"></div>
    <a href="dashboard.php" class="btn btn-secondary mt-4">Back</a>

  </form>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  let questionIndex = 0;

  function addQuestion() {
    const questionContainer = document.createElement('div');
    questionContainer.className = 'question-container animate-input';
    questionContainer.id = 'question-' + questionIndex;

    const questionInputs = `
      <label for="question">Question:</label>
      <input type="text" class="form-control" name="question[${questionIndex}][question]" placeholder="Enter question" required>
      
      <div class="row mt-3">
        <div class="col-md-6">
          <label for="option1">Option 1:</label>
          <input type="text" class="form-control" name="question[${questionIndex}][options][]" placeholder="Option 1" required>
        </div>
        <div class="col-md-6">
          <label for="option2">Option 2:</label>
          <input type="text" class="form-control" name="question[${questionIndex}][options][]" placeholder="Option 2" required>
        </div>
      </div>
      
      <div class="row mt-3">
        <div class="col-md-6">
          <label for="option3">Option 3:</label>
          <input type="text" class="form-control" name="question[${questionIndex}][options][]" placeholder="Option 3" required>
        </div>
        <div class="col-md-6">
          <label for="option4">Option 4:</label>
          <input type="text" class="form-control" name="question[${questionIndex}][options][]" placeholder="Option 4" required>
        </div>
      </div>
      
      <label for="correct-answer" class="mt-3">Correct Answer:</label>
      <select class="form-control" name="question[${questionIndex}][correct_answer]" required>
        <option value="0">Option 1</option>
        <option value="1">Option 2</option>
        <option value="2">Option 3</option>
        <option value="3">Option 4</option>
      </select>

      <label for="marks" class="mt-3">Marks:</label>
      <input type="number" class="form-control" name="question[${questionIndex}][marks]" placeholder="Marks" required>
      <button type="button" class="btn btn-danger mt-2" onclick="removeQuestion('question-${questionIndex}')">Remove Question</button>
    `;

    questionContainer.innerHTML = questionInputs;
    document.getElementById('questions').appendChild(questionContainer);

    questionIndex++;
  }

  function removeQuestion(questionId) {
    const questionContainer = document.getElementById(questionId);
    if (questionContainer) {
      document.getElementById('questions').removeChild(questionContainer);
      questionIndex--;
      calculateMarks(); // Recalculate total marks after removing a question
    }
  }

  document.addEventListener('input', function (event) {
    if (event.target && event.target.matches('input[name^="question"]')) {
      calculateMarks();
    }
  });

  function calculateMarks() {
    const questionContainers = document.getElementsByClassName('question-container');
    let totalMarks = 0;

    for (let i = 0; i < questionContainers.length; i++) {
      const marksInput = questionContainers[i].querySelector('input[name^="question"][name$="[marks]"]');
      if (marksInput && marksInput.value !== '') {
        totalMarks += parseInt(marksInput.value);
      }
    }

    document.getElementById('total-marks').innerText = 'Total Marks: ' + totalMarks;
  }
</script>

</body>
</html>
