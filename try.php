<?php
// Include the database connection file
include 'db_connection.php';

// Retrieve quiz ID from the previous page or wherever it's coming from
if (isset($_GET['quiz_id'])) {
    $quizId = $_GET['quiz_id'];
} else {
    // Handle the case where quiz ID is not provided
    exit("Error: Quiz ID not provided");
}

// Fetch questions for the given quiz ID from the database
$stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->bind_param("i", $quizId);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any questions
if ($result->num_rows === 0) {
    exit("Error: No questions found for this quiz");
}

// Store the questions and options in an array
$questions = array();
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Conducting Page</title>
    <style>
        body {
            background-color: #f8c000;
            color: #000; /* Set black color for text */
            margin: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: Arial, sans-serif;
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

        h2 {
            color: #000;
        }

        .question {
            margin-bottom: 20px;
        }

        .options {
            display: flex;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        label {
            margin-right: 10px;
        }

        input[type="submit"] {
            width: 150px;
            height: 50px;
            font-size: 18px;
            background-color: #f8c000;
            color: #fff;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #f8a000;
        }

        #nextButton {
            width: 150px;
            height: 50px;
            font-size: 18px;
            background-color: #f8c000;
            color: #fff;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #nextButton:hover {
            background-color: #f8a000;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Quiz Conducting Page</h2>
    <form id="quizForm" action="submit_quiz.php" method="post">
        <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quizId); ?>">
        <input type="hidden" name="username" value="<?php echo htmlspecialchars($creatorUsername); ?>">
        <?php foreach ($questions as $index => $question): ?>
        <div class="question" id="question<?php echo $index + 1; ?>" <?php if ($index !== 0) echo 'style="display: none;"'; ?>>
            <h3>Question <?php echo $index + 1; ?> of <?php echo count($questions); ?>: <?php echo htmlspecialchars($question['question_text']); ?></h3>
            <div class="options">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                <input type="radio" id="option<?php echo $index + 1; ?>_<?php echo $i; ?>" name="answer[<?php echo $index + 1; ?>]" value="<?php echo htmlspecialchars($question['option'.$i]); ?>">
                <label for="option<?php echo $index + 1; ?>_<?php echo $i; ?>"><?php echo htmlspecialchars($question['option'.$i]); ?></label>
                <?php endfor; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="btn-group">
            <input type="button" value="Next" onclick="nextQuestion()" id="nextButton">
            <input type="submit" value="Submit" id="submitButton" style="display: none;">
        </div>
    </form>
</div>

<script>
    var currentQuestion = 1;
    var totalQuestions = <?php echo count($questions); ?>;

    function nextQuestion() {
        var currentDiv = document.getElementById('question' + currentQuestion);
        currentDiv.style.display = 'none';
        currentQuestion++;

        var nextDiv = document.getElementById('question' + currentQuestion);
        if (nextDiv) {
            nextDiv.style.display = 'block';
            if (currentQuestion === totalQuestions) {
                document.getElementById('nextButton').style.display = 'none';
                document.getElementById('submitButton').style.display = 'block';
            }
        }
    }
</script>

</body>
</html>
