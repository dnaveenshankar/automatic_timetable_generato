<?php
// Include the database connection file
include 'db_connection.php';

// Check if the user is logged in, if not redirect to login page
session_start();
if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit;
}

// Retrieve username and quiz ID from previous page
$username = $_SESSION['username'];
$quiz_id = $_GET['quiz_id'];
$_SESSION['quiz_id'] = $quiz_id;

// Fetch questions for the specified quiz from the database
$query = "SELECT * FROM quiz_questions WHERE quiz_id = $quiz_id";
$result = $conn->query($query);

// Process the retrieved questions
$questions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
} else {
    // Handle case when no questions are found
    echo "No questions found for this quiz.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize an array to store user's answers
    $user_answers = [];

    // Iterate over the submitted form data to extract user's answers
    foreach ($_POST as $key => $value) {
        // Check if the input field corresponds to an answer option
        if (strpos($key, 'answer_') === 0) {
            // Extract question ID from the input field name
            $question_id = substr($key, strlen('answer_'));

            // Store question ID and user's answer in the array
            $user_answers[$question_id] = $value;
        }
    }

    // Prepare and execute SQL queries to insert quiz responses into the database
    foreach ($user_answers as $question_id => $user_answer) {
        // Retrieve the correct answer for the current question from the database
        $query = "SELECT correct_answer FROM quiz_questions WHERE question_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $stmt->bind_result($correct_answer);
        $stmt->fetch();
        $stmt->close();
        
        // Determine if the user's answer is correct
        $is_correct = ($user_answer == $correct_answer) ? 1 : 0;

        // Insert the user's response into the quiz_responses table
        $query = "INSERT INTO quiz_responses (username, quiz_id, question_id, user_answer, correct_answer, is_correct) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("siiiii", $username, $quiz_id, $question_id, $user_answer, $correct_answer, $is_correct);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect the user to a confirmation page or any other appropriate page
    header("location: quiz_confirmation.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - Quiz Craft</title>
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
            text-align: left; 
            overflow: auto;
            max-height: 100vh; 
        }

        .quiz-question {
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 20px;
        }

        .option-button {
            margin: 10px;
        }

        .play-tts-button,
        .voice-input-button {
            margin-top: 10px;
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
        <h2>Quiz Questions</h2>
        <div id="languageSelector">
            <label for="languageSelect">Select Language:</label>
            <select id="languageSelect" class="form-control">
                <?php
                // Define supported languages (you can fetch these from a database)
                $languages = [
                    'en' => 'English',
                    'ta' => 'Tamil',
                    'fr' => 'French',
                    'hi' => 'Hindi',
                    'ml' => 'Malayalam',
                    'te' => 'Telugu'
                ];

                // Generate options for each language
                foreach ($languages as $code => $name) {
                    echo '<option value="' . $code . '">' . $name . '</option>';
                }
                ?>
            </select>
            <button id="translateButton" class="btn btn-primary">Translate</button>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?quiz_id=' . $quiz_id; ?>" method="post">

            <?php foreach ($questions as $question): ?>
                <div class="quiz-question">
                    <h5><?php echo $question['question_text']; ?></h5>
                    <div class="quiz-options">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <?php $optionKey = 'option'.$i; ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answer_<?php echo $question['question_id']; ?>" id="option_<?php echo $question['question_id']; ?>_<?php echo $i; ?>" value="<?php echo $i; ?>">
                                <label class="form-check-label" for="option_<?php echo $question['question_id']; ?>_<?php echo $i; ?>">
                                    <?php echo $question[$optionKey]; ?>
                                </label>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <!-- Voice input button -->
                    <button type="button" class="btn btn-primary voice-input-button" onclick="handleVoiceInput(<?php echo $question['question_id']; ?>)">Voice Input</button>

                    <!-- Play button -->
                    <button type="button" class="btn btn-primary play-tts-button" data-text="<?php echo $question['question_text']; ?>">Play Question</button>
                </div>
            <?php endforeach; ?>
            <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
            <input type="submit" class="btn btn-primary" value="Submit Quiz">
        </form>
    </div>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- JavaScript for text-to-speech (TTS) and voice input -->
    <script>
        // Function to play text-to-speech
        function playTTS(text) {
            const synth = window.speechSynthesis;
            const utterance = new SpeechSynthesisUtterance(text);
            synth.speak(utterance);
        }

        // Function to handle voice input
        function handleVoiceInput(questionId) {
            // Check if the browser supports the Web Speech API
            if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
                const recognition = new (webkitSpeechRecognition || SpeechRecognition)();
                recognition.lang = 'en-US';
                recognition.start();

                // Event listener for when speech is recognized
                recognition.onresult = function(event) {
                    const result = event.results[0][0].transcript.trim();
                    const optionLabels = ['A', 'B', 'C', 'D'];
                    
                    // Check if the recognized speech matches any option labels
                    if (optionLabels.includes(result.toUpperCase())) {
                        const selectedOption = result.toUpperCase();
                        const optionIndex = optionLabels.indexOf(selectedOption);
                        const radioButtonId = 'option_' + questionId + '_' + (optionIndex + 1);
                        const radioButton = document.getElementById(radioButtonId);
                        
                        // Check if the corresponding radio button exists and trigger a click event
                        if (radioButton) {
                            radioButton.checked = true;
                            radioButton.click(); // Simulate a click event on the radio button
                        }
                    } else {
                        alert('Invalid option. Please choose from options A, B, C, or D.');
                    }
                };

                // Event listener for errors
                recognition.onerror = function(event) {
                    alert('Error occurred while processing voice input.');
                    console.error('Voice recognition error:', event.error);
                };
            } else {
                alert('Your browser does not support speech recognition.');
            }
        }

        // Event listener for play button (text-to-speech)
        $('.play-tts-button').on('click', function() {
            const text = $(this).data('text');
            playTTS(text);
        });
        // Event listener for translate button
        document.getElementById('translateButton').addEventListener('click', function() {
            var selectedLanguage = document.getElementById('languageSelect').value;
            translateText(selectedLanguage);
        });

        function translateText(targetLang) {
            var elements = document.querySelectorAll('.quiz-question h5, .quiz-options label');
            elements.forEach(function(element) {
                var text = element.textContent;
                var url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=" + targetLang + "&dt=t&q=" + encodeURI(text);
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        element.textContent = data[0][0][0];
                    })
                    .catch(error => console.error('Error:', error));
            });
        }

        // Initial translation to English
        translateText('en'); // Change 'en' to your desired default target language code
    </script>
</body>
</html>
