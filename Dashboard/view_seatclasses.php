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

// ===== Seat classes table se rates nikal rahe hain =====
$seatClassQuery = "SELECT * FROM seat_classes ORDER BY id ASC";
$seatClassResult = mysqli_query($conn, $seatClassQuery);
$totalSeatClasses = mysqli_num_rows($seatClassResult);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Classes - MovieBook Admin</title>
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
            content: "\f6c0";
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
                <h1>Seat Classes</h1>
                <p class="page-subtitle">Manage seat pricing tiers for MovieBook</p>
            </div>
        </div>

        <!-- ===== Stats ===== -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon movies"><i class="fa-solid fa-film"></i></div>
                <div>
                    <p class="stat-label">Total Movies</p>
                    <p class="stat-value"><?php echo $totalMovies; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon theatres"><i class="fa-solid fa-building"></i></div>
                <div>
                    <p class="stat-label">Total Theatres</p>
                    <p class="stat-value"><?php echo $totalTheatres; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon shows"><i class="fa-solid fa-ticket"></i></div>
                <div>
                    <p class="stat-label">Total Shows</p>
                    <p class="stat-value"><?php echo $totalShows; ?></p>
                </div>
            </div>
        </section>

        <!-- ===== Seat Classes / Rates ===== -->
        <section class="manage-section">
            <div class="manage-header">
                <h2><i class="fa-solid fa-chair"></i> Seat Classes &amp; Rates (<?php echo $totalSeatClasses; ?>)</h2>
                <a href="add_seatclass.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add Class</a>
            </div>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Class</th>
                            <th>Adult Rate</th>
                            <th>Kid Rate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($totalSeatClasses == 0){ ?>
                            <tr>
                                <td colspan="5" style="text-align:center; color:#9584b0; padding:24px;">
                                    No seat classes added yet. Click "Add Class" to create one.
                                </td>
                            </tr>
                        <?php }else{ ?>
                            <?php foreach($seatClassResult as $seatclass){ ?>
                                <tr>
                                    <td><?php echo $seatclass["id"]; ?></td>
                                    <td><span class="class-badge <?php echo strtolower($seatclass["class_name"]); ?>"><i class="fa-solid fa-chair"></i> <?php echo htmlspecialchars($seatclass["class_name"]); ?></span></td>
                                    <td><span class="amount-value">Rs. <?php echo number_format($seatclass["adult_price"], 2); ?></span></td>
                                    <td><span class="amount-value">Rs. <?php echo number_format($seatclass["kid_price"], 2); ?></span></td>
                                    <td class="actions">
                                        <a href="edit_seatclass.php?id=<?php echo $seatclass["id"]; ?>" class="btn-small edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                        <a href="delete_seatclass.php?id=<?php echo $seatclass["id"]; ?>" class="btn-small delete" onclick="return confirm('Delete this seat class? This cannot be undone.');"><i class="fa-solid fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>

</body>
</html>