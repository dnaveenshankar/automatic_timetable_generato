<!-- signup.php -->

<?php
// Include the database connection file
include 'db_connection.php';

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if the username already exists
    $checkUsernameStmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $checkUsernameStmt->bind_param("s", $username);
    $checkUsernameStmt->execute();
    $result = $checkUsernameStmt->get_result();

    if ($result->num_rows > 0) {
        echo '<script>alert("Username already exists. Please choose a different username.");</script>';
    } else {
        // Insert user data
        $insertStmt = $conn->prepare("INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)");
        $insertStmt->bind_param("ssss", $name, $username, $email, $password);

        if ($insertStmt->execute()) {
            // Redirect to login page after successful signup
            header('Location: login.php');
            exit();
        } else {
            echo '<script>alert("Error occurred while signing up. Please try again.");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Quiz Craft</title>
    <!-- Bootstrap CSS -->
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
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .signup-form {
            margin-top: 20px;
        }

        .form-group label {
            color: #000;
            text-align: left;
        }

        .center-vertically {
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }

        .already-a-user {
            color: #000;
        }

        video {
            max-width: 100%;
            max-height: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <video autoplay muted loop>
                <source src="quizcraft2.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="col-md-6 center-vertically">
            <hr class="my-4">
            <h2 class="mb-4 text-center" style="color: #f8c000;">Quiz Craft</h2>
            <form class="signup-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
                </div>
                <div class="form-group">
                    <label for="email">Mail ID:</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Choose a password (min 8 characters)" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary">Sign Up</button>
            </form>
            <p class="mt-3 already-a-user text-center">Already have an account? <a href="login.php"
                                                                                   style="color: #f8c000;">Login</a></p>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
