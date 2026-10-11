<?php
session_start();
include('db.php');

// አድሚን መሆኑን ማረጋገጥ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

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

$id = $_GET['id'];
$message = '';

// ውጤቱን ማስተካከል (Update)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_grade'])) {
    $quiz       = (float)$_POST['quiz'];
    $assignment = (float)$_POST['assignment'];
    $mid_exam   = (float)$_POST['mid_exam'];
    $final_exam = (float)$_POST['final_exam'];

    $total_mark = $quiz + $assignment + $mid_exam + $final_exam;
    $grade = calculateGrade($total_mark);

    $update_sql = "UPDATE grades SET quiz='$quiz', assignment='$assignment', mid_exam='$mid_exam', final_exam='$final_exam', total_mark='$total_mark', grade='$grade' WHERE id='$id'";
    
    if ($conn->query($update_sql) === TRUE) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $message = "<p style='color:red;'>Error updating record: " . $conn->error . "</p>";
    }
}

// የነባር ውጤቱን መረጃ ማምጣት
$result = $conn->query("SELECT grades.*, users.full_name, courses.course_code FROM grades JOIN users ON grades.student_id = users.id JOIN courses ON grades.course_id = courses.course_id WHERE grades.id='$id'");
$grade_data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Grade - JU Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f1f5f9; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 400px; }
        h3 { margin-bottom: 15px; color: #0f172a; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 4px; }
        input { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px; }
        button { width: 100%; padding: 10px; background-color: #2563eb; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        button:hover { background-color: #1d4ed8; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

    <div class="card">
        <h3>Edit Grade for <?php echo htmlspecialchars($grade_data['full_name']); ?></h3>
        <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">Course: <strong><?php echo $grade_data['course_code']; ?></strong></p>
        
        <?php echo $message; ?>

        <form action="edit_grade.php?id=<?php echo $id; ?>" method="POST">
            <div class="form-group">
                <label>Quiz</label>
                <input type="number" step="0.5" name="quiz" value="<?php echo $grade_data['quiz']; ?>" required>
            </div>
            <div class="form-group">
                <label>Assignment</label>
                <input type="number" step="0.5" name="assignment" value="<?php echo $grade_data['assignment']; ?>" required>
            </div>
            <div class="form-group">
                <label>Mid Exam</label>
                <input type="number" step="0.5" name="mid_exam" value="<?php echo $grade_data['mid_exam']; ?>" required>
            </div>
            <div class="form-group">
                <label>Final Exam</label>
                <input type="number" step="0.5" name="final_exam" value="<?php echo $grade_data['final_exam']; ?>" required>
            </div>
            <button type="submit" name="update_grade">Update Grade</button>
        </form>
        
        <a href="admin_dashboard.php" class="back-link">&larr; Back to Dashboard</a>
    </div>

</body>
</html>