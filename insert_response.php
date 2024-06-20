<?php
// Include the database connection file
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the POST parameters
    $quizId = $_POST['quiz_id'];
    $questionNumber = $_POST['question_number'];
    $userResponse = $_POST['user_response'];
    $username = $_POST['username'];

    // Insert the response into the database
    $stmt = $conn->prepare("INSERT INTO quiz_responses (quiz_id, question_number, user_response, username) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $quizId, $questionNumber, $userResponse, $username);
    if ($stmt->execute()) {
        echo "Response saved successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
} else {
    echo "Invalid request method.";
}
?>
