<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// 1. ነባር መረጃዎችን ከዳታቤዝ ማምጣት
$sql = "SELECT users.full_name, users.email, students.department, students.program, students.year_of_study 
        FROM users 
        LEFT JOIN students ON users.id = students.user_id 
        WHERE users.id = '$user_id'";
$result = $conn->query($sql);
$student = $result->fetch_assoc();

// 2. Form Submit ሲደረግ መረጃውን ማዘመን (Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name     = $_POST['full_name'];
    $student_id = $_POST['student_id'];
    $department    = $_POST['department'];
    $program       = $_POST['program'];
    $year_of_study = $_POST['year_of_study'];

    // Update users table 
$sql_user = "UPDATE users SET full_name = '$full_name' WHERE id = '$user_id'";
$conn->query($sql_user);
$_SESSION['full_name'] = $full_name;

    // Check if student row exists, then update or insert
    $check_student = "SELECT * FROM students WHERE user_id = '$user_id'";

if ($conn->query($check_student)->num_rows > 0) {
    $sql_student = "UPDATE students SET student_id = '$student_id', department = '$department', program = '$program', year_of_study = '$year_of_study' WHERE user_id = '$user_id'";
} else {
    $sql_student = "INSERT INTO students (user_id, student_id, department, program, year_of_study) VALUES ('$user_id', '$student_id_code', '$department', '$program', '$year_of_study')";
}
    if ($conn->query($sql_student) === TRUE) {
        $message = "<p style='color:green; text-align:center;'>Profile updated successfully!</p>";
        // መረጃውን እንደገና Refresh ማድረግ
        $student['full_name'] = $full_name;
        $student['student_id'] = $student_id;
        $student['department'] = $department;
        $student['program'] = $program;
        $student['year_of_study'] = $year_of_study;
    } else {
        $message = "<p style='color:red; text-align:center;'>Error updating profile: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - JU Student Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background-color: #f4f7f6; min-height: 100vh; }
        .sidebar { width: 250px; background-color: #1e293b; color: white; display: flex; flex-direction: column; }
        .sidebar .brand { padding: 20px; font-size: 18px; font-weight: bold; background-color: #0f172a; text-align: center; border-bottom: 1px solid #334155; }
        .sidebar ul { list-style: none; padding: 20px 0; }
        .sidebar ul li a { display: block; padding: 12px 25px; color: #94a3b8; text-decoration: none; font-size: 15px; transition: 0.3s; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background-color: #2563eb; color: white; }
        .main-content { flex: 1; display: flex; flex-direction: column; }
        .topbar { background-color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .container { padding: 30px; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #334155; font-size: 14px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; font-size: 14px; }
        .btn-submit { background-color: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; width: 100%; font-size: 15px; font-weight: 600; }
        .btn-submit:hover { background-color: #1d4ed8; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">JU Student Portal</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="edit_profile.php" class="active">Edit Profile</a></li>
            <li><a href="add_course.php">Course Registration</a></li>
            <li><a href="grade_report.php">Grade Report</a></li>
            <li><a href="cost_sharing.php">Cost Sharing</a></li>
            <li><a href="dorm_cafe.php">Dormitory & Cafe</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h2>Edit Profile Information</h2>
            <div>Welcome, <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong> | <a href="logout.php" style="color:#ef4444; text-decoration:none;">Logout</a></div>
        </div>

        <div class="container">
            <div class="card">
                <h3 style="margin-bottom: 20px; color:#1e293b;">Update Your Details</h3>
                <?php echo $message; ?>
                <form action="edit_profile.php" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
    <label>Student ID (e.g. RU/1234/15)</label>
    <input type="text" name="student_id" value="<?php echo htmlspecialchars($student['student_id_code'] ?? 'RU/1234/15'); ?>" required>
</div>


                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" name="department" value="<?php echo htmlspecialchars($student['department'] ?? 'Electrical & Computer Eng.'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Program</label>
                        <select name="program" required>
                            <option value="Undergraduate (BSc)" <?php if(($student['program'] ?? '') == 'Undergraduate (BSc)') echo 'selected'; ?>>Undergraduate (BSc)</option>
                            <option value="Postgraduate (MSc)" <?php if(($student['program'] ?? '') == 'Postgraduate (MSc)') echo 'selected'; ?>>Postgraduate (MSc)</option>
                            <option value="PhD" <?php if(($student['program'] ?? '') == 'PhD') echo 'selected'; ?>>PhD</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Academic Year & Semester</label>
                        <input type="text" name="year_of_study" value="<?php echo htmlspecialchars($student['year_of_study'] ?? 'Year 3 - Semester I'); ?>" required>
                    </div>
                    <button type="submit" class="btn-submit">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>