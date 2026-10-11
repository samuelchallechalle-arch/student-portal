<?php
session_start();
include('db.php');

// አድሚን መሆኑን ማረጋገጥ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = '';

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_grade'])) {
    // 1. ከፎርሙ የሚመጡ መረጃዎችን መቀበል
    $student_user_id = $_POST['student_user_id'];
    $course_code     = $_POST['course_code'];
    $course_name     = $_POST['course_name'];
    $credit_hours    = $_POST['credit_hours'];
    $semester        = $_POST['semester'];
    
    $quiz       = (float)$_POST['quiz'];
    $assignment = (float)$_POST['assignment'];
    $mid_exam   = (float)$_POST['mid_exam'];
    $final_exam = (float)$_POST['final_exam'];

    $total_mark = $quiz + $assignment + $mid_exam + $final_exam;
    $grade = calculateGrade($total_mark);

    // 2. ኮርሱ አስቀድሞ በዳታቤዝ ውስጥ መኖሩን ማረጋገጥ
   // 1. ኮርሱ በዳታቤዝ ውስጥ መኖሩን ማረጋገጥ
    $check_course = "SELECT course_id FROM courses WHERE course_code = '$course_code'";
    $res_course = $conn->query($check_course);

    if ($res_course->num_rows > 0) {
        $course_row = $res_course->fetch_assoc();
        $course_id = $course_row['course_id'];
    } else {
        $sql_course = "INSERT INTO courses (course_code, course_name, credit_hours) VALUES ('$course_code', '$course_name', '$credit_hours')";
        $conn->query($sql_course);
        $course_id = $conn->insert_id;
    }

    // 2. ውጤቱን ማስገባት (አስቀድሞ ካለ ራሱ በራስ-ሰር Update እንዲያደርግ)
    $sql_grade = "INSERT INTO grades (student_id, course_id, quiz, assignment, mid_exam, final_exam, total_mark, grade, semester) 
                  VALUES ('$student_user_id', '$course_id', '$quiz', '$assignment', '$mid_exam', '$final_exam', '$total_mark', '$grade', '$semester')
                  ON DUPLICATE KEY UPDATE 
                  quiz = '$quiz', assignment = '$assignment', mid_exam = '$mid_exam', final_exam = '$final_exam', total_mark = '$total_mark', grade = '$grade'";

    if ($conn->query($sql_grade) === TRUE) {
        $message = "<p style='color:green; font-weight:bold;'>Grade saved successfully!</p>";
    } else {
        $message = "<p style='color:red;'>Grade Error: " . $conn->error . "</p>";
    
    }
}

// 2. የተማሪዎችን ዝርዝር ማምጣት
$students_query = "SELECT id, full_name, username FROM users WHERE role = 'student'";
$students_list = $conn->query($students_query);

// 3. የተመዘገቡ ውጤቶችን በሙሉ ማምጣት
$all_grades_query = "SELECT grades.id AS grade_id, users.full_name, courses.course_code, courses.course_name, grades.quiz, grades.assignment, grades.mid_exam, grades.final_exam, grades.total_mark, grades.grade 
                     FROM grades 
                     JOIN users ON grades.student_id = users.id 
                     JOIN courses ON grades.course_id = courses.course_id";
$all_grades_result = $conn->query($all_grades_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - JU Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background-color: #f1f5f9; min-height: 100vh; }
        .sidebar { width: 250px; background-color: #0f172a; color: white; display: flex; flex-direction: column; }
        .sidebar .brand { padding: 20px; font-size: 18px; font-weight: bold; background-color: #020617; text-align: center; border-bottom: 1px solid #1e293b; }
        .sidebar ul { list-style: none; padding: 20px 0; }
        .sidebar ul li a { display: block; padding: 12px 25px; color: #94a3b8; text-decoration: none; font-size: 15px; }
        .sidebar ul li a.active { background-color: #2563eb; color: white; }
        .main-content { flex: 1; display: flex; flex-direction: column; }
        .topbar { background-color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .container { padding: 30px; display: grid; grid-template-columns: 1fr 2fr; gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; font-size: 12px; font-weight: bold; color: #475569; margin-bottom: 3px; }
        input, select { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        button { width: 100%; padding: 10px; background-color: #16a34a; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        th { background-color: #f8fafc; color: #475569; text-align: left; padding: 10px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">QIC Portal Admin</div>
        <ul>
            <li><a href="admin_dashboard.php" class="active">Manage Grades</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h2>Registrar / Teacher Dashboard</h2>
            <div>Logged in as: <strong>Admin</strong> | <a href="logout.php" style="color:#ef4444; text-decoration:none;">Logout</a></div>
        </div>

        <div class="container">
            <!-- Grade Entry Form -->
            <div class="card">
                <h3>Enter Student Grade</h3>
                <br>
                <?php echo $message; ?>
                <form action="admin_dashboard.php" method="POST">
                    <div class="form-group">
                        <label>Select Student</label>
                        <select name="student_user_id" required>
                            <option value="">-- Select Student --</option>
                            <?php 
                            while($st = $students_list->fetch_assoc()) {
                                echo "<option value='".$st['id']."'>".$st['full_name']." (".$st['username'].")</option>";
                            }
                            ?>
                        </select>
                    </div>
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
                            <label>Cr. Hours</label>
                            <input type="number" name="credit_hours" required>
                        </div>
                        <div class="form-group">
                            <label>Semester</label>
                            <input type="text" name="semester" placeholder="Sem I Year 3" required>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Quiz (10/15)</label>
                            <input type="number" step="0.5" name="quiz" required>
                        </div>
                        <div class="form-group">
                            <label>Assignment (10/15)</label>
                            <input type="number" step="0.5" name="assignment" required>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Mid Exam (30)</label>
                            <input type="number" step="0.5" name="mid_exam" required>
                        </div>
                        <div class="form-group">
                            <label>Final Exam (50)</label>
                            <input type="number" step="0.5" name="final_exam" required>
                        </div>
                    </div>
                    <button type="submit" name="submit_grade">Submit Student Grade</button>
                </form>
            </div>

            <!-- Grade Display Table -->
            <div class="card">
                <h3>All Submitted Student Grades</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Quiz</th>
                            <th>Assign.</th>
                            <th>Mid</th>
                            <th>Final</th>
                            <th>Total</th>
                            <th>Grade</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($all_grades_result->num_rows > 0) {
                            while($row = $all_grades_result->fetch_assoc()) {
                                echo "<tr>
                                        <td><strong>" . htmlspecialchars($row['full_name']) . "</strong></td>
                                        <td>" . htmlspecialchars($row['course_code']) . "</td>
                                        <td>" . $row['quiz'] . "</td>
                                        <td>" . $row['assignment'] . "</td>
                                        <td>" . $row['mid_exam'] . "</td>
                                        <td>" . $row['final_exam'] . "</td>
                                        <td><strong>" . $row['total_mark'] . "%</strong></td>
                                        <td><span style='background:#dbeafe; color:#1e40af; padding:2px 6px; border-radius:3px; font-weight:bold;'>" . $row['grade'] . "</span></td>
                                        <td><a href='edit_grade.php?id=" . $row['grade_id'] . "' style='background:#2563eb; color:white; padding:4px 8px; text-decoration:none; border-radius:4px; font-size:12px;'>Edit</a></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align:center;'>No grades submitted yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>


