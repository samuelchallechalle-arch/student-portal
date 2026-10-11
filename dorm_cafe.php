<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dorm & Cafe - JU Student Portal</title>
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

        .grid-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card h3 { color: #1e293b; font-size: 18px; margin-bottom: 15px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; }
        
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f8fafc; }
        .info-row label { color: #64748b; font-size: 14px; font-weight: 500; }
        .info-row span { color: #0f172a; font-size: 14px; font-weight: 600; }
        .badge-cafe { background-color: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="brand">JU Student Portal</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add_course.php">Course Registration</a></li>
            <li><a href="grade_report.php">Grade Report</a></li>
            <li><a href="cost_sharing.php">Cost Sharing</a></li>
            <li><a href="dorm_cafe.php" class="active">Dormitory & Cafe</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="topbar">
            <h2>Dormitory & Cafeteria Information</h2>
            <div>Welcome, <strong><?php echo htmlspecialchars($full_name); ?></strong> | <a href="logout.php" style="color:#ef4444; text-decoration:none;">Logout</a></div>
        </div>

        <div class="container">
            <div class="grid-container">
                <!-- Dormitory Card -->
                <div class="card">
                    <h3>Dormitory Allocation</h3>
                    <div class="info-row">
                        <label>Campus:</label>
                        <span>Main Campus</span>
                    </div>
                    <div class="info-row">
                        <label>Block Number:</label>
                        <span>Block 104</span>
                    </div>
                    <div class="info-row">
                        <label>Room Number:</label>
                        <span>Room 12</span>
                    </div>
                    <div class="info-row">
                        <label>Proctor Name:</label>
                        <span>Abebe Kebede</span>
                    </div>
                </div>

                <!-- Cafe Status Card -->
                <div class="card">
                    <h3>Cafeteria Services</h3>
                    <div class="info-row">
                        <label>Cafe User Status:</label>
                        <span class="badge-cafe">Cafe User (Cafe)</span>
                    </div>
                    <div class="info-row">
                        <label>Dining Hall:</label>
                        <span>Student Dining Hall 2</span>
                    </div>
                    <div class="info-row">
                        <label>Card ID:</label>
                        <span>JU-CAF-8821</span>
                    </div>
                    <div class="info-row">
                        <label>Status:</label>
                        <span style="color: #16a34a;">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
