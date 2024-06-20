<!-- login.php -->

<?php
// Include the database connection file
include 'db_connection.php';

// Function to authenticate the user
function authenticateUser($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            return $user;
        }
    }

    return null;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Authenticate the user
    $user = authenticateUser($conn, $username, $password);

    if ($user) {
        // Start the session and store user information
        session_start();
        $_SESSION['username'] = $user['username'];
        $_SESSION['name'] = $user['name'];

        // Redirect to the dashboard after successful login
        header('Location: dashboard.php');
        exit();
    } else {
        echo '<script>alert("Invalid username or password. Please try again.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Quiz Craft</title>
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
            box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 1200px; 
            text-align: center;
            overflow: auto;
            max-height: 100vh; 
        }

        .login-form {
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

        .not-a-user {
            color: #000;
        }

        video {
            max-width: 100%;
            max-height: 100%;
        }

        /* Additional CSS Styling */
        h2 {
            color: #f8c000;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #f8c000;
            border-color: #f8c000;
        }

        .btn-primary:hover {
            background-color: #f8a000;
            border-color: #f8a000;
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
            <h2 class="mb-4 text-center">Quiz Craft Login</h2>
            <form class="login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <input type="checkbox" onclick="showPassword()"> Show
                            </span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p class="mt-3 not-a-user text-center">Not a user? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- JavaScript to show/hide password -->
<script>
    function showPassword() {
        var passwordField = document.getElementById("password");
        if (passwordField.type === "password") {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }
</script>

</body>
</html>
