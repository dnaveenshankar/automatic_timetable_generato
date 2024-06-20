<?php
// Start the session
session_start();

// Include the database connection file
include 'db_connection.php';

// Retrieve the username from the session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    // Handle the case where the username is not set in the session
    exit("Error: User not logged in");
}

// Fetch total quiz scores for the logged-in user from quiz_responses table
$stmt = $conn->prepare("SELECT quiz_id, SUM(marks_scored) AS total_marks FROM quiz_responses WHERE username = ? GROUP BY quiz_id");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Close the statement
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Scores</title>
    <style>
        body {
            background-color: #f8c000;
            color: #000;
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
            max-width: 800px; 
            text-align: center;
            overflow: auto;
            max-height: 100vh; 
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8c000;
            color: #fff;
        }

        tr:hover {
            background-color: #f8a000;
        }

        .btn-back {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 18px;
            background-color: #f8c000;
            color: #fff;
            border: none;
            border-radius: 15px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #f8a000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Scores</h1>
        <table>
            <thead>
                <tr>
                    <th>Quiz ID</th>
                    <th>Total Marks Scored</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through quiz scores and display them in a table
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['quiz_id'] . "</td>";
                    echo "<td>" . $row['total_marks'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
</body>
</html>
