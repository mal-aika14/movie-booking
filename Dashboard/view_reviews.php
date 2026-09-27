<?php
session_start();



include_once "../connect.php";
include_once "side.php";


// ===== Stats =====
$reviewCountQuery = "SELECT * FROM reviews";
$reviewCountResult = mysqli_query($conn, $reviewCountQuery);
$totalReviews = mysqli_num_rows($reviewCountResult);

$avgRatingQuery = "SELECT AVG(rating) AS overall_avg FROM reviews";
$avgRatingResult = mysqli_query($conn, $avgRatingQuery);
$avgRatingRow = mysqli_fetch_assoc($avgRatingResult);
$overallAvg = $avgRatingRow["overall_avg"] ? round($avgRatingRow["overall_avg"], 1) : 0;

$reviewedMoviesQuery = "SELECT COUNT(DISTINCT movie_id) AS total FROM reviews";
$reviewedMoviesResult = mysqli_query($conn, $reviewedMoviesQuery);
$reviewedMoviesRow = mysqli_fetch_assoc($reviewedMoviesResult);
$totalReviewedMovies = $reviewedMoviesRow["total"];

// Search box se aayi hui value uthana (movie title ya reviewer name se search)
$search = "";
if(isset($_GET["search"])){
    $search = $_GET["search"];
}

if($search != ""){
    $reviewQuery = "SELECT reviews.*, movies.title AS movie_title, movies.poster_url, users.name AS reviewer_name
                     FROM reviews
                     JOIN movies ON reviews.movie_id = movies.id
                     JOIN users ON reviews.user_id = users.id
                     WHERE movies.title LIKE '%$search%' OR users.name LIKE '%$search%'
                     ORDER BY reviews.created_at DESC";
}else{
    $reviewQuery = "SELECT reviews.*, movies.title AS movie_title, movies.poster_url, users.name AS reviewer_name
                     FROM reviews
                     JOIN movies ON reviews.movie_id = movies.id
                     JOIN users ON reviews.user_id = users.id
                     ORDER BY reviews.created_at DESC";
}
$reviewResult = mysqli_query($conn, $reviewQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reviews - MovieBook Admin</title>
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

        /* ===== Review specific bits ===== */
        .reviewer-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .reviewer-avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #9333ea, #6b21a8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .review-comment-cell {
            max-width: 260px;
            font-size: 13px;
            color: #6b6b6b;
            white-space: normal;
            overflow-wrap: break-word;
        }

        .review-comment-cell.empty {
            color: #bbb;
            font-style: italic;
        }

        .review-date-cell {
            font-size: 12.5px;
            color: #999;
            white-space: nowrap;
        }

        </style>
</head>
<body class="dashboard-body">


    <!-- ===== Main content ===== -->
    <div class="admin-content">

        <div class="admin-topbar">
            <div class="page-heading">
                <h1>
                    <i class="fa-solid fa-star"></i>
                    <?php echo $search != "" ? 'Search Results for "'.htmlspecialchars($search).'"' : 'Reviews'; ?>
                </h1>
                <p class="page-subtitle">See what users are saying about movies on MovieBook</p>
            </div>
        </div>

        <!-- ===== Stats ===== -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon movies"><i class="fa-solid fa-comment-dots"></i></div>
                <div>
                    <p class="stat-label">Total Reviews</p>
                    <p class="stat-value"><?php echo $totalReviews; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon theatres"><i class="fa-solid fa-star"></i></div>
                <div>
                    <p class="stat-label">Overall Avg Rating</p>
                    <p class="stat-value"><?php echo $overallAvg; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon shows"><i class="fa-solid fa-film"></i></div>
                <div>
                    <p class="stat-label">Movies Reviewed</p>
                    <p class="stat-value"><?php echo $totalReviewedMovies; ?></p>
                </div>
            </div>
        </section>


        <!-- ===== All Reviews Table ===== -->
        <section class="manage-section">
            <div class="manage-header">
                <h2><i class="fa-solid fa-star"></i> All Reviews (<?php echo mysqli_num_rows($reviewResult); ?>)</h2>
            </div>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Movie</th>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($reviewResult) == 0){ ?>
                            <tr><td colspan="6" style="text-align:center; color:#999; padding:30px;">No review was found.</td></tr>
                        <?php } ?>
                        <?php foreach($reviewResult as $review){ ?>
                            <tr>
                                <td>
                                    <div class="reviewer-cell">
                                        <img src="../<?php echo $review["poster_url"]; ?>" alt="<?php echo htmlspecialchars($review["movie_title"]); ?>" style="width:36px;height:52px;object-fit:cover;border-radius:6px;">
                                        <span class="movie-title"><?php echo htmlspecialchars($review["movie_title"]); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="reviewer-cell">
                                        <div class="reviewer-avatar-sm"><?php echo strtoupper(substr($review["reviewer_name"], 0, 1)); ?></div>
                                        <span><?php echo htmlspecialchars($review["reviewer_name"]); ?></span>
                                    </div>
                                </td>
                                <td><span class="rating-value"><i class="fa-solid fa-star"></i> <?php echo $review["rating"]; ?>/5</span></td>
                                <td>
                                    <?php if($review["comment"] != ""){ ?>
                                        <span class="review-comment-cell"><?php echo htmlspecialchars($review["comment"]); ?></span>
                                    <?php }else{ ?>
                                        <span class="review-comment-cell empty">No comment</span>
                                    <?php } ?>
                                </td>
                                <td><span class="review-date-cell"><?php echo date("d M Y", strtotime($review["created_at"])); ?></span></td>
                                <td class="actions">
                                    <a href="delete_review.php?id=<?php echo $review["id"]; ?>" class="btn-small delete" onclick="return confirm('Are you sure you want to delete this review?')"><i class="fa-solid fa-trash"></i> Delete</a>
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