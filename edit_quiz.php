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

// Retrieve the quiz ID from the URL parameter
if (isset($_GET['quiz_id'])) {
    $quizId = $_GET['quiz_id'];
} else {
    // Handle the case where the quiz ID is not provided
    exit("Error: Quiz ID not provided");
}

// Fetch quiz details from the database
$stmt_quiz = $conn->prepare("SELECT quiz_name, quiz_description FROM quizzes WHERE quiz_id = ? AND creator_username = ?");
$stmt_quiz->bind_param("is", $quizId, $creatorUsername);
$stmt_quiz->execute();
$result_quiz = $stmt_quiz->get_result();

if ($result_quiz->num_rows > 0) {
    $quiz = $result_quiz->fetch_assoc();
    $quizName = $quiz['quiz_name'];
    $quizDescription = $quiz['quiz_description'];

    // Fetch questions related to the quiz
    $stmt_questions = $conn->prepare("SELECT question_id, question_text, option1, option2, option3, option4, correct_answer, marks FROM questions WHERE quiz_id = ?");
    $stmt_questions->bind_param("i", $quizId);
    $stmt_questions->execute();
    $result_questions = $stmt_questions->get_result();
} else {
    // Handle the case where the quiz is not found
    exit("Error: Quiz not found or you do not have permission to edit this quiz");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Quiz</title>
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
            max-width: 1200px; 
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
    textarea.form-control,
    select.form-control {
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
  <h1 class="text-center mb-4">Edit Your Quiz</h1>
  
  <form class="quiz-form" method="post" action="update_quiz.php">
    <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quizId); ?>">
    <div class="form-group animate-input">
      <label for="quiz-name">Quiz Name:</label>
      <input type="text" class="form-control" id="quiz-name" name="quiz-name" placeholder="Enter quiz name" value="<?php echo htmlspecialchars($quizName); ?>" required>
    </div>

    <div class="form-group animate-input">
      <label for="quiz-description">Quiz Description:</label>
      <textarea class="form-control" id="quiz-description" name="quiz-description" placeholder="Enter quiz description"><?php echo htmlspecialchars($quizDescription); ?></textarea>
    </div>

    <div id="questions">
      <?php
while ($row = $result_questions->fetch_assoc()) {
    echo '<div class="question-container animate-input" id="question-' . $row['question_id'] . '">';
    echo '<input type="hidden" name="question[' . $row['question_id'] . '][question_id]" value="' . $row['question_id'] . '">';
    echo '<label for="question">Question:</label>';
    echo '<input type="text" class="form-control" name="question[' . $row['question_id'] . '][question]" value="' . htmlspecialchars($row['question_text']) . '" placeholder="Enter question" required>';
    
    echo '<div class="row mt-3">';
    echo '<div class="col-md-6">';
    echo '<label for="option1">Option 1:</label>';
    echo '<input type="text" class="form-control" name="question[' . $row['question_id'] . '][options][]" value="' . htmlspecialchars($row['option1']) . '" placeholder="Option 1" required>';
    echo '</div>';
    echo '<div class="col-md-6">';
    echo '<label for="option2">Option 2:</label>';
    echo '<input type="text" class="form-control" name="question[' . $row['question_id'] . '][options][]" value="' . htmlspecialchars($row['option2']) . '" placeholder="Option 2" required>';
    echo '</div>';
    echo '</div>';

    echo '<div class="row mt-3">';
    echo '<div class="col-md-6">';
    echo '<label for="option3">Option 3:</label>';
    echo '<input type="text" class="form-control" name="question[' . $row['question_id'] . '][options][]" value="' . htmlspecialchars($row['option3']) . '" placeholder="Option 3" required>';
    echo '</div>';
    echo '<div class="col-md-6">';
    echo '<label for="option4">Option 4:</label>';
    echo '<input type="text" class="form-control" name="question[' . $row['question_id'] . '][options][]" value="' . htmlspecialchars($row['option4']) . '" placeholder="Option 4" required>';
    echo '</div>';
    echo '</div>';

    echo '<label for="correct-answer" class="mt-3">Correct Answer:</label>';
    echo '<select class="form-control" name="question[' . $row['question_id'] . '][correct_answer]" required>';
    for ($i = 0; $i < 4; $i++) {
        $selected = ($i == $row['correct_answer']) ? 'selected' : '';
        echo '<option value="' . $i . '" ' . $selected . '>Option ' . ($i + 1) . '</option>';
    }
    echo '</select>';

    echo '<label for="marks" class="mt-3">Marks:</label>';
    echo '<input type="number" class="form-control" name="question[' . $row['question_id'] . '][marks]" value="' . $row['marks'] . '" placeholder="Marks" required>';
    echo '<button type="button" class="btn btn-danger mt-2" onclick="removeQuestion(' . $row['question_id'] . ')">Remove Question</button>';
    echo '</div>';
}
?>
    </div>
        
    <button type="button" class="btn btn-primary animate-input" onclick="addQuestion()">Add Question</button>
    <button type="submit" class="btn btn-danger animate-input">Save Changes</button>
    <div id="total-marks" class="mt-4 animate-input"></div>
    <a href="javascript:history.back()" class="btn btn-secondary mt-4">Back</a> <!-- Go one page back -->
  </form>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  let questionIndex = <?php echo $result_questions->num_rows; ?>;

  function addQuestion() {
    const questionContainer = document.createElement('div');
    questionContainer.className = 'question-container animate-input';
    questionContainer.id = 'question-' + questionIndex;

    const questionInputs = `
      <label for="question">Question:</label>
      <input type="text" class="form-control" name="question[new_${questionIndex}][question]" placeholder="Enter question" required>
      
      <div class="row mt-3">
        <div class="col-md-6">
          <label for="option1">Option 1:</label>
          <input type="text" class="form-control" name="question[new_${questionIndex}][options][]" placeholder="Option 1" required>
        </div>
        <div class="col-md-6">
          <label for="option2">Option 2:</label>
          <input type="text" class="form-control" name="question[new_${questionIndex}][options][]" placeholder="Option 2" required>
        </div>
      </div>
      
      <div class="row mt-3">
        <div class="col-md-6">
          <label for="option3">Option 3:</label>
          <input type="text" class="form-control" name="question[new_${questionIndex}][options][]" placeholder="Option 3" required>
        </div>
        <div class="col-md-6">
          <label for="option4">Option 4:</label>
          <input type="text" class="form-control" name="question[new_${questionIndex}][options][]" placeholder="Option 4" required>
        </div>
      </div>
      
      <label for="correct-answer" class="mt-3">Correct Answer:</label>
      <select class="form-control" name="question[new_${questionIndex}][correct_answer]" required>
        <option value="0">Option 1</option>
        <option value="1">Option 2</option>
        <option value="2">Option 3</option>
        <option value="3">Option 4</option>
      </select>

      <label for="marks" class="mt-3">Marks:</label>
      <input type="number" class="form-control" name="question[new_${questionIndex}][marks]" placeholder="Marks" required>
      <button type="button" class="btn btn-danger mt-2" onclick="removeQuestion(${questionIndex})">Remove Question</button>
    `;

    questionContainer.innerHTML = questionInputs;
    document.getElementById('questions').appendChild(questionContainer);

    questionIndex++;
  }

  function removeQuestion(questionId) {
    const questionContainer = document.getElementById('question-' + questionId);
    if (questionContainer) {
        // Check if the question ID starts with 'new_', indicating it's a new question
        if (questionId.toString().startsWith('new_')) {
            // This is a new question, remove it from the DOM
            document.getElementById('questions').removeChild(questionContainer);
            questionIndex--;
            calculateMarks(); // Recalculate total marks after removing a question
        } else {
            // This is an existing question, remove its input fields from the form
            questionContainer.parentNode.removeChild(questionContainer);
        }
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
