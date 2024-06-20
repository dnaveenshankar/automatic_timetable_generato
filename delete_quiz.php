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

// Check if the quiz ID is provided in the URL parameter
if (isset($_GET['quiz_id'])) {
    $quizId = $_GET['quiz_id'];

    // Delete related questions first
    $stmtDeleteQuestions = $conn->prepare("DELETE FROM questions WHERE quiz_id = ?");
    $stmtDeleteQuestions->bind_param("i", $quizId);
    $stmtDeleteQuestions->execute();

    // Now delete the quiz
    $stmtDeleteQuiz = $conn->prepare("DELETE FROM quizzes WHERE quiz_id = ? AND creator_username = ?");
    $stmtDeleteQuiz->bind_param("is", $quizId, $creatorUsername);
    $stmtDeleteQuiz->execute();

    // Redirect to the dashboard after successful deletion
    header('Location: dashboard.php');
    exit();
} else {
    // Handle the case where the quiz ID is not provided
    exit("Error: Quiz ID not provided");
}
?>
