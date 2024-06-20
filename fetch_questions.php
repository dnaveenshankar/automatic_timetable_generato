<?php
// Include database connection file
require_once "db_connection.php";

// Check if question index is provided
if (isset($_GET['question_index'])) {
    // Retrieve question index
    $questionIndex = intval($_GET['question_index']);

    // Retrieve quiz ID from GET parameters
    $quizId = $_GET['quiz_id'];

    // Query to fetch the question based on quiz ID and question index
    $sql = "SELECT * FROM questions WHERE quiz_id = '$quizId' LIMIT $questionIndex, 1";
    $result = mysqli_query($conn, $sql);

    // Check if query was successful
    if ($result) {
        // Fetch the question as an associative array
        $question = mysqli_fetch_assoc($result);

        // Check if a question was found
        if ($question) {
            // Output the question HTML
            echo '<div class="question">';
            echo '<h3>' . htmlspecialchars($question['question_text']) . '</h3>';
            echo '<div class="options">';
            for ($i = 1; $i <= 4; $i++) {
                echo '<input type="radio" id="option_' . $question['question_id'] . '_' . $i . '" name="user_answer" value="' . $i . '" required>';
                echo '<label for="option_' . $question['question_id'] . '_' . $i . '">' . htmlspecialchars($question['option' . $i]) . '</label>';
            }
            echo '</div>';
            echo '</div>';
            echo '<button id="nextButton">Next</button>';
        } else {
            // No more questions, send an empty response
            echo '';
        }
    } else {
        // Query failed, send error message
        echo 'Error: ' . mysqli_error($conn);
    }
} else {
    // Question index not provided, send error message
    echo 'Error: Question index is missing!';
}

// Close database connection
mysqli_close($conn);
?>
