<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// ከ100 የሚወጣውን አጠቃላይ ነጥብ ወደ Letter Grade መቀየር
function calculateGrade($total) {
    if ($total >= 90) return 'A+';
    if ($total >= 85) return 'A';
    if ($total >= 80) return 'A-';
    if ($total >= 75) return 'B+';
    if ($total >= 70) return 'B';
    if ($total >= 65) return 'B-';
    if ($total >= 60) return 'C+';
    if ($total >= 50) return 'C';
    if ($total >= 40) return 'D';
    return 'F';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_code  = $_POST['course_code'];
    $course_name  = $_POST['course_name'];
    $credit_hours = $_POST['credit_hours'];
    $semester     = $_POST['semester'];
    
    // የፈተና ውጤቶች
    $quiz       = (float)$_POST['quiz'];         // out of 10/15
    $assignment = (float)$_POST['assignment'];   // out of 10/15
    $mid_exam   = (float)$_POST['mid_exam'];     // out of 30
    $final_exam = (float)$_POST['final_exam'];   // out of 50

    // ድምር ውጤት (Total Assessment)
    $total_mark = $quiz + $assignment + $mid_exam + $final_exam;
    $grade = calculateGrade($total_mark);

    // 1. ኮርሱን ማስገባት
    $sql_course = "INSERT INTO courses (course_code, course_name, credit_hours) VALUES ('$course_code', '$course_name', '$credit_hours')";
    
    if ($conn->query($sql_course) === TRUE) {
        $course_id = $conn->insert_id;

        // 2. የፈተና ውጤቶቹን ማስገባት
        $sql_grade = "INSERT INTO grades (student_id, course_id, quiz, assignment, mid_exam, final_exam, total_mark, grade, semester) 
                      VALUES ('$user_id', '$course_id', '$quiz', '$assignment', '$mid_exam', '$final_exam', '$total_mark', '$grade', '$semester')";
        
        if ($conn->query($sql_grade) === TRUE) {
            header("Location: grade_report.php");
            exit();
        } else {
            $message = "<p style='color:red;'>Grade Error: " . $conn->error . "</p>";
        }
    } else {
        $message = "<p style='color:red;'>Course Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Assessment Grades - JU Student Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background-color: #f4f7f6; min-height: 100vh; justify-content: center; align-items: center; }
        .form-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); width: 450px; }
        h2 { text-align: center; color: #1e293b; margin-bottom: 20px; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px; }
        input { width: 100%; padding: 9px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        button { width: 100%; padding: 11px; background-color: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 15px; font-weight: bold; margin-top: 10px; }
        button:hover { background-color: #1d4ed8; }
        .back-link { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #64748b; font-size: 14px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add Course & Marks</h2>
    <?php echo $message; ?>
    <form action="add_grade.php" method="POST">
        <div class="form-group">
            <label>Course Code</label>
            <input type="text" name="course_code" placeholder="e.g. ECE3102" required>
        </div>
        <div class="form-group">
            <label>Course Title</label>
            <input type="text" name="course_name" placeholder="e.g. Signals and Systems" required>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>Credit Hours</label>
                <input type="number" name="credit_hours" min="1" max="7" required>
            </div>
            <div class="form-group">
                <label>Semester</label>
                <input type="text" name="semester" placeholder="Sem I Year 3" required>
            </div>
        </div>

        <hr style="margin: 15px 0; border: 0; border-top: 1px solid #e2e8f0;">

        <!-- Assessment Marks Input -->
        <div class="grid-2">
            <div class="form-group">
                <label>Quiz Marks</label>
                <input type="number" step="0.5" name="quiz" placeholder="e.g. 10" required>
            </div>
            <div class="form-group">
                <label>Assignment Marks</label>
                <input type="number" step="0.5" name="assignment" placeholder="e.g. 10" required>
            </div>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label>Mid Exam Marks</label>
                <input type="number" step="0.5" name="mid_exam" placeholder="e.g. 30" required>
            </div>
            <div class="form-group">
                <label>Final Exam Marks</label>
                <input type="number" step="0.5" name="final_exam" placeholder="e.g. 50" required>
            </div>
        </div>

        <button type="submit">Calculate & Save</button>
    </form>
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>