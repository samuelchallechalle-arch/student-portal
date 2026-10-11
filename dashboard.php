<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$sql_student = "SELECT users.full_name, students.department, students.program, students.year_of_study 
                FROM users 
                LEFT JOIN students ON users.id = students.user_id 
                WHERE users.id = '$user_id'";
$res_student = $conn->query($sql_student);
$student_info = $res_student->fetch_assoc();

$full_name     = $student_info['full_name'] ?? $_SESSION['full_name'];
$student_id
    = $student_info['student_id'] ?? 'RU1234/15.';
$department    = $student_info['department'] ?? 'Electrical & Computer Eng.';
$program       = $student_info['program'] ?? 'Undergraduate (BSc)';
$year_of_study = $student_info['year_of_study'] ?? 'Year 3 - Semester I';
$sql = "SELECT courses.course_code, courses.course_name, courses.credit_hours, 
               grades.quiz, grades.assignment, grades.mid_exam, grades.final_exam, 
               grades.total_mark, grades.grade, grades.semester 
        FROM grades 
        JOIN courses ON grades.course_id = courses.course_id 
        WHERE grades.student_id = '$user_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Quante industrial collage Style</title>
    <style>

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background-color: #f4f7f6; min-height: 100vh; }

        /* Sidebar Styling */
        .sidebar { width: 250px; background-color: #1e293b; color: white; display: flex; flex-direction: column; }
        .sidebar .brand { padding: 20px; font-size: 18px; font-weight: bold; background-color: #0f172a; text-align: center; border-bottom: 1px solid #334155; }
        .sidebar ul { list-style: none; padding: 20px 0; }
        .sidebar ul li a { display: block; padding: 12px 25px; color: #94a3b8; text-decoration: none; font-size: 15px; transition: 0.3s; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background-color: #2563eb; color: white; }

        /* Main Content Styling */
        .main-content { flex: 1; display: flex; flex-direction: column; }
        .topbar { background-color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .user-profile { font-size: 14px; color: #334155; }
        .btn-logout { background-color: #ef4444; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; margin-left: 10px; }

        .container { padding: 30px; }
        
        /* Student Info Card */
        .profile-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px; display: flex; gap: 30px; flex-wrap: wrap; }
        .info-group { flex: 1; min-width: 200px; }
        .info-group label { font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 4px; }
        .info-group p { font-size: 15px; color: #1e293b; font-weight: 600; }


        /* Table Design */
.table-card { 
    background: white; 
    padding: 25px; 
    border-radius: 8px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
}

.table-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-bottom: 15px; 
}

.table-header h3 { 
    font-size: 18px; 
    color: #1e293b; 
}

.btn-add { 
    background-color: #2563eb; 
    color: white; 
    padding: 8px 16px; 
    border-radius: 5px; 
    text-decoration: none; 
    font-size: 14px; 
}

/* የሠንጠረዡን መዋቅር በትክክል እንዲያዝ ማድረግ */
table { 
    width: 100% !important; 
    border-collapse: collapse !important; 
    margin-top: 10px !important; 
    display: table !important; 
}

thead { display: table-header-group !important; }
tbody { display: table-row-group !important; }
tr { display: table-row !important; }

th { 
    display: table-cell !important;
    background-color: #f8fafc !important; 
    color: #475569 !important; 
    text-align: left !important; 
    padding: 12px !important; 
    font-size: 13px !important; 
    text-transform: uppercase !important; 
    border-bottom: 2px solid #e2e8f0 !important; 
} 

td { 
    display: table-cell !important;
    padding: 12px !important; 
    border-bottom: 1px solid #e2e8f0 !important; 
    color: #334155 !important; 
    font-size: 14px !important; 
} 

tr:hover { 
    background-color: #f1f5f9 !important; 
} 

.badge-grade { 
    background-color: #dbeafe !important; 
    color: #1e40af !important; 
    padding: 4px 8px !important; 
    border-radius: 4px !important; 
    font-weight: bold !important; 
}
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="brand">QIC Student Portal</div>
       <ul>
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="add_course.php">Course Registration</a></li>
    <li><a href="grade_report.php">Grade Report</a></li>
    <li><a href="cost_sharing.php">Cost Sharing</a></li>
    <li><a href="dorm_cafe.php">Dormitory & Cafe</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navigation Bar -->
        <div class="topbar">
            <h2>Academic Dashboard</h2>
            <div class="user-profile">
                Welcome, <strong><?php echo htmlspecialchars($full_name); ?></strong>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="container">
            <!-- Student Profile Summary -->
<div class="profile-card">
    <div class="info-group">
        <label>Full Name</label>
        <p><?php echo htmlspecialchars($full_name); ?></p>
    </div>
<div class="info-group">
        <label>ID</label>
        <p><?php echo htmlspecialchars($student_id); ?></p>
    </div>

    <div class="info-group">
        <label>Department</label>
        <p><?php echo htmlspecialchars($department); ?></p>
    </div>
    <div class="info-group">
        <label>Program</label>
        <p><?php echo htmlspecialchars($program); ?></p>
    </div>
    <div class="info-group">
        <label>Academic Year</label>
        <p><?php echo htmlspecialchars($year_of_study); ?></p>
    </div>
    <div style="width: 100%; margin-top: 10px;">
        <a href="edit_profile.php" style="color: #2563eb; text-decoration: none; font-weight: bold; font-size: 14px;">✏️ Edit Profile Info</a>
    </div>
</div>

            <!-- Grade Report Table -->
            <div class="table-card">
                <div class="table-header">
                    <h3>Enrolled Courses & Grade History</h3>
                    <a href="add_course.php" class="btn-add">+ Register New Course</a>
                </div>

                <table>
    <thead>
    <tr>
        <th>Course Code</th>
        <th>Course Title</th>
        <th>Quiz</th>
        <th>Assignment</th>
        <th>Mid</th>
        <th>Final</th>
        <th>Total (100%)</th>
        <th>Grade</th>
    </tr>
</thead>
    <tbody>
        <!-- PHP loop code... -->
   <tbody>
</table>

<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><strong>" . htmlspecialchars($row['course_code']) . "</strong></td>";
    echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
    echo "<td>" . ($row['quiz'] !== null ? $row['quiz'] : '-') . "</td>";
    echo "<td>" . ($row['assignment'] !== null ? $row['assignment'] : '-') . "</td>";
    echo "<td>" . ($row['mid_exam'] !== null ? $row['mid_exam'] : '-') . "</td>";
    echo "<td>" . ($row['final_exam'] !== null ? $row['final_exam'] : '-') . "</td>";
    echo "<td><strong>" . ($row['total_mark'] !== null ? $row['total_mark'] . '%' : '-') . "</strong></td>";
    echo "<td><span style='background:#dbeafe; color:#1e40af; padding:2px 6px; border-radius:3px; font-weight:bold;'>" . ($row['grade'] !== null ? $row['grade'] : '-') . "</span></td>";
    echo "</tr>";
}
} else {
    echo "<tr><td colspan='8' style='text-align:center; color:#64748b; padding:20px;'>No course or grade records found.</td></tr>";
}
?>

</tbody>
</table>
            </div>
        </div>
    </div>

</body>
</html>