<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id      = $_SESSION['user_id'];
    $course_code  = $_POST['course_code'];
    $course_name  = $_POST['course_name'];
    $credit_hours = $_POST['credit_hours'];
    $semester     = $_POST['semester'];
    $grade        = $_POST['grade'];

    // 1. ኮርሱን ወደ courses ሰንጠረዥ ማስገባት
    $sql_course = "INSERT INTO courses (course_code, course_name, credit_hours) VALUES ('$course_code', '$course_name', '$credit_hours')";
    
    if ($conn->query($sql_course) === TRUE) {
        $course_id = $conn->insert_id;

        // 2. ውጤቱን ወደ grades ሰንጠረዥ ማስገባት
        $sql_grade = "INSERT INTO grades (student_id, course_id, grade, semester) VALUES ('$user_id', '$course_id', '$grade', '$semester')";
        if ($conn->query($sql_grade) === TRUE) {
            header("Location: dashboard.php");
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
    <title>Add Course - Student Portal</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .form-container { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; color: #333; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 10px; }
        .back-link { display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #007bff; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add Course & Grade</h2>
    <?php echo $message; ?>
    <form action="add_course.php" method="POST">
        <input type="text" name="course_code" placeholder="Course Code (e.g., ECE201)" required>
        <input type="text" name="course_name" placeholder="Course Name" required>
        <input type="number" name="credit_hours" placeholder="Credit Hours" min="1" max="6" required>
        <input type="text" name="semester" placeholder="Semester (e.g., Year 2 Sem 1)" required>
        <select name="grade" required>
            <option value="">Select Grade</option>
            <option value="A+">A+</option>
            <option value="A">A</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="F">F</option>
        </select>
        <button type="submit">Save Course</button>
    </form>
    <a href="dashboard.php" class="back-link">Back to Dashboard</a>
</div>

</body>
</html>