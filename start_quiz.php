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

// Check if the quiz ID is provided
if (isset($_GET['quiz_id'])) {
    $quizId = $_GET['quiz_id'];

    // Check if the user has already attended the quiz
    $stmt = $conn->prepare("SELECT * FROM quiz_responses WHERE quiz_id = ? AND username = ?");
    $stmt->bind_param("is", $quizId, $creatorUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the user has already attended the quiz, display an alert and redirect them back to the dashboard
    if ($result->num_rows > 0) {
        echo '<script>alert("You have already attended this quiz."); window.location.href = "dashboard.php";</script>';
        exit();
    }

    // Close the statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Quiz</title>
    <style>
    body {
        background-color: #f8c000;
        color: #fff;
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
        max-width: 500px; 
        text-align: center;
    }

    .logo {
        width: 80%;
        max-width: 100px;
        margin-bottom: 20px;
        border-radius: 10px;
    }

    .btn-group {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    .btn-start, .btn-back {
        width: 150px;
        height: 50px;
        font-size: 18px;
        margin: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8c000;
        color: #fff;
        border: none;
        border-radius: 15px;
        text-decoration: none;
        transition: background-color 0.3s ease, border-color 0.3s ease;
        cursor: pointer;
    }

    .btn-start:hover, .btn-back:hover {
        background-color: #f8a000;
    }

    h2 {
        color: #000;
    }

    .quiz-code-input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-sizing: border-box;
    }
</style>

</head>
<body>

<div class="container">
    <h2>Ready to start the quiz?</h2>
    <form action="quiz.php" method="get">
    <input type="hidden" name="username" value="<?php echo urlencode($creatorUsername); ?>">
    <input type="text" name="quiz_id" class="quiz-code-input" placeholder="Enter Quiz Code" required>
    <div class="btn-group">
        <button type="submit" class="btn-start">Start Quiz</button>
        <a href="dashboard.php" class="btn-back">Back</a>
    </div>
</form>

</div>

</body>
</html>
