<?php
// Include the database connection file
include 'db_connection.php';

// Retrieve quiz ID and username from POST data
if (isset($_POST['quiz_id'], $_POST['username'])) {
    $quizId = $_POST['quiz_id'];
    $username = $_POST['username'];

    // Fetch user's responses from the quiz_responses table
    $stmt_responses = $conn->prepare("SELECT question_number, user_answer FROM quiz_responses WHERE quiz_id = ? AND username = ?");
    $stmt_responses->bind_param("is", $quizId, $username);
    $stmt_responses->execute();
    $result_responses = $stmt_responses->get_result();

    // Prepare statement to update correct_answer and marks_scored in quiz_responses table
    $updateStmt = $conn->prepare("UPDATE quiz_responses SET correct_answer = ?, marks_scored = ? WHERE quiz_id = ? AND username = ? AND question_number = ?");

    // Loop through user's responses
    while ($row = $result_responses->fetch_assoc()) {
        $questionNumber = $row['question_number'];
        $userAnswer = $row['user_answer'];

        // Fetch correct option and marks for the question from the questions table
        $stmt_question = $conn->prepare("SELECT option1, option2, option3, option4, correct_answer, marks FROM questions WHERE quiz_id = ? AND question_number = ?");
        $stmt_question->bind_param("ii", $quizId, $questionNumber);
        $stmt_question->execute();
        $result_question = $stmt_question->get_result();

        // If question exists, update correct_answer and marks_scored in quiz_responses table
        if ($result_question->num_rows > 0) {
            $questionData = $result_question->fetch_assoc();
            $correctOption = $questionData['option' . $questionData['correct_answer']];
            $marks = $questionData['marks'];
            $marksScored = ($userAnswer === $correctOption) ? $marks : 0;

            // Update correct_answer and marks_scored in the quiz_responses table
            $updateStmt->bind_param("siiisi", $correctOption, $marksScored, $quizId, $username, $questionNumber);
            $updateStmt->execute();
        }

        // Close question statement
        $stmt_question->close();
    }

    // Close statements
    $stmt_responses->close();
    $updateStmt->close();

    // Close the database connection
    $conn->close();

    // JavaScript for displaying the alert and redirecting after insertion
    echo '<script>';
    echo 'alert("Answers Submitted");';
    echo 'window.location.href = "dashboard.php";';
    echo '</script>';
    exit(); // Ensure script execution stops after redirection
} else {
    // Handle the case where quiz ID or username is not provided
    echo "Error: Quiz ID or Username not provided";
}
?>
