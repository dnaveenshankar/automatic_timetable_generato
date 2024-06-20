<?php
session_start();

include 'db_connection.php';

// Retrieve the username from the URL parameter or session
if (isset($_GET['username'])) {
    $username = $_GET['username'];
} elseif (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    exit("Error: Username not provided");
}

// Fetch quizzes associated with the username
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE creator_username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Quizzes</title>
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

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table th {
            background-color: #007bff;
            color: #fff;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 123, 255, 0.1);
        }

        .table-striped tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.2);
        }

        .btn-action {
            margin-right: 5px;
        }

        .delete-btn {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .delete-btn:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-4">My Quizzes</h1>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Quiz ID</th>
                <th>Quiz Name</th>
                <th>Quiz Description</th>
                <th>Total Marks</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            // Iterate through the retrieved quizzes and display them in table rows
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['quiz_id'] . "</td>";
                echo "<td>" . $row['quiz_name'] . "</td>";
                echo "<td>" . $row['quiz_description'] . "</td>";
                echo "<td>" . $row['total_marks'] . "</td>";
                echo "<td>";
                echo "<a href='view_quiz.php?quiz_id=" . $row['quiz_id'] . "' class='btn btn-primary btn-action'>View Quiz</a>";
                echo "<a href='participants.php?quiz_id=" . $row['quiz_id'] . "' class='btn btn-success btn-action'>Participants</a>";
                echo "<a href='edit_quiz.php?quiz_id=" . $row['quiz_id'] . "' class='btn btn-warning btn-action'>Edit</a>";
                echo "<button class='btn btn-danger btn-action delete-btn' data-quiz-id='" . $row['quiz_id'] . "'>Delete</button>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <a href="dashboard.php" class="btn btn-secondary mt-4">Back</a>
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function () {
        // Delete button click event
        $('.delete-btn').click(function () {
            var quizId = $(this).data('quiz-id');
            var confirmDelete = confirm("Are you sure you want to delete this quiz?");
            if (confirmDelete) {
                // Redirect to delete_quiz.php with quiz_id parameter for deletion
                window.location.href = 'delete_quiz.php?quiz_id=' + quizId;
            }
        });
    });
</script>

</body>
</html>

