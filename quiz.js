document.addEventListener('DOMContentLoaded', function() {
    let currentQuestionIndex = 0;
    let questions = [];
    
    // Fetch questions from the server
    function fetchQuestions() {
        const quiz_id = document.getElementById('quiz_id').value;
        const formData = new FormData();
        formData.append('quiz_id', quiz_id);

        fetch('quiz.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            questions = data;
            displayQuestion();
        })
        .catch(error => console.error('Error:', error));
    }

    // Function to display current question and options
    function displayQuestion() {
        const questionContainer = document.getElementById('question');
        const optionsContainer = document.getElementById('options');
        
        questionContainer.innerHTML = questions[currentQuestionIndex].question_text;
        optionsContainer.innerHTML = '';

        for (let i = 1; i <= 4; i++) {
            const option = document.createElement('input');
            option.type = 'radio';
            option.name = 'option';
            option.value = i;
            option.id = 'option' + i;
            const label = document.createElement('label');
            label.setAttribute('for', 'option' + i);
            label.textContent = questions[currentQuestionIndex]['option' + i];
            optionsContainer.appendChild(option);
            optionsContainer.appendChild(label);
        }
    }

    // Event listener for the Next button
    document.getElementById('nextBtn').addEventListener('click', function() {
        currentQuestionIndex++;
        if (currentQuestionIndex < questions.length) {
            displayQuestion();
        } else {
            // Hide next button if no more questions
            document.getElementById('nextBtn').style.display = 'none';
        }
    });

    // Fetch questions on page load
    fetchQuestions();
});
