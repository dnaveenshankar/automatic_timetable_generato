<?php
// Define the quiz questions and options
$quiz = array(
    array(
        'question' => 'Question 1: What is the capital of France?',
        'options' => array('Paris', 'London', 'Berlin', 'Rome'),
        'correct_answer' => 1
    ),
    array(
        'question' => 'Question 2: What is the chemical symbol for water?',
        'options' => array('H2O', 'CO2', 'NaCl', 'O2'),
        'correct_answer' => 1
    ),
    array(
        'question' => 'Question 3: What is the largest planet in the solar system?',
        'options' => array('Jupiter', 'Saturn', 'Mars', 'Earth'),
        'correct_answer' => 1
    ),
    array(
        'question' => 'Question 4: Who developed the theory of relativity?',
        'options' => array('Albert Einstein', 'Isaac Newton', 'Galileo Galilei', 'Nikola Tesla'),
        'correct_answer' => 1
    ),
    array(
        'question' => 'Question 5: What is the powerhouse of the cell?',
        'options' => array('Mitochondria', 'Nucleus', 'Ribosome', 'Golgi apparatus'),
        'correct_answer' => 1
    )
);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user answer is set
    if (isset($_POST['user_answer'])) {
        // Start the session
        session_start();

        // Get the current question index from the session
        $question_index = $_SESSION['current_question'];

        // Check if session data for user answers exists
        if (!isset($_SESSION['user_answers'])) {
            $_SESSION['user_answers'] = array();
        }

        // Store the user's answer for the current question in the session
        $_SESSION['user_answers'][$question_index] = $_POST['user_answer'];

        // Check if all questions are answered
        if ($question_index + 1 == count($quiz)) {
            // All questions answered, insert responses into the database
            require_once 'db_connection.php'; // Include the database connection file

            // Prepare and execute the insertion query for each question
            foreach ($_SESSION['user_answers'] as $question_index => $user_answer) {
                $question_id = $question_index + 1; // Assuming question IDs start from 1
                $correct_answer = $quiz[$question_index]['correct_answer']; // Get correct answer from quiz array
                $marks_scored = ($user_answer == $correct_answer) ? 1 : 0; // Assign marks based on correctness

                // Prepare the SQL statement
                $stmt = $conn->prepare("INSERT INTO quiz_responses (question_id, user_answer, correct_answer, marks_scored) VALUES (?, ?, ?, ?)");

                // Bind parameters and execute the statement
                $stmt->bind_param("iiii", $question_id, $user_answer, $correct_answer, $marks_scored);
                $stmt->execute();
            }

            // Close the statement and database connection
            $stmt->close();
            $conn->close();

            // Clear session data
            session_unset();
            session_destroy();

            // Redirect to a thank you page or any other page
            header('Location: thank_you.php');
            exit;
        } else {
            // Move to the next question
            $_SESSION['current_question']++;
            header('Location: quiz.php');
            exit;
        }
    } else {
        echo "Error: User answer is missing!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <style>
        /* Add your CSS styles here */
        body {
            background-color: #f8c000;
            color: #000;
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
            max-width: 800px;
            text-align: center;
            overflow: auto;
            max-height: 100vh;
        }

        h2 {
            color: #000;
        }

        .options {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        button {
            margin: 5px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #f8c000;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #f8a000;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Quiz</h2>
    <?php
    // Check if session is started
    if (!isset($_SESSION)) {
        session_start();
    }

    // Check if current question index is set in session
    if (!isset($_SESSION['current_question'])) {
        $_SESSION['current_question'] = 0; // Set initial question index
    }

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Display a message to select an option if the user didn't select any option
        if (!isset($_POST['user_answer'])) {
            echo '<p style="color: red;">Please select an option.</p>';
        }
    }

    // Display current question
    $current_question = $_SESSION['current_question'];
    echo '<p>' . $quiz[$current_question]['question'] . '</p>';

    // Display options as buttons
    echo '<div class="options">';
    foreach ($quiz[$current_question]['options'] as $option_index => $option) {
        echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
        echo '<button type="submit" name="user_answer" value="' . ($option_index + 1) . '">' . $option . '</button>';
        echo '</form>';
    }
    echo '</div>';
    ?>
</div>
</body>
</html>
