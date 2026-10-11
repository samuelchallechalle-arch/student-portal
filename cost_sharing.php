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
    <title>Cost Sharing - JU Student Portal</title>
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

        .card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #f8fafc; color: #475569; text-align: left; padding: 12px; font-size: 13px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; color: #334155; font-size: 14px; }
        .total-row { font-weight: bold; background-color: #f1f5f9; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">JU Student Portal</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add_course.php">Course Registration</a></li>
            <li><a href="grade_report.php">Grade Report</a></li>
            <li><a href="cost_sharing.php" class="active">Cost Sharing</a></li>
            <li><a href="dorm_cafe.php">Dormitory & Cafe</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h2>Cost Sharing Statement</h2>
            <div>Welcome, <strong><?php echo htmlspecialchars($full_name); ?></strong> | <a href="logout.php" style="color:#ef4444; text-decoration:none;">Logout</a></div>
        </div>

        <div class="container">
            <div class="card">
                <h3>Accumulated Cost Sharing Expense Summary</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Academic Year</th>
                            <th>Tuition Fee (ETB)</th>
                            <th>Food Expense (ETB)</th>
                            <th>Lodging Expense (ETB)</th>
                            <th>Total Annual Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Year I (2024/2025)</td>
                            <td>9,500.00</td>
                            <td>12,000.00</td>
                            <td>3,500.00</td>
                            <td>25,000.00 ETB</td>
                        </tr>
                        <tr>
                            <td>Year II (2025/2026)</td>
                            <td>10,200.00</td>
                            <td>12,500.00</td>
                            <td>3,500.00</td>
                            <td>26,200.00 ETB</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="4" style="text-align: right;">Cumulative Total Cost:</td>
                            <td style="color: #2563eb;">51,200.00 ETB</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>