<?php
// Include TCPDF library
require_once('tcpdf/tcpdf.php');

// Include the database connection file
include("db_connection.php");

// Get the quiz ID from the query string
$quiz_id = $_GET['quiz_id'];

// Fetch quiz details
$sql_quiz = "SELECT quiz_name, quiz_description, total_marks FROM quizzes WHERE quiz_id = ?";
$stmt_quiz = $conn->prepare($sql_quiz);
$stmt_quiz->bind_param("i", $quiz_id);
$stmt_quiz->execute();
$result_quiz = $stmt_quiz->get_result();

if ($result_quiz->num_rows > 0) {
    $quiz = $result_quiz->fetch_assoc();
    $quiz_name = $quiz['quiz_name'];
    $description = $quiz['quiz_description'];
    $total_marks = $quiz['total_marks'];
    $quiz_code = $quiz_id;

    // Create new PDF document
    $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('Quiz Craft');
    $pdf->SetAuthor('Quiz Craft');
    $pdf->SetTitle('Quiz: ' . $quiz_name);
    $pdf->SetSubject('Quiz Questions');
    $pdf->SetKeywords('Quiz, Questions, TCPDF, PDF, PHP');

    // Set margins
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(0);

    // Fetch questions related to the quiz
    $sql_questions = "SELECT question_text, option1, option2, option3, option4, correct_answer, marks FROM questions WHERE quiz_id = ?";
    $stmt_questions = $conn->prepare($sql_questions);
    $stmt_questions->bind_param("i", $quiz_id);
    $stmt_questions->execute();
    $result_questions = $stmt_questions->get_result();

    // Add a page
    $pdf->AddPage();

    // Set background color for all pages
    $pdf->SetFillColor(255, 255, 204); // Light yellow

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // Add watermark
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->SetTextColor(204, 204, 204); // Light gray
    $pdf->SetXY(55, 10);
    $pdf->Cell(0, 0, 'Quiz Craft', 0, 1, 'C');

    // Content
    $pdf->SetFont('helvetica', 'B', 16); // Bold, font size 16
    $pdf->SetTextColor(0, 0, 0); // Dark black
    $pdf->SetXY(10, 30); // Positioning
    $pdf->Cell(0, 0, 'Quiz: ' . $quiz_name, 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12); // Regular, font size 12
    $pdf->SetXY(10, 40);
    $pdf->MultiCell(0, 0, 'Description: ' . $description, 0, 'L');
    $pdf->SetXY(10, 50);
    $pdf->Cell(0, 0, 'Total Marks: ' . $total_marks, 0, 1, 'L');
    $pdf->Cell(0, 0, 'Quiz Code: ' . $quiz_code, 0, 1, 'L');

    // Content separation line
    $pdf->Line(10, 60, $pdf->getPageWidth() - 10, 60);

    // Questions
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetXY(10, 65);
    $pdf->Cell(0, 0, 'Questions:', 0, 1, 'L');

    // Questions content
    $pdf->SetFont('helvetica', '', 12);
    $question_number = 1;
    while ($row = $result_questions->fetch_assoc()) {
        $pdf->SetX(10);
        $pdf->MultiCell(0, 10, "Question " . $question_number . ": " . $row['question_text'], 0, 'L');
        $pdf->MultiCell(0, 10, "Options:\nA. " . $row['option1'] . "\nB. " . $row['option2'] . "\nC. " . $row['option3'] . "\nD. " . $row['option4'], 0, 'L');
        $correct_answer = $row['correct_answer'] == 0 ? 'A' : 'B';
        $pdf->MultiCell(0, 10, "Correct Answer: " . $correct_answer, 0, 'L');
        $pdf->MultiCell(0, 10, "Marks: " . $row['marks'], 0, 'L');
        $pdf->Ln(5); // Add some space between questions
        $question_number++;
    }

    // Close and output PDF document
    $pdf->Output('quiz_' . $quiz_id . '.pdf', 'D');

    // Close statements and connection
    $stmt_quiz->close();
    $stmt_questions->close();
    $conn->close();
} else {
    echo "Quiz not found.";
}
?>
