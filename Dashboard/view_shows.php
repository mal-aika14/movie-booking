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

$showQuery = "SELECT shows.id, movies.title, theatres.name, shows.show_date, shows.show_time
              FROM shows
              JOIN movies ON shows.movie_id = movies.id
              JOIN theatres ON shows.theatre_id = theatres.id
              ";
$showResult = mysqli_query($conn, $showQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Shows - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* ===== Classy Heading ===== */
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
            content: "\f145";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            -webkit-text-fill-color: initial;
            color: #a855f7;
            background: rgba(168, 85, 247, 0.1);
            padding: 10px;
            border-radius: 12px;
            font-size: 1.3rem;
            animation: pulseIcon 2.5s ease-in-out infinite;
        }

        .page-heading .page-subtitle {
            margin: 6px 0 0 62px;
            color: #8a8a8a;
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

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* ===== Stats Cards Animation ===== */
        .stats-grid .stat-card {
            opacity: 0;
            transform: translateY(16px);
            animation: fadeSlideUp 0.5s ease forwards;
        }
        .stats-grid .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stats-grid .stat-card:nth-child(2) { animation-delay: 0.22s; }
        .stats-grid .stat-card:nth-child(3) { animation-delay: 0.34s; }

        .stat-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.08);
        }

        /* ===== Manage Section ===== */
        .manage-section {
            opacity: 0;
            animation: fadeSlideUp 0.6s ease forwards;
            animation-delay: 0.4s;
        }

        /* ===== Table Rows Animation ===== */
        .admin-table tbody tr {
            opacity: 0;
            animation: fadeIn 0.4s ease forwards;
            transition: background-color 0.2s ease;
        }
        .admin-table tbody tr:nth-child(1) { animation-delay: 0.45s; }
        .admin-table tbody tr:nth-child(2) { animation-delay: 0.50s; }
        .admin-table tbody tr:nth-child(3) { animation-delay: 0.55s; }
        .admin-table tbody tr:nth-child(4) { animation-delay: 0.60s; }
        .admin-table tbody tr:nth-child(5) { animation-delay: 0.65s; }
        .admin-table tbody tr:nth-child(n+6) { animation-delay: 0.70s; }

        .admin-table tbody tr:hover {
            background-color: rgba(168, 85, 247, 0.05);
        }

        /* ===== Buttons Animation ===== */
        .btn-add, .btn-small {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-add:hover, .btn-small.edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }
        .btn-small.delete:hover {
            transform: translateY(-2px) scale(1.03);
        }

        .genre-badge {
            transition: transform 0.2s ease;
        }
        .admin-table tbody tr:hover .genre-badge {
            transform: scale(1.03);
        }
    
        /* ===== CineAura Dark Purple Theme Override ===== */
        body.dashboard-body {
            background-color: #060010;
            color: #e8def8;
        }

        .admin-content {
            background-color: #060010;
        }

        .stat-card {
            background: #12081f !important;
            border: 1px solid #2a1a3d !important;
            box-shadow: 0 6px 18px rgba(0,0,0,0.5) !important;
        }

        .stat-label {
            color: #9584b0 !important;
        }

        .stat-value {
            color: #f3e8ff !important;
        }

        .stat-icon {
            background: rgba(168,85,247,0.15) !important;
            color: #c084fc !important;
        }

        .manage-section, .chart-card {
            background: #12081f !important;
            border: 1px solid #2a1a3d !important;
            box-shadow: 0 6px 18px rgba(0,0,0,0.5) !important;
        }

        .manage-header h2, .chart-card-header h2 {
            color: #f3e8ff !important;
        }

        .manage-header h2 i, .chart-card-header h2 i {
            color: #c084fc !important;
        }
        .manage-header{
            background: #1a0f2e !important;

        }

        .chart-card-header .dots {
            color: #9584b0 !important;
        }

        .admin-table {
            background: transparent !important;
            color: #d4c5e8 !important;
        }

        .admin-table thead {
            background: #1a0f2e !important;
        }

        .admin-table thead th {
            color: #9584b0 !important;
            border-bottom: 1px solid #2a1a3d !important;
        }

        .admin-table td {
            border-bottom: 1px solid #2a1a3d !important;
            color: #d4c5e8 !important;
        }

        .admin-table tbody tr:hover {
            background-color: rgba(168,85,247,0.08) !important;
        }

        .movie-title {
            color: #f3e8ff !important;
        }

        .admin-table tbody tr:hover .movie-title {
            color: #c084fc !important;
        }

        .genre-badge {
            background: rgba(168,85,247,0.15) !important;
            color: #c084fc !important;
            border: 1px solid rgba(168,85,247,0.3);
        }

        .rating-value {
            color: #ffd700 !important;
        }

        .btn-add {
            background: linear-gradient(135deg, #a855f7, #6b21a8) !important;
            color: #fff !important;
            border: none !important;
        }
        .btn-add:hover {
            background: linear-gradient(135deg, #9333ea, #581c87) !important;
        }

        .btn-small.edit {
            background: rgba(168,85,247,0.15) !important;
            color: #c084fc !important;
            border: 1px solid rgba(168,85,247,0.3) !important;
        }
        .btn-small.edit:hover {
            background: #a855f7 !important;
            color: #fff !important;
        }

        .btn-small.delete {
            background: rgba(239,68,68,0.12) !important;
            color: #f87171 !important;
            border: 1px solid rgba(239,68,68,0.25) !important;
        }
        .btn-small.delete:hover {
            background: #ef4444 !important;
            color: #fff !important;
        }

        .table-wrapper td, .table-wrapper th {
            color: inherit;
        }

    </style>
</head>
<body class="dashboard-body">


    <!-- ===== Main content ===== -->
    <div class="admin-content">

        <div class="admin-topbar">
            <div class="page-heading">
                <h1>Shows</h1>
                <p class="page-subtitle">Manage all shows listed on MovieBook</p>
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


        <!-- ===== All Shows Table ===== -->
        <section class="manage-section">
            <div class="manage-header">
                <h2><i class="fa-solid fa-ticket"></i> All Shows (<?php echo mysqli_num_rows($showResult); ?>)</h2>
                <a href="add_show.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add Show</a>
            </div>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Movie</th>
                            <th>Theatre</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($showResult) == 0){ ?>
                            <tr><td colspan="6" style="text-align:center; color:#999; padding:30px;">No show was found.</td></tr>
                        <?php } ?>
                        <?php foreach($showResult as $show){ ?>
                            <tr>
                                <td><?php echo $show["id"]; ?></td>
                                <td><span class="genre-badge"><?php echo $show["title"]; ?></span></td>
                                <td><?php echo $show["name"]; ?></td>
                                <td><i class="fa-regular fa-calendar" style="color:#999; margin-right:6px;"></i><?php echo $show["show_date"]; ?></td>
                                <td><i class="fa-regular fa-clock" style="color:#999; margin-right:6px;"></i><?php echo date("g:i A", strtotime($show["show_time"])); ?></td>
                                <td class="actions">
                                    <a href="edit_show.php?id=<?php echo $show["id"]; ?>" class="btn-small edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                    <a href="delete_show.php?id=<?php echo $show["id"]; ?>" class="btn-small delete" onclick="return confirm('Are you sure you want to delete this show?')"><i class="fa-solid fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>

</body>
</html>