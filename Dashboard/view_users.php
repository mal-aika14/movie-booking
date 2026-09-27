<?php
session_start();


include_once "../connect.php";
include_once "side.php";

$movieCountQuery = "SELECT * FROM movies";
$movieCountResult = mysqli_query($conn, $movieCountQuery);
$totalMovies = mysqli_num_rows($movieCountResult);

$theatreCountQuery = "SELECT * FROM theatres";
$theatreCountResult = mysqli_query($conn, $theatreCountQuery);
$totalTheatres = mysqli_num_rows($theatreCountResult);

$showCountQuery = "SELECT * FROM shows";
$showCountResult = mysqli_query($conn, $showCountQuery);
$totalShows = mysqli_num_rows($showCountResult);

$userQuery = "SELECT * FROM users";
$userResult = mysqli_query($conn, $userQuery);
$totalUsers = mysqli_num_rows($userResult);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* ===== Page heading (icon + gradient title + subtitle), same style as other admin pages ===== */
        .page-heading {
            opacity: 0;
            animation: fadeSlideDown 0.6s ease forwards;
        }

        .page-heading h1 {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 2.1rem;
            font-weight: 800;
            letter-spacing: 0.3px;
            margin: 0;
            background: linear-gradient(90deg, #a855f7, #6b21a8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-heading h1::before {
            content: "\f0c0";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            -webkit-text-fill-color: initial;
            color: #c084fc;
            background: rgba(168, 85, 247, 0.15);
            padding: 10px;
            border-radius: 12px;
            font-size: 1.3rem;
            animation: pulseIcon 2.5s ease-in-out infinite;
        }

        .page-heading .page-subtitle {
            margin: 6px 0 0 62px;
            color: #9584b0;
            font-size: 0.95rem;
            font-weight: 400;
            letter-spacing: 0.2px;
        }

        @keyframes fadeSlideDown {
            from { opacity: 0; transform: translateY(-14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulseIcon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }
    </style>
</head>
<body class="dashboard-body">

    <!-- ===== Main content ===== -->
    <div class="admin-content">

        <div class="admin-topbar">
            <div class="page-heading">
                <h1>Users</h1>
                <p class="page-subtitle">Manage all users registered on MovieBook</p>
            </div>
        </div>

        <!-- ===== Stats ===== -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div>
                    <p class="stat-label">Registered Users</p>
                    <p class="stat-value"><?php echo $totalUsers; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-film"></i></div>
                <div>
                    <p class="stat-label">Total Movies</p>
                    <p class="stat-value"><?php echo $totalMovies; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-building"></i></div>
                <div>
                    <p class="stat-label">Total Theatres</p>
                    <p class="stat-value"><?php echo $totalTheatres; ?></p>
                </div>
            </div>
        </section>

        <!-- ===== All Users Table ===== -->
        <section class="manage-section">
            <div class="manage-header">
                <h2><i class="fa-solid fa-users"></i> All Registered Users (<?php echo $totalUsers; ?>)</h2>
            </div>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($totalUsers == 0){ ?>
                            <tr><td colspan="3" style="text-align:center; color:#9584b0; padding:30px;">No users have registered yet.
</td></tr>
                        <?php } ?>
                        <?php foreach($userResult as $user){ ?>
                            <tr>
                                <td>#<?php echo $user["id"]; ?></td>
                                <td>
                                    <span class="user-avatar">
                                        <span class="avatar-circle"><?php echo strtoupper(substr($user["name"], 0, 1)); ?></span>
                                        <span class="user-name"><?php echo $user["name"]; ?></span>
                                    </span>
                                </td>
                                <td><?php echo $user["email"]; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>

</body>
</html>