<?php
// Include the database connection file
require_once "db_connection.php";

// Check if all required parameters are received
if (isset($_POST['quiz_id'], $_POST['username'], $_POST['question_id'], $_POST['user_answer'])) {
    // Sanitize and retrieve the submitted data
    $quizId = $_POST['quiz_id'];
    $username = $_POST['username'];
    $questionId = $_POST['question_id'];
    $userAnswer = $_POST['user_answer'];

    // Insert the user's answer into the database
    $sql = "INSERT INTO quiz_responses (quiz_id, username, question_id, user_answer) VALUES ('$quizId', '$username', '$questionId', '$userAnswer')";
    if (mysqli_query($conn, $sql)) {
        // Answer inserted successfully
        echo "Answer submitted successfully.";
    } else {
        // Error inserting answer
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // Required parameters missing
    echo "Error: Required parameters are missing!";
}

// Close the database connection
mysqli_close($conn);
?>