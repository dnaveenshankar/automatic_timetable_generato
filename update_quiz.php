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
    // Retrieve quiz ID from the form
    if (isset($_POST['quiz_id'])) {
        $quizId = $_POST['quiz_id'];
    } else {
        // Handle the case where the quiz ID is not provided
        exit("Error: Quiz ID not provided");
    }

    // Retrieve quiz name and description from the form
    $quizName = $_POST['quiz-name'];
    $quizDescription = $_POST['quiz-description'];

    // Update quiz details in the database
    $stmtUpdateQuiz = $conn->prepare("UPDATE quizzes SET quiz_name = ?, quiz_description = ? WHERE quiz_id = ? AND creator_username = ?");
    $stmtUpdateQuiz->bind_param("ssis", $quizName, $quizDescription, $quizId, $creatorUsername);
    $stmtUpdateQuiz->execute();

    // Update or insert questions in the database
    foreach ($_POST['question'] as $questionId => $questionData) {
        // Check if the question is new or existing
        if (strpos($questionId, 'new_') === 0) {
            // This is a new question, insert it into the database
            $questionText = $questionData['question'];
            $options = $questionData['options'];
            $correctAnswer = $questionData['correct_answer'];
            $marks = $questionData['marks'];

            $stmtInsertQuestion = $conn->prepare("INSERT INTO questions (quiz_id, question_text, option1, option2, option3, option4, correct_answer, marks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtInsertQuestion->bind_param("isssssii", $quizId, $questionText, $options[0], $options[1], $options[2], $options[3], $correctAnswer, $marks);
            $stmtInsertQuestion->execute();
        } else {
            // This is an existing question, update it in the database
            $questionText = $questionData['question'];
            $options = $questionData['options'];
            $correctAnswer = $questionData['correct_answer'];
            $marks = $questionData['marks'];

            $stmtUpdateQuestion = $conn->prepare("UPDATE questions SET question_text = ?, option1 = ?, option2 = ?, option3 = ?, option4 = ?, correct_answer = ?, marks = ? WHERE question_id = ? AND quiz_id = ?");
            $stmtUpdateQuestion->bind_param("ssssssiii", $questionText, $options[0], $options[1], $options[2], $options[3], $correctAnswer, $marks, $questionId, $quizId);
            $stmtUpdateQuestion->execute();
        }
    }

    // Calculate total marks
    $totalMarks = 0;
    foreach ($_POST['question'] as $questionData) {
        $totalMarks += $questionData['marks'];
    }

    // Update total marks in the quizzes table
    $stmtUpdateTotalMarks = $conn->prepare("UPDATE quizzes SET total_marks = ? WHERE quiz_id = ?");
    $stmtUpdateTotalMarks->bind_param("ii", $totalMarks, $quizId);
    $stmtUpdateTotalMarks->execute();

    // Redirect to a success page
    header('Location: my_quiz.php?quiz_updated=1&username=' . urlencode($creatorUsername));
    exit();
} else {
    // Redirect to an error page if the form is not submitted
    header('Location: error.php');
    exit();
}
?>
