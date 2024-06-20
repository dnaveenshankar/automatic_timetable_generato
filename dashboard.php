<!-- dashboard.php -->

<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quiz Craft</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/css/bootstrap.min.css">
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

        .btn-square {
            width: 150px;
            height: 150px;
            font-size: 18px;
            margin: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8c000;
            color: #fff;
            border: 1px solid #f8c000;
            border-radius: 15px;
            text-decoration: none;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-square:hover {
            background-color: #f8a000;
            border: 1px solid #f8a000;
        }

        h2 {
            color: #000;
        }

        .footer {
            background-color: #333;
            text-align: center;
            color: #fff;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <video class="logo" autoplay muted loop>
        <source src="logo.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <h2>Welcome, <?php echo $username; ?>!</h2>

    <div class="btn-group">
        <a href="create_quiz.php?username=<?php echo urlencode($username); ?>" class="btn btn-primary btn-square">Create Quiz</a>
        <a href="start_quiz.php?username=<?php echo urlencode($username); ?>" class="btn btn-primary btn-square">Start Quiz</a>
        <a href="my_scores.php?username=<?php echo urlencode($username); ?>" class="btn btn-primary btn-square">My Scores</a>
        <a href="my_quiz.php?username=<?php echo urlencode($username); ?>" class="btn btn-danger btn-square">My Quizzes</a>
        <a href="logout.php?username=<?php echo urlencode($username); ?>" class="btn btn-danger btn-square">Logout</a>
    </div>

    <div class="footer">
        Quiz Craft &copy; 2024
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>

</body>
</html>
