<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Quiz</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8c000;
            color: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
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

        h1 {
            color: #f8c000;
            margin-bottom: 20px;
        }

        .question-number {
            font-weight: bold;
        }

        .download-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .correct-answer {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <a href="download_quiz_pdf.php?quiz_id=<?php echo isset($_GET['quiz_id']) ? $_GET['quiz_id'] : ''; ?>" class="btn btn-primary download-btn">Download as PDF</a>
        <h1>View Quiz</h1>
        <?php
        // Include database connection and fetch quiz details
        include 'db_connection.php';

        if (isset($_GET['quiz_id'])) {
            $quizId = $_GET['quiz_id'];

            // Fetch quiz details from the database
            $stmt = $conn->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
            $stmt->bind_param("i", $quizId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $quiz = $result->fetch_assoc();
                echo "<h2>Quiz Name: " . $quiz['quiz_name'] . "</h2>";
                echo "<p>Description: " . $quiz['quiz_description'] . "</p>";
                echo "<p>Total Marks: " . $quiz['total_marks'] . "</p>";

                // Fetch questions related to the quiz
                $stmtQuestions = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
                $stmtQuestions->bind_param("i", $quizId);
                $stmtQuestions->execute();
                $resultQuestions = $stmtQuestions->get_result();

                if ($resultQuestions->num_rows > 0) {
                    echo "<table class='table'>";
                    echo "<thead><tr><th>#</th><th>Question</th><th>Options</th><th>Marks</th></tr></thead>";
                    echo "<tbody>";
                    $questionNumber = 1;
                    while ($row = $resultQuestions->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='question-number'>$questionNumber</td>";
                        echo "<td>" . $row['question_text'] . "</td>";
                        echo "<td>";
                        $options = array($row['option1'], $row['option2'], $row['option3'], $row['option4']);
                        foreach ($options as $key => $option) {
                            echo "<div";
                            if ($key == $row['correct_answer']) {
                                echo " class='correct-answer'";
                            }
                            echo ">" . $option . "</div>";
                        }
                        echo "</td>";
                        echo "<td>" . $row['marks'] . "</td>";
                        echo "</tr>";
                        $questionNumber++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p>No questions found for this quiz.</p>";
                }
            } else {
                echo "<p>Quiz not found.</p>";
            }

            $stmt->close();
            $stmtQuestions->close();
            $conn->close();
        } else {
            echo "<p>Quiz ID not provided.</p>";
        }
        ?>
        <a href="my_quiz.php" class="btn btn-primary">Back to My Quizzes</a>
    </div>
</body>
</html>
