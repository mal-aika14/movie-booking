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

// Search box se aayi hui value uthana
$search = "";
if(isset($_GET["search"])){
    $search = $_GET["search"];
}

if($search != ""){
    $movieQuery = "SELECT * FROM movies WHERE title LIKE '%$search%' ORDER BY id DESC";
}else{
    $movieQuery = "SELECT * FROM movies";
}
$movieResult = mysqli_query($conn, $movieQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Movies - MovieBook Admin</title>
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

        .page-heading h1 i {
            -webkit-text-fill-color: initial;
            color: #a855f7;
            background: rgba(255, 77, 109, 0.1);
            padding: 10px;
            border-radius: 12px;
            font-size: 1.3rem;
            animation: pulseIcon 2.5s ease-in-out infinite;
        }

        .page-subtitle {
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

        /* ===== Stats Cards Animation ===== */
        .stats-grid .stat-card {
            opacity: 0;
            transform: translateY(16px);
            animation: fadeSlideUp 0.5s ease forwards;
        }
        .stats-grid .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stats-grid .stat-card:nth-child(2) { animation-delay: 0.22s; }
        .stats-grid .stat-card:nth-child(3) { animation-delay: 0.34s; }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

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
        }
        .admin-table tbody tr:nth-child(1) { animation-delay: 0.45s; }
        .admin-table tbody tr:nth-child(2) { animation-delay: 0.50s; }
        .admin-table tbody tr:nth-child(3) { animation-delay: 0.55s; }
        .admin-table tbody tr:nth-child(4) { animation-delay: 0.60s; }
        .admin-table tbody tr:nth-child(5) { animation-delay: 0.65s; }
        .admin-table tbody tr:nth-child(n+6) { animation-delay: 0.70s; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .admin-table tbody tr {
            transition: background-color 0.2s ease;
        }
        .admin-table tbody tr:hover {
            background-color: rgba(255, 77, 109, 0.05);
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

        .movie-title, .rating-value {
            transition: color 0.2s ease;
        }
        .admin-table tbody tr:hover .movie-title {
            color: #a855f7;
        }
    
        </style>
</head>
<body class="dashboard-body">


    <!-- ===== Main content ===== -->
    <div class="admin-content">

        <div class="admin-topbar">
            <div class="page-heading">
                <h1>
                    <i class="fa-solid fa-clapperboard"></i>
                    <?php echo $search != "" ? 'Search Results for "'.htmlspecialchars($search).'"' : 'Movies'; ?>
                </h1>
                <p class="page-subtitle">Manage all movies listed on MovieBook</p>
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


        <!-- ===== All Movies Table ===== -->
        <section class="manage-section">
            <div class="manage-header">
                <h2><i class="fa-solid fa-film"></i> All Movies (<?php echo mysqli_num_rows($movieResult); ?>)</h2>
                <a href="add_movie.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add Movie</a>
            </div>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Poster</th>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Genre</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($movieResult) == 0){ ?>
                            <tr><td colspan="5" style="text-align:center; color:#999; padding:30px;">No movie was found.
</td></tr>
                        <?php } ?>
                        <?php foreach($movieResult as $movie){ ?>
                            <tr>
                                <td><img src="../<?php echo $movie["poster_url"]; ?>" alt="<?php echo $movie["title"]; ?>"></td>
                                <td><?php echo $movie["id"]; ?></td>
                                <td><span class="movie-title"><?php echo $movie["title"]; ?></span></td>
                                <td><span class="genre-badge"><?php echo $movie["genre"]; ?></span></td>
                                <td class="actions">
                                    <a href="edit_movie.php?id=<?php echo $movie["id"]; ?>" class="btn-small edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                    <a href="delete_movie.php?id=<?php echo $movie["id"]; ?>" class="btn-small delete" onclick="return confirm('Are you sure you want to delete this movie?')"><i class="fa-solid fa-trash"></i> Delete</a>
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