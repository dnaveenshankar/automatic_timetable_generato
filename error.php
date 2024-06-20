<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
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

        h1 {
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
    <div class="container mt-5">
        <h1>An Error Occurred</h1>
        <p>Sorry, an error occurred while processing your request.</p>
        <a href="javascript:history.go(-1);" class="btn btn-primary">Go Back</a>
    </div>
</body>
</html>
