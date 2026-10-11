<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Letter Grade ወደ Grade Point የሚቀይር PHP Function
function getGradePoint($grade) {
    switch (strtoupper($grade)) {
        case 'A+':
        case 'A':  return 4.0;
        case 'A-': return 3.75;
        case 'B+': return 3.5;
        case 'B':  return 3.0;
        case 'B-': return 2.75;
        case 'C+': return 2.5;
        case 'C':  return 2.0;
        case 'D':  return 1.0;
        case 'F':  return 0.0;
        default:   return 0.0;
    }
}

// የተማሪውን ውጤቶች ከዳታቤዝ ማምጣት
$sql = "SELECT courses.course_code, courses.course_name, courses.credit_hours, grades.grade, grades.semester 
        FROM grades 
        JOIN courses ON grades.course_id = courses.course_id 
        WHERE grades.student_id = '$user_id'";
$result = $conn->query($sql);

$total_credits = 0;
$total_points = 0;
$courses_data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ch = (int)$row['credit_hours'];
        $gp = getGradePoint($row['grade']);
        $points = $ch * $gp;

        $total_credits += $ch;
        $total_points += $points;

        $row['points'] = $points;
        $courses_data[] = $row;
    }
}

// CGPA ስሌት
$cgpa = ($total_credits > 0) ? number_format($total_points / $total_credits, 2) : "0.00";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Report - JU Student Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background-color: #f4f7f6; min-height: 100vh; }

        /* Sidebar Styling */
        .sidebar { width: 250px; background-color: #1e293b; color: white; display: flex; flex-direction: column; }
        .sidebar .brand { padding: 20px; font-size: 18px; font-weight: bold; background-color: #0f172a; text-align: center; border-bottom: 1px solid #334155; }
        .sidebar ul { list-style: none; padding: 20px 0; }
        .sidebar ul li a { display: block; padding: 12px 25px; color: #94a3b8; text-decoration: none; font-size: 15px; transition: 0.3s; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background-color: #2563eb; color: white; }

        /* Main Content */
        .main-content { flex: 1; display: flex; flex-direction: column; }
        .topbar { background-color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .container { padding: 30px; }

        /* Summary Cards */
        .summary-cards { display: flex; gap: 20px; margin-bottom: 25px; }
        .card-stat { background: white; padding: 20px; border-radius: 8px; flex: 1; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-left: 5px solid #2563eb; }
        .card-stat h4 { color: #64748b; font-size: 13px; text-transform: uppercase; }
        .card-stat p { font-size: 24px; font-weight: bold; color: #1e293b; margin-top: 5px; }

        /* Table */
        .table-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #f8fafc; color: #475569; text-align: left; padding: 12px; font-size: 13px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; color: #334155; font-size: 14px; }
        .badge { background-color: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="brand">JU Student Portal</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add_course.php">Course Registration</a></li>
            <li><a href="grade_report.php" class="active">Grade Report</a></li>
            <li><a href="#">Cost Sharing</a></li>
            <li><a href="#">Dormitory & Cafe</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="topbar">
            <h2>Official Grade Report</h2>
            <div>Welcome, <strong><?php echo htmlspecialchars($full_name); ?></strong> | <a href="logout.php" style="color:#ef4444; text-decoration:none;">Logout</a></div>
        </div>

        <div class="container">
            <!-- GPA Summary Cards -->
            <div class="summary-cards">
                <div class="card-stat">
                    <h4>Total Credit Hours</h4>
                    <p><?php echo $total_credits; ?></p>
                </div>
                <div class="card-stat">
                    <h4>Total Points</h4>
                    <p><?php echo $total_points; ?></p>
                </div>
                <div class="card-stat" style="border-left-color: #10b981;">
                    <h4>Cumulative GPA (CGPA)</h4>
                    <p><?php echo $cgpa; ?></p>
                </div>
            </div>

            <!-- Detailed Table -->
            <div class="table-card">
                <h3>Academic Transcript Details</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Title</th>
                            <th>Cr. Hrs</th>
                            <th>Semester</th>
                            <th>Grade</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($courses_data) > 0) {
                            foreach ($courses_data as $course) {
                                echo "<tr>
                                        <td><strong>" . htmlspecialchars($course['course_code']) . "</strong></td>
                                        <td>" . htmlspecialchars($course['course_name']) . "</td>
                                        <td>" . htmlspecialchars($course['credit_hours']) . "</td>
                                        <td>" . htmlspecialchars($course['semester']) . "</td>
                                        <td><span class='badge'>" . htmlspecialchars($course['grade']) . "</span></td>
                                        <td>" . htmlspecialchars($course['points']) . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; padding:20px;'>No grade records available.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>