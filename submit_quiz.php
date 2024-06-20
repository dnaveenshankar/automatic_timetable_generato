<?php
// Include the database connection file
include 'db_connection.php';

// Retrieve quiz ID from the previous page or wherever it's coming from
if (isset($_POST['quiz_id'])) {
    $quizId = $_POST['quiz_id'];
} else {
    // Handle the case where quiz ID is not provided
    exit("Error: Quiz ID not provided");
}

// Retrieve username from the previous page or wherever it's coming from
if (isset($_POST['username'])) {
    $username = $_POST['username'];
} else {
    // Handle the case where username is not provided
    exit("Error: Username not provided");
}

// Count the total number of questions for the given quiz ID
$stmt = $conn->prepare("SELECT COUNT(*) AS total_questions FROM questions WHERE quiz_id = ?");
$stmt->bind_param("i", $quizId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalQuestions = $row['total_questions'];

// Close the statement
$stmt->close();

// Loop through each question to retrieve the user's response
for ($i = 1; $i <= $totalQuestions; $i++) {
    // Construct the field names for user's response and correct answer
    $userResponseField = "question_" . $i . "_response";
    $correctAnswerField = "question_" . $i . "_correct";

    // Check if the user's response is set
    if (isset($_POST[$userResponseField])) {
        $userResponse = $_POST[$userResponseField];
        // Retrieve the correct answer for this question (You may fetch this from the database)
        $stmt = $conn->prepare("SELECT correct_answer FROM questions WHERE quiz_id = ? AND question_number = ?");
        $stmt->bind_param("ii", $quizId, $i);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $correctAnswer = $row['correct_answer'];

        // Determine if the user's response is correct
        $marksScored = ($userResponse == $correctAnswer) ? 1 : 0;

        // Store the response in the database
        $stmt = $conn->prepare("INSERT INTO quiz_responses (quiz_id, username, question_number, user_answer, correct_answer, marks_scored) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isissi", $quizId, $username, $i, $userResponse, $correctAnswer, $marksScored);
        $stmt->execute();
        $stmt->close();
    } else {
        // Handle the case where user's response is not set
        exit("Error: User's response for question " . $i . " not provided");
    }
}

// Redirect the user to a confirmation page or back to the quiz dashboard
header("Location: quiz_confirmation.php");
exit();
?>
