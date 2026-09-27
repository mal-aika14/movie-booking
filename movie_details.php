<?php
session_start();

include_once "connect.php";

function getYoutubeEmbedUrl($url){
    if($url == ""){
        return "";
    }

    if(strpos($url, "embed/") !== false){
        return $url;
    }

    $videoId = "";

    if(strpos($url, "watch?v=") !== false){
        $parts = explode("watch?v=", $url);
        $videoId = $parts[1];

        if(strpos($videoId, "&") !== false){
            $videoParts = explode("&", $videoId);
            $videoId = $videoParts[0];
        }
    }
   

    if($videoId != ""){
        return "https://www.youtube.com/embed/" . $videoId;
    }

    return $url;
}

function renderStarsHtml($value){
    $html = "";
    for($i = 1; $i <= 5; $i++){
        if($value >= $i){
            $html .= '<i class="fa-solid fa-star"></i>';
        }elseif($value >= ($i - 0.5)){
            $html .= '<i class="fa-solid fa-star-half-stroke"></i>';
        }else{
            $html .= '<i class="fa-regular fa-star"></i>';
        }
    }
    return $html;
}

$movieId = 0;
if(isset($_GET["id"])){
    $movieId = intval($_GET["id"]);
}

$movieQuery = "SELECT * FROM movies WHERE id = $movieId";
$movieResult = mysqli_query($conn, $movieQuery);
$movie = mysqli_fetch_assoc($movieResult);

if(!$movie){
    header("Location: movies.php");
    exit;
}

$isComingSoon = false;
if($movie["release_year"] >= date("Y")){
    $isComingSoon = true;
}

// ===== Review submit handle karna (rating + comment) =====
$reviewMessage = "";

if(isset($_POST["submit_review"])){

    if(!isset($_SESSION["user_id"])){
        // Login kiye bagair review submit nahi ho sakta
        header("Location: login.php");
        exit;
    }

    if($isComingSoon){
        // Movie abhi release hi nahi hui, is liye review allow nahi karni
        $reviewMessage = "This movie hasn't released yet, so it can't be rated or reviewed.";
    }else{

    $userId = intval($_SESSION["user_id"]);
    $userRating = isset($_POST["user_rating"]) ? floatval($_POST["user_rating"]) : 0;
    $userRating = round($userRating, 1);
    $userComment = isset($_POST["user_comment"]) ? trim($_POST["user_comment"]) : "";
    $safeComment = mysqli_real_escape_string($conn, $userComment);

    if($userRating < 0.5 || $userRating > 5){
        $reviewMessage = "Please give a rating between 0.5 and 5.";
    }else{

        $formattedRating = number_format($userRating, 1);

        // Check karo ke user ne pehle is movie ko review kiya hua hai ya nahi
        $checkQuery = "SELECT id FROM reviews WHERE movie_id = $movieId AND user_id = $userId";
        $checkResult = mysqli_query($conn, $checkQuery);

        if(mysqli_num_rows($checkResult) > 0){
            // Pehle se review mojood hai, usay update kar do
            $existingReview = mysqli_fetch_assoc($checkResult);
            $reviewId = $existingReview["id"];

            $updateQuery = "UPDATE reviews SET rating = $formattedRating, comment = '$safeComment', created_at = NOW() WHERE id = $reviewId";
            mysqli_query($conn, $updateQuery);
        }else{
            // Naya review insert karo
            $insertQuery = "INSERT INTO reviews (movie_id, user_id, rating, comment) VALUES ($movieId, $userId, $formattedRating, '$safeComment')";
            mysqli_query($conn, $insertQuery);
        }

        // Movie ki avg_rating ko reviews ke average se update kar do
        $avgQuery = "SELECT AVG(rating) AS new_avg FROM reviews WHERE movie_id = $movieId";
        $avgResult = mysqli_query($conn, $avgQuery);
        $avgRow = mysqli_fetch_assoc($avgResult);
        $newAvg = round($avgRow["new_avg"], 1);

        mysqli_query($conn, "UPDATE movies SET avg_rating = $newAvg WHERE id = $movieId");

        header("Location: movie_details.php?id=$movieId#reviews");
        exit;
    }
    }
}

$showsQuery = "SELECT shows.id AS show_id, shows.show_date, shows.show_time,
                      theatres.id AS theatre_id, theatres.name AS theatre_name, theatres.location AS theatre_location
               FROM shows
               JOIN theatres ON shows.theatre_id = theatres.id
               WHERE shows.movie_id = $movieId
               ORDER BY theatres.name ASC, shows.show_date ASC, shows.show_time ASC";
$showsResult = mysqli_query($conn, $showsQuery);

$theatreGroups = array();

while($row = mysqli_fetch_assoc($showsResult)){
    $tid = $row["theatre_id"];

    if(!isset($theatreGroups[$tid])){
        $theatreGroups[$tid] = array(
            "name" => $row["theatre_name"],
            "location" => $row["theatre_location"],
            "shows" => array()
        );
    }

    $theatreGroups[$tid]["shows"][] = array(
        "show_id" => $row["show_id"],
        "date" => $row["show_date"],
        "time" => $row["show_time"]
    );
}

// ===== Reviews fetch karna (reviewer ka naam users table se join karke) =====
$reviewsQuery = "SELECT reviews.*, users.name AS reviewer_name
                  FROM reviews
                  JOIN users ON reviews.user_id = users.id
                  WHERE reviews.movie_id = $movieId
                  ORDER BY reviews.created_at DESC";
$reviewsResult = mysqli_query($conn, $reviewsQuery);

$allReviews = array();
$totalRating = 0;
$reviewCount = 0;

while($row = mysqli_fetch_assoc($reviewsResult)){
    $allReviews[] = $row;
    $totalRating += $row["rating"];
    $reviewCount++;
}

$averageRating = $reviewCount > 0 ? round($totalRating / $reviewCount, 1) : 0;

// ===== Agar logged-in user ne pehle is movie ko review kiya hua hai, to uska review pre-fill karna =====
$myExistingReview = null;

if(isset($_SESSION["user_id"])){
    $myUserId = intval($_SESSION["user_id"]);
    $myReviewQuery = "SELECT * FROM reviews WHERE movie_id = $movieId AND user_id = $myUserId";
    $myReviewResult = mysqli_query($conn, $myReviewQuery);

    if(mysqli_num_rows($myReviewResult) > 0){
        $myExistingReview = mysqli_fetch_assoc($myReviewResult);
    }
}

// Ab tak koi redirect nahi hua, to yahan se header (nav bar) safely include ho sakta hai
include_once "include/header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie["title"]); ?> - MovieBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* ===== Reset ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background-color: #060010;
            color: #e8def8;
            line-height: 1.5;
        }

        a {
            text-decoration: none;
        }

        /* ===== Buttons ===== */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
            border: none;
        }

        .btn-filled {
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
        }

        .btn-filled:hover {
            background: linear-gradient(135deg, #9333ea, #581c87);
        }

        .btn-outline {
            background-color: transparent;
            color: #d8b4fe;
            border: 1px solid #a855f7;
        }

        .btn-outline:hover {
            background-color: #a855f7;
            color: #fff;
        }



        /* ===== Movie Hero (ab movie ki apni poster background mein, overlay ke sath) ===== */
        .movie-hero {
            position: relative;
            padding: 50px 60px;
            overflow: hidden;
        }

        .movie-hero .hero-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            filter: blur(2px);
            transform: scale(1.1);
        }

        .movie-hero .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.92), rgba(46,16,101,0.85));
        }

        .movie-hero .back-link {
            position: relative;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #b8a8d4;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 26px;
            transition: color 0.2s ease;
        }

        .movie-hero .back-link:hover {
            color: #c084fc;
        }

        .movie-hero-inner {
            position: relative;
            z-index: 2;
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }

        .movie-hero .poster-img {
            width: 260px;
            height: 380px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            flex-shrink: 0;
        }

        .movie-hero .movie-info {
            color: #fff;
            padding-top: 6px;
        }

        .movie-hero .badge-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .movie-hero .genre-tag {
            background: rgba(139,92,246,0.9);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 5px 14px;
            border-radius: 20px;
        }

        .movie-hero .status-tag {
            background: rgba(168,85,247,0.2);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 5px 14px;
            border-radius: 20px;
        }

        .movie-hero .rating {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #ffd700;
            font-size: 14px;
            font-weight: 700;
        }

        .movie-hero .rating .review-count {
            color: #b8a8d4;
            font-weight: 500;
            font-size: 12.5px;
        }

        .movie-hero h1 {
            font-size: 36px;
            margin-bottom: 14px;
        }

        .movie-hero .meta-line {
            color: #b8a8d4;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            gap: 22px;
            flex-wrap: wrap;
        }

        .movie-hero .meta-line span {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .movie-hero .description {
            color: #b8a8d4;
            font-size: 14px;
            max-width: 640px;
            margin-bottom: 24px;
        }

        .movie-hero .info-row {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
            margin-bottom: 24px;
        }

        .movie-hero .info-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .movie-hero .info-label {
            font-size: 11px;
            color: #b8a8d4;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 700;
        }

        .movie-hero .info-value {
            font-size: 14.5px;
            color: #fff;
            font-weight: 600;
        }

        .movie-hero .cast-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 24px;
        }

        .movie-hero .cast-label {
            color: #b8a8d4;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .movie-hero .cast-pill {
            background: rgba(168,85,247,0.15);
            border: 1px solid rgba(168,85,247,0.3);
            color: #fff;
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
        }

        .movie-hero .hero-actions {
            display: flex;
            gap: 14px;
        }

        /* ===== Trailer section ===== */
        .trailer-section {
            max-width: 1300px;
            margin: 0 auto;
            padding: 40px 60px;
        }

        .trailer-section .section-heading {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 20px;
        }

        .trailer-section .accent-bar {
            width: 5px;
            height: 22px;
            background: linear-gradient(180deg, #a855f7, #6b21a8);
            border-radius: 4px;
        }

        .trailer-section h2 {
            font-size: 22px;
            color: #f3e8ff;
            font-weight: 700;
        }

        .trailer-wrap {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            aspect-ratio: 16 / 9;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 16px 40px rgba(0,0,0,0.2);
        }

        .trailer-wrap iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        /* ===== Showtimes section ===== */
        .showtimes-section {
            max-width: 1300px;
            margin: 0 auto;
            padding: 10px 60px 60px;
        }

        .showtimes-section .section-heading {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 24px;
        }

        .showtimes-section .accent-bar {
            width: 5px;
            height: 22px;
            background: linear-gradient(180deg, #a855f7, #6b21a8);
            border-radius: 4px;
        }

        .showtimes-section h2 {
            font-size: 22px;
            color: #f3e8ff;
            font-weight: 700;
        }

        .theatre-card {
            background: #12081f;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.5);
            padding: 22px 26px;
            margin-bottom: 20px;
        }

        .theatre-card .theatre-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }

        .theatre-card .theatre-name {
            font-size: 17px;
            font-weight: 700;
            color: #f3e8ff;
        }

        .theatre-card .theatre-location {
            font-size: 13px;
            color: #9584b0;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
        }

        .theatre-card .theatre-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #9333ea, #6b21a8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .theatre-card .theatre-title-row {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .date-group {
            margin-bottom: 14px;
        }

        .date-group:last-child {
            margin-bottom: 0;
        }

        .date-group .date-label {
            font-size: 12px;
            font-weight: 700;
            color: #9584b0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .time-slots {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .time-slot {
            border: 1px solid #2a1a3d;
            background: #0d0518;
            color: #e8def8;
            font-size: 13px;
            font-weight: 600;
            padding: 9px 18px;
            border-radius: 8px;
            transition: 0.2s ease;
        }

        .time-slot:hover {
            background: #a855f7;
            border-color: #a855f7;
            color: #fff;
        }

        .no-shows {
            color: #9584b0;
            font-size: 14px;
            padding: 30px 0;
            text-align: center;
        }

        /* ===== Cast section (pill style, same as hero) ===== */
        .cast-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .cast-pill-tag {
            background: #12081f;
            border: 1px solid #2a1a3d;
            border-left: 5px solid #a855f7;
            color: #e8def8;
            font-size: 13.5px;
            font-weight: 600;
            padding: 9px 18px;
            border-radius: 20px;
        }

        /* ===== Reviews section ===== */
        .reviews-section {
            max-width: 1300px;
            margin: 0 auto;
            padding: 10px 60px 60px;
        }

        .reviews-section .section-heading {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 10px;
        }

        .reviews-section .accent-bar {
            width: 5px;
            height: 22px;
            background: linear-gradient(180deg, #a855f7, #6b21a8);
            border-radius: 4px;
        }

        .reviews-section h2 {
            font-size: 22px;
            color: #f3e8ff;
            font-weight: 700;
        }

        .reviews-summary {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
            color: #b8a8d4;
            font-size: 14px;
        }

        .reviews-summary .avg-number {
            font-size: 26px;
            font-weight: 800;
            color: #ffd700;
        }

        .reviews-summary .stars-static {
            color: #ffd700;
            font-size: 15px;
        }

        /* ---- Review form card ---- */
        .review-form-card {
            background: #12081f;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.5);
            padding: 26px 28px;
            margin-bottom: 28px;
        }

        .review-form-card h3 {
            font-size: 16px;
            color: #f3e8ff;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .rating-precise-input {
            margin-bottom: 6px;
        }

        .stars-display {
            position: relative;
            display: inline-block;
            font-size: 30px;
            line-height: 1;
            letter-spacing: 4px;
            margin-bottom: 14px;
        }

        .stars-display .stars-empty {
            color: #3a2a52;
        }

        .stars-display .stars-filled {
            position: absolute;
            top: 0;
            left: 0;
            width: 0%;
            overflow: hidden;
            white-space: nowrap;
            color: #ffd700;
        }

        .rating-controls {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .rating-controls input[type="range"] {
            flex: 1;
            min-width: 160px;
            max-width: 280px;
            accent-color: #a855f7;
        }

        .rating-controls input[type="number"] {
            width: 70px;
            padding: 8px 10px;
            border: 1px solid #2a1a3d;
            border-radius: 8px;
            background: #0d0518;
            color: #e8def8;
            font-size: 14px;
            text-align: center;
        }

        .rating-controls input[type="number"]:focus {
            outline: none;
            border-color: #a855f7;
        }

        .rating-controls .rating-out-of {
            color: #9584b0;
            font-size: 13px;
        }

        .clear-rating-btn {
            background: transparent;
            border: 1px solid #2a1a3d;
            color: #9584b0;
            font-size: 12.5px;
            padding: 7px 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .clear-rating-btn:hover {
            border-color: #a855f7;
            color: #c084fc;
        }

        .star-hint {
            font-size: 12px;
            color: #7a6a92;
            margin: 10px 0 16px;
        }

        .review-form-card textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #2a1a3d;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            background: #0d0518;
            color: #e8def8;
            resize: vertical;
            min-height: 90px;
            margin-bottom: 16px;
        }

        .review-form-card textarea:focus {
            outline: none;
            border-color: #a855f7;
        }

        .review-message {
            background-color: #3a1420;
            border: 1px solid #5a2030;
            color: #fca5a5;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .login-to-review {
            background: #12081f;
            border-radius: 14px;
            padding: 22px 26px;
            margin-bottom: 28px;
            color: #b8a8d4;
            font-size: 14px;
            text-align: center;
        }

        .login-to-review a {
            color: #c084fc;
            font-weight: 700;
        }

        /* ---- Review list ---- */
        .review-card {
            background: #12081f;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.5);
            padding: 20px 24px;
            margin-bottom: 16px;
        }

        .review-card .review-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .review-card .reviewer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .review-card .reviewer-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #9333ea, #6b21a8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .review-card .reviewer-name {
            font-size: 14.5px;
            font-weight: 700;
            color: #f3e8ff;
        }

        .review-card .review-date {
            font-size: 12px;
            color: #9584b0;
        }

        .review-card .review-stars {
            color: #ffd700;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .review-card .review-rating-number {
            color: #b8a8d4;
            font-size: 12px;
            font-weight: 600;
        }

        .review-card .review-comment {
            font-size: 13.5px;
            color: #cbb9e0;
            line-height: 1.6;
        }

        /* ===== Footer ===== */
        .footer {
            background-color: #030008;
            color: #b8a8d4;
            text-align: center;
            padding: 24px;
            font-size: 14px;
            margin-top: 20px;
        }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
           
            .movie-hero {
                padding: 30px 24px;
            }
            .movie-hero-inner {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .movie-hero .poster-img {
                width: 200px;
                height: 290px;
            }
            .movie-hero h1 {
                font-size: 26px;
            }
            .movie-hero .meta-line,
            .movie-hero .badge-row {
                justify-content: center;
            }
            .movie-hero .hero-actions {
                justify-content: center;
            }
            .trailer-section,
            .showtimes-section,
            .reviews-section {
                padding: 30px 20px;
            }
            .theatre-card .theatre-head {
                flex-direction: column;
                align-items: flex-start;
            }
            .review-card .review-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    
        .user-menu {
            position: relative;
        }

        .user-menu-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #1a0f2e;
            border: 1px solid #2a1a3d;
            padding: 8px 16px 8px 8px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #e8def8;
            transition: 0.2s ease;
        }

        .user-menu-toggle:hover {
            background: #241636;
        }

        .user-menu-toggle .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #9333ea, #6b21a8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-menu-toggle i.fa-chevron-down {
            font-size: 11px;
            color: #9584b0;
            transition: transform 0.2s ease;
        }

        .user-menu.open .user-menu-toggle i.fa-chevron-down {
            transform: rotate(180deg);
        }

        .user-menu-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: #12081f;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.65);
            min-width: 190px;
            padding: 8px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: 0.2s ease;
            z-index: 300;
        }

        .user-menu.open .user-menu-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-menu-dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13.5px;
            color: #d4c5e8;
            font-weight: 500;
            transition: 0.15s ease;
        }

        .user-menu-dropdown a:hover {
            background: #241636;
            color: #c084fc;
        }

        .user-menu-dropdown a i {
            width: 16px;
            color: #9584b0;
        }

        .user-menu-dropdown a:hover i {
            color: #a855f7;
        }

        .user-menu-dropdown .dropdown-divider {
            height: 1px;
            background: #2a1a3d;
            margin: 6px 4px;
        }

    </style>
</head>
<body>

    <!-- ===== Movie Hero ===== -->
    <section class="movie-hero">
        <div class="hero-bg" style="background-image: url('<?php echo $movie["poster_url"]; ?>');"></div>
        <div class="hero-overlay"></div>

        <a href="movies.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Movies</a>

        <div class="movie-hero-inner">
            <img class="poster-img" src="<?php echo $movie["poster_url"]; ?>" alt="<?php echo htmlspecialchars($movie["title"]); ?>">
            <div class="movie-info">
                <div class="badge-row">
                    <span class="genre-tag"><?php echo htmlspecialchars(ucfirst($movie["genre"])); ?></span>
                    <span class="status-tag"><?php echo $isComingSoon ? "Coming Soon" : "Now Showing"; ?></span>
                    <?php if(!$isComingSoon && $reviewCount > 0){ ?>
                        <span class="rating">
                            <i class="fa-solid fa-star"></i> <?php echo $averageRating; ?>
                            <span class="review-count">(<?php echo $reviewCount; ?> review<?php echo $reviewCount > 1 ? "s" : ""; ?>)</span>
                        </span>
                    <?php } ?>
                </div>
                <h1><?php echo htmlspecialchars($movie["title"]); ?></h1>
                <div class="meta-line">
                    <?php if(isset($movie["release_year"])){ ?>
                        <span><i class="fa-regular fa-calendar"></i> <?php echo htmlspecialchars($movie["release_year"]); ?></span>
                    <?php } ?>
                    <?php if(isset($movie["duration"])){ ?>
                        <span><i class="fa-regular fa-clock"></i> <?php echo htmlspecialchars($movie["duration"]); ?></span>
                    <?php } ?>
                </div>
                <?php if(isset($movie["description"]) && $movie["description"] != ""){ ?>
                    <p class="description"><?php echo htmlspecialchars($movie["description"]); ?></p>
                <?php } ?>
                <div class="info-row">
                    <?php if(isset($movie["release_year"])){ ?>
                        <div class="info-block">
                            <span class="info-label">Release Year</span>
                            <span class="info-value"><?php echo htmlspecialchars($movie["release_year"]); ?></span>
                        </div>
                    <?php } ?>
                    <?php if(isset($movie["director"]) && $movie["director"] != ""){ ?>
                        <div class="info-block">
                            <span class="info-label">Director</span>
                            <span class="info-value"><?php echo htmlspecialchars($movie["director"]); ?></span>
                        </div>
                    <?php } ?>
                </div>
                <?php if(isset($movie["cast"]) && $movie["cast"] != ""){ ?>
                    <div class="cast-row">
                        <span class="cast-label"><i class="fa-solid fa-users"></i> Cast:</span>
                        <?php
                        $castNames = array_map("trim", explode(",", $movie["cast"]));
                        foreach($castNames as $castName){
                            if($castName != ""){
                                echo '<span class="cast-pill">' . htmlspecialchars($castName) . '</span>';
                            }
                        }
                        ?>
                    </div>
                <?php } ?>
                <div class="hero-actions">
                    <?php if(!$isComingSoon){ ?>
                        <a href="book_ticket.php?movieid=<?php echo $movie["id"]; ?>" class="btn btn-filled"><i class="fa-solid fa-ticket"></i> Book Tickets</a>
                    <?php } ?>
                    <?php if(!empty($movie["trailer_url"])){ ?>
                        <a href="#trailer" class="btn btn-outline" style="color:#fff; border-color:#fff;"><i class="fa-solid fa-play"></i> Watch Trailer</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Trailer ===== -->
    <?php if(!empty($movie["trailer_url"])){ ?>
        <section class="trailer-section" id="trailer">
            <div class="section-heading">
                <div class="accent-bar"></div>
                <h2>Trailer</h2>
            </div>
            <div class="trailer-wrap">
                <iframe src="<?php echo getYoutubeEmbedUrl($movie["trailer_url"]); ?>" title="Trailer" allow="autoplay; encrypted-media; fullscreen" allowfullscreen></iframe>
            </div>
        </section>
    <?php } ?>

    <!-- ===== Showtimes ===== -->
    <section class="showtimes-section" id="cast">
        <div class="section-heading">
            <div class="accent-bar"></div>
            <h2>Cast</h2>
        </div>

        <?php if(isset($movie["cast_names"]) && $movie["cast_names"] != ""){ ?>
            <div class="cast-grid">
                <?php
                $castNames = array_map("trim", explode(",", $movie["cast_names"]));
                foreach($castNames as $castName){
                    if($castName != ""){
                        echo '<span class="cast-pill-tag">' . htmlspecialchars($castName) . '</span>';
                    }
                }
                ?>
            </div>
        <?php }else{ ?>
            <p class="no-shows">Cast details for this movie haven't been added yet.</p>
        <?php } ?>
    </section>

    <!-- ===== Reviews ===== -->
    <section class="reviews-section" id="reviews">
        <div class="section-heading">
            <div class="accent-bar"></div>
            <h2>Ratings &amp; Reviews</h2>
        </div>

        <?php if($isComingSoon){ ?>

            <div class="login-to-review">
                <i class="fa-regular fa-clock" style="margin-right:6px;"></i>
                This movie hasn't released yet. Ratings and reviews will open up once it starts showing in theatres.
            </div>

        <?php }else{ ?>

        <?php if($reviewCount > 0){ ?>
            <div class="reviews-summary">
                <span class="avg-number"><?php echo $averageRating; ?></span>
                <span class="stars-static">
                    <?php echo renderStarsHtml($averageRating); ?>
                </span>
                <span>based on <?php echo $reviewCount; ?> review<?php echo $reviewCount > 1 ? "s" : ""; ?></span>
            </div>
        <?php }else{ ?>
            <div class="reviews-summary">
                <span>No ratings yet. Be the first to review this movie!</span>
            </div>
        <?php } ?>

        <?php if($reviewMessage){ ?>
            <div class="review-message"><?php echo htmlspecialchars($reviewMessage); ?></div>
        <?php } ?>

        <?php if(isset($_SESSION["user_id"])){ ?>

            <div class="review-form-card">
                <h3><?php echo $myExistingReview ? "Update Your Review" : "Write a Review"; ?></h3>
                <form action="movie_details.php?id=<?php echo $movieId; ?>" method="post" id="reviewForm">

                    <div class="rating-precise-input" id="starRatingWidget">
                        <div class="stars-display">
                            <div class="stars-empty">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                            <div class="stars-filled" id="starsFilledOverlay">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                        </div>

                        <div class="rating-controls">
                            <input type="range" id="ratingSlider" min="0" max="5" step="0.1" value="0">
                            <input type="number" id="ratingNumberInput" min="0.5" max="5" step="0.1" placeholder="0.0">
                            <span class="rating-out-of">/ 5</span>
                            <button type="button" id="clearRatingBtn" class="clear-rating-btn"><i class="fa-solid fa-xmark"></i> Clear</button>
                        </div>

                        <input type="hidden" name="user_rating" id="userRatingValue" value="">
                    </div>
                    <p class="star-hint">Drag the slider or type an exact value (e.g. 4.7) between 0.5 and 5.</p>

                    <textarea name="user_comment" placeholder="Share your thoughts about this movie..."></textarea>

                    <button type="submit" name="submit_review" class="btn btn-filled">
                        <i class="fa-solid fa-paper-plane"></i> <?php echo $myExistingReview ? "Update Review" : "Submit Review"; ?>
                    </button>
                </form>
            </div>

        <?php }else{ ?>

            <div class="login-to-review">
                <a href="login.php">Login</a> to write a review for this movie.
            </div>

        <?php } ?>

        <?php if(count($allReviews) > 0){ ?>
            <?php foreach($allReviews as $review){ ?>
                <div class="review-card">
                    <div class="review-head">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar"><?php echo strtoupper(substr($review["reviewer_name"], 0, 1)); ?></div>
                            <div>
                                <div class="reviewer-name"><?php echo htmlspecialchars($review["reviewer_name"]); ?></div>
                                <div class="review-date"><?php echo date("d M Y", strtotime($review["created_at"])); ?></div>
                            </div>
                        </div>
                        <div class="review-stars">
                            <?php echo renderStarsHtml($review["rating"]); ?>
                            <span class="review-rating-number"><?php echo number_format($review["rating"], 1); ?></span>
                        </div>
                    </div>
                    <?php if($review["comment"] != ""){ ?>
                        <p class="review-comment"><?php echo nl2br(htmlspecialchars($review["comment"])); ?></p>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php }else{ ?>
            <p class="no-shows">No reviews yet.</p>
        <?php } ?>

        <?php } ?>

    </section>


    <script>
        function toggleUserMenu() {
            document.querySelector(".user-menu").classList.toggle("open");
        }
        document.addEventListener("click", function(e) {
            const menu = document.querySelector(".user-menu");
            if (menu && !menu.contains(e.target)) {
                menu.classList.remove("open");
            }
        });

        // ===== Precise rating widget (slider + number box, e.g. 4.7) =====
        (function(){
            const widget = document.getElementById("starRatingWidget");
            if(!widget){
                return;
            }

            const slider = document.getElementById("ratingSlider");
            const numberInput = document.getElementById("ratingNumberInput");
            const hiddenInput = document.getElementById("userRatingValue");
            const starsFilled = document.getElementById("starsFilledOverlay");
            const clearBtn = document.getElementById("clearRatingBtn");

            function updateDisplay(value){
                value = Math.max(0, Math.min(5, value));
                const percent = (value / 5) * 100;

                starsFilled.style.width = percent + "%";
                slider.value = value;
                numberInput.value = value > 0 ? value.toFixed(1) : "";
                hiddenInput.value = value > 0 ? value.toFixed(1) : "";
            }

            slider.addEventListener("input", function(){
                updateDisplay(parseFloat(slider.value));
            });

            numberInput.addEventListener("input", function(){
                const typedValue = parseFloat(numberInput.value);
                if(!isNaN(typedValue)){
                    updateDisplay(typedValue);
                }
            });

            clearBtn.addEventListener("click", function(){
                updateDisplay(0);
            });

            // Shuru mein khali/zero state
            updateDisplay(0);
        })();

        // Submit se pehle check kar lo ke koi rating select ki hui hai
        const reviewForm = document.getElementById("reviewForm");
        if(reviewForm){
            reviewForm.addEventListener("submit", function(e){
                const ratingValue = document.getElementById("userRatingValue").value;
                if(ratingValue === "" || ratingValue === "0"){
                    e.preventDefault();
                    alert("Please select a star rating before submitting.");
                }
            });
        }
    </script>
    <?php
    include_once "include/footer.php";
    
    ?>


</body>
</html>