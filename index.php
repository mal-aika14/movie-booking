<?php
session_start();

include_once "connect.php";




function getYoutubeEmbedUrl($url){
    if($url == "") {
        return "";
    }

    if(strpos($url, "embed/") !== false){
        return $url;
    }

    $videoId = "";

    if(strpos($url, "watch?v=") !== false){
        $parts = explode("watch?v=", $url);
        $videoId = $parts[1];

       
    }
   
    if($videoId != ""){
        return "https://www.youtube.com/embed/" . $videoId;
    }

    return $url;
}

$nowShowingQuery = "SELECT * FROM movies WHERE release_year < YEAR(CURDATE()) ORDER BY id DESC LIMIT 8";
$nowShowingResult = mysqli_query($conn, $nowShowingQuery);

$topRatedQuery = "SELECT * FROM movies WHERE release_year < YEAR(CURDATE()) ORDER BY avg_rating DESC LIMIT 8";
$topRatedResult = mysqli_query($conn, $topRatedQuery);

$comingSoonQuery = "SELECT * FROM movies WHERE release_year >= YEAR(CURDATE()) ORDER BY release_year ASC LIMIT 1";
$comingSoonResult = mysqli_query($conn, $comingSoonQuery);

$comingSoon = null;
if(mysqli_num_rows($comingSoonResult) > 0){
    $comingSoon = mysqli_fetch_assoc($comingSoonResult);
}

$comingSoonListQuery = "SELECT * FROM movies WHERE release_year >= YEAR(CURDATE())";
if($comingSoon){
    $comingSoonListQuery .= " AND id != " . $comingSoon["id"];
}
$comingSoonListQuery .= " ORDER BY release_year ASC LIMIT 8";
$comingSoonListResult = mysqli_query($conn, $comingSoonListQuery);

$totalMoviesQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM movies");
$totalMoviesRow = mysqli_fetch_assoc($totalMoviesQuery);
$totalMovies = $totalMoviesRow["total"];

$totalGenresQuery = mysqli_query($conn, "SELECT COUNT(DISTINCT genre) AS total FROM movies");
$totalGenresRow = mysqli_fetch_assoc($totalGenresQuery);
$totalGenres = $totalGenresRow["total"];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - CineAura</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
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

        /* ===== Navbar ===== */
        .navbar {
            background-color: #0d0518;
            padding: 16px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 14px rgba(0,0,0,0.6);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-family: 'Bebas Neue', 'Segoe UI', Arial, sans-serif;
            font-size: 30px;
            font-weight: 400;
            color: #f3e8ff;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 1.5px;
        }

        .logo i {
            color: #c084fc;
            font-size: 24px;
            animation: logoPulse 2.6s ease-in-out infinite;
        }

        .logo span {
            background: linear-gradient(90deg, #a855f7, #e9d5ff, #a855f7);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: #a855f7;
            animation: shimmer 3.5s linear infinite;
        }

        @keyframes shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        @keyframes logoPulse {
            0%, 100% { text-shadow: 0 0 0 rgba(168,85,247,0); transform: scale(1); }
            50% { text-shadow: 0 0 12px rgba(168,85,247,0.6); transform: scale(1.08); }
        }

        .nav-links {
            display: flex;
            gap: 28px;
        }

        .nav-links a {
            color: #c9b8dc;
            font-size: 15px;
            font-weight: 600;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -4px;
            width: 0;
            height: 2px;
            background-color: #a855f7;
            transition: width 0.25s ease;
        }

        .nav-links a.active,
        .nav-links a:hover {
            color: #c084fc;
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }

        .nav-actions {
            display: flex;
            gap: 12px;
        }

        .nav-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 22px;
            color: #e8def8;
            cursor: pointer;
            padding: 4px 6px;
            line-height: 1;
        }

        /* ===== Hero / Coming Soon Slider Banner ===== */
        .hero-banner {
            position: relative;
            background: linear-gradient(135deg, #000000, #2e1065);
            overflow: hidden;
            height: 640px;
        }

        .slides {
            position: absolute;
            inset: 0;
        }

        .slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.6s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slide.active {
            opacity: 1;
            visibility: visible;
        }

        /* --- Slide 1: Poster (full cover) --- */
        .slide-poster {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .slide-poster .poster-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center top;
        }

        .slide-poster .poster-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 10%, rgba(0,0,0,0.35) 55%, rgba(0,0,0,0.55) 100%);
        }

        .slide-poster .poster-content {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding: 40px 60px 50px;
            text-align: center;
        }

        .slide-poster .tag {
            display: inline-block;
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 9px 30px;
            border-radius: 40px;
            box-shadow: 0 8px 22px rgba(168,85,247,0.5);
        }

        .slide-poster .poster-info {
            color: #fff;
        }

        .slide-poster h1 {
            font-size: 34px;
            margin-bottom: 10px;
        }

        .slide-poster .tagline {
            color: #d4c5e8;
            font-size: 15px;
            max-width: 520px;
            margin: 0 auto 14px;
            line-height: 1.6;
        }

        .slide-poster p {
            color: #b8a8d4;
            font-size: 14px;
        }

        .slide-poster .release-date {
            font-size: 13px;
            color: #ffd700;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* --- Slide 2: Trailer --- */
        .slide-trailer {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 30px 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .trailer-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            filter: blur(5px) brightness(0.45) saturate(1.2);
            transform: scale(1.15);
            z-index: 0;
        }

        .trailer-bg-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.8), rgba(46,16,101,0.75));
            z-index: 1;
        }

        .trailer-wrap {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 780px;
            aspect-ratio: 16 / 9;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .trailer-wrap iframe,
        .trailer-wrap video {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        .no-comingsoon {
            color: #b8a8d4;
            text-align: center;
            font-size: 15px;
        }

        /* --- Slider controls --- */
        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(168,85,247,0.2);
            color: #fff;
            border: none;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s ease;
            z-index: 5;
        }

        .slider-arrow:hover {
            background: #8b5cf6;
        }

        .slider-arrow.prev { left: 20px; }
        .slider-arrow.next { right: 20px; }

        .slider-dots {
            position: absolute;
            bottom: 18px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 5;
        }

        .slider-dots span {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(168,85,247,0.4);
            cursor: pointer;
            transition: 0.2s ease;
        }

        .slider-dots span.active {
            background: #a855f7;
            width: 26px;
            border-radius: 6px;
        }

        /* ===== Now Showing section ===== */
        .nowshowing-section {
            max-width: 1300px;
            margin: 0 auto;
            padding: 40px 24px 4px;
        }

        .nowshowing-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }

        .nowshowing-header .title-group {
            display: flex;
            align-items: baseline;
            gap: 10px;
        }

        .nowshowing-header h2 {
            font-size: 22px;
            color: #f3e8ff;
            font-weight: 700;
        }

        .nowshowing-header .accent-bar {
            width: 5px;
            height: 22px;
            background: linear-gradient(180deg, #a855f7, #6b21a8);
            border-radius: 4px;
        }

        .nowshowing-header .view-all {
            font-size: 13px;
            font-weight: 600;
            color: #c084fc;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nowshowing-header .view-all:hover {
            color: #a855f7;
        }

        /* ===== Movie cards (compact size) ===== */
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 6px 6px 18px;
        }

        .movie-card {
            background: #12081f;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            border: 1px solid #2a1a3d;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            position: relative;
        }

        .movie-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 22px rgba(168,85,247,0.3);
            border-color: transparent;
        }

        .movie-card .poster {
            position: relative;
            overflow: hidden;
        }

        .movie-card .poster img {
            width: 100%;
            aspect-ratio: 3 / 4;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }

        .movie-card:hover .poster img {
            transform: scale(1.05);
        }

        .movie-card .poster-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0) 55%);
        }

        .movie-card .rating-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0,0,0,0.75);
            backdrop-filter: blur(4px);
            color: #ffd700;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .movie-card .genre-pill {
            position: absolute;
            top: 8px;
            left: 8px;
            max-width: 60%;
            background: rgba(139,92,246,0.9);
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 8px;
            border-radius: 20px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .movie-card .poster-title {
            position: absolute;
            bottom: 10px;
            left: 10px;
            right: 10px;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            text-shadow: 0 2px 6px rgba(0,0,0,0.5);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .movie-card .card-body {
            padding: 10px;
            border-top: 1px solid #2a1a3d;
        }

        .movie-card .card-body a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 8px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 6px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .movie-card .card-body a:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(168,85,247,0.45);
        }

        .no-movies {
            color: #9584b0;
            font-size: 14px;
            padding: 30px 0;
            text-align: center;
        }

        /* ===== Stats bar ===== */
        .stats-bar {
            position: relative;
            background-image: linear-gradient(135deg, rgba(0,0,0,0.9), rgba(46,16,101,0.85)), url('https://images.unsplash.com/photo-1717915604557-94283edbcc1b?fm=jpg&q=70&w=1600&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 60px 24px;
            margin-top: 10px;
        }

        .stats-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 24px;
            text-align: center;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .stat-item i {
            font-size: 24px;
            color: #c084fc;
            margin-bottom: 6px;
            background: rgba(168,85,247,0.15);
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-item .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
        }

        .stat-item .stat-label {
            font-size: 13px;
            color: #b8a8d4;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===== Welcome section (two-column) ===== */
        .home-section {
            max-width: 1300px;
            margin: 0 auto;
            padding: 60px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 60px;
        }

        .home-section .welcome-img {
            flex: 1 1 420px;
            max-width: 460px;
            border-radius: 18px;
            box-shadow: 0 20px 45px rgba(0,0,0,0.55);
        }

        .home-section .welcome-text {
            flex: 1 1 420px;
            max-width: 500px;
        }

        .home-section .welcome-text .accent-bar {
            width: 5px;
            height: 22px;
            background: linear-gradient(180deg, #a855f7, #6b21a8);
            border-radius: 4px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
        }

        .home-section .welcome-text .eyebrow {
            font-size: 13px;
            font-weight: 700;
            color: #c084fc;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .home-section h2 {
            font-size: 28px;
            color: #f3e8ff;
            margin: 10px 0 14px;
            font-weight: 700;
        }

        .home-section p {
            color: #b8a8d4;
            font-size: 14px;
            margin-bottom: 22px;
            line-height: 1.7;
        }

        .home-section .welcome-points {
            list-style: none;
            margin-bottom: 26px;
        }

        .home-section .welcome-points li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #d4c5e8;
            margin-bottom: 10px;
        }

        .home-section .welcome-points i {
            color: #c084fc;
            font-size: 14px;
        }

        /* ===== Reviews / Testimonials ===== */
        .reviews-section {
            max-width: 1300px;
            margin: 0 auto;
            padding: 20px 24px 60px;
            text-align: center;
        }

        .reviews-section .section-heading {
            margin-bottom: 34px;
        }

        .reviews-section .eyebrow {
            font-size: 13px;
            font-weight: 700;
            color: #c084fc;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .reviews-section h2 {
            font-size: 26px;
            color: #f3e8ff;
            margin-top: 8px;
            font-weight: 700;
        }

        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .review-card {
            position: relative;
            background: #12081f;
            border-radius: 16px;
            padding: 30px 26px 26px;
            text-align: left;
            box-shadow: 0 6px 18px rgba(0,0,0,0.5);
            border: 1px solid #2a1a3d;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }

        .review-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px rgba(168,85,247,0.3);
            border-color: transparent;
        }

        .review-card .quote-icon {
            position: absolute;
            top: 22px;
            right: 24px;
            font-size: 22px;
            color: #3d2260;
        }

        .review-card .review-stars {
            color: #ffd700;
            font-size: 13px;
            margin-bottom: 14px;
        }

        .review-card .review-text {
            color: #cbb8e0;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 22px;
        }

        .review-card .review-author {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid #2a1a3d;
        }

        .review-card .review-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #9333ea, #6b21a8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            flex-shrink: 0;
        }

        .review-card .review-name {
            font-size: 14px;
            font-weight: 700;
            color: #f3e8ff;
        }

        .review-card .review-role {
            font-size: 12px;
            color: #9584b0;
        }

        /* ===== Footer ===== */
        .footer {
            background-color: #030008;
            color: #b8a8d4;
            padding: 60px 24px 0;
            margin-top: 50px;
        }

        .footer-container {
            max-width: 1300px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 1.2fr;
            gap: 40px;
            padding-bottom: 40px;
            border-bottom: 1px solid rgba(168,85,247,0.15);
        }

        .footer-brand .logo {
            color: #fff;
            margin-bottom: 14px;
        }

        .footer-brand .logo span {
            color: #a855f7;
        }

        .footer-brand p {
            font-size: 13.5px;
            color: #9584b0;
            line-height: 1.8;
            max-width: 320px;
            margin-bottom: 18px;
        }

        .footer-social {
            display: flex;
            gap: 12px;
        }

        .footer-social a {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(168,85,247,0.1);
            color: #c9b8dc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: 0.2s ease;
        }

        .footer-social a:hover {
            background: #8b5cf6;
            color: #fff;
            transform: translateY(-3px);
        }

        .footer-col h3 {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 12px;
        }

        .footer-col ul li a {
            color: #9584b0;
            font-size: 13.5px;
            transition: 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .footer-col ul li a:hover {
            color: #c084fc;
            transform: translateX(3px);
        }

        .footer-col ul li a i {
            font-size: 11px;
            color: #a855f7;
        }

        .footer-contact li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13.5px;
            color: #9584b0;
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .footer-contact li i {
            color: #a855f7;
            font-size: 14px;
            margin-top: 3px;
            width: 16px;
            flex-shrink: 0;
        }

        .footer-bottom {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 22px 0;
            font-size: 13px;
            color: #7a6a94;
            flex-wrap: wrap;
        }

        .footer-bottom-links {
            display: flex;
            gap: 22px;
        }

        .footer-bottom-links a {
            color: #7a6a94;
            transition: 0.2s ease;
        }

        .footer-bottom-links a:hover {
            color: #c084fc;
        }

        /* ===== Animations ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide.active .poster-info,
        .slide.active .poster-img,
        .slide.active .trailer-wrap {
            animation: fadeInUp 0.6s ease;
        }

        /* ===== Responsive ===== */
        @media (max-width: 992px) {
            .footer-container {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* ===== Tablet ===== */
        @media (max-width: 992px) {
            .navbar {
                padding: 14px 24px;
            }
            .nav-links {
                gap: 18px;
            }
            .footer-container {
                grid-template-columns: 1fr 1fr;
            }
            .home-section {
                gap: 36px;
            }
            .movie-grid {
                gap: 16px;
            }
        }

        @media (max-width: 768px) {
            .stats-bar {
                background-attachment: scroll;
            }

            /* --- Header / hamburger menu --- */
            .navbar {
                flex-wrap: wrap;
                padding: 14px 20px;
                position: relative;
            }
            .nav-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .logo {
                order: 1;
            }
            .nav-actions {
                order: 2;
            }
            .nav-toggle {
                order: 3;
            }
            .nav-links {
                display: none;
                width: 100%;
                flex-direction: column;
                gap: 0;
                order: 4;
                background: #0d0518;
                margin-top: 14px;
                border-top: 1px solid #2a1a3d;
            }
            .nav-links.active {
                display: flex;
            }
            .nav-links a {
                width: 100%;
                padding: 14px 4px;
                border-bottom: 1px solid #1e1030;
            }
            .nav-links a::after {
                display: none;
            }

            .hero-banner {
                height: 520px;
            }
            .slide-poster .poster-content {
                padding: 24px 20px 32px;
                gap: 10px;
            }
            .slide-poster .tag {
                font-size: 16px;
                padding: 7px 18px;
            }
            .slide-poster h1 {
                font-size: 22px;
            }
            .slide-trailer {
                padding: 20px;
            }

            .nowshowing-section {
                padding: 32px 18px 4px;
            }
            .nowshowing-header {
                flex-wrap: wrap;
                gap: 8px;
            }
            .movie-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                padding: 4px 0 14px;
            }
            .movie-card .poster-title {
                font-size: 12px;
            }

            .stats-bar {
                padding: 44px 20px;
            }
            .stats-grid {
                gap: 28px 20px;
            }
            .stat-item .stat-number {
                font-size: 26px;
            }

            .home-section {
                flex-direction: column;
                gap: 30px;
                padding: 40px 20px;
                text-align: center;
            }
            .home-section .welcome-text {
                text-align: center;
            }
            .home-section .welcome-points li {
                justify-content: center;
            }

            .reviews-section {
                padding: 16px 20px 44px;
            }
            .reviews-grid {
                gap: 18px;
            }

            .footer {
                padding: 44px 20px 0;
            }
            .footer-container {
                grid-template-columns: 1fr;
                gap: 32px;
                text-align: center;
            }
            .footer-brand p {
                max-width: none;
                margin-left: auto;
                margin-right: auto;
            }
            .footer-brand .logo {
                justify-content: center;
            }
            .footer-social {
                justify-content: center;
            }
            .footer-contact li {
                justify-content: center;
                text-align: left;
            }
            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }

        /* ===== Small phones ===== */
        @media (max-width: 480px) {
            .navbar {
                padding: 12px 16px;
            }
            .logo {
                font-size: 20px;
            }
            .nav-actions {
                gap: 8px;
            }
            .nav-actions .btn {
                padding: 8px 14px;
                font-size: 13px;
            }

            .hero-banner {
                height: 440px;
            }
            .slide-poster h1 {
                font-size: 19px;
            }
            .slide-poster .tagline {
                font-size: 13px;
            }
            .slide-poster .tag {
                font-size: 14px;
                padding: 6px 14px;
            }

            .nowshowing-header h2 {
                font-size: 19px;
            }
            .movie-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .movie-card .genre-pill {
                font-size: 8px;
                padding: 3px 6px;
                top: 6px;
                left: 6px;
            }
            .movie-card .rating-badge {
                font-size: 10px;
                padding: 3px 6px;
                top: 6px;
                right: 6px;
            }
            .movie-card .card-body {
                padding: 8px;
            }
            .movie-card .card-body a {
                padding: 7px;
                font-size: 11px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .home-section h2 {
                font-size: 23px;
            }

            .reviews-section h2 {
                font-size: 22px;
            }

            .footer-bottom-links {
                flex-direction: column;
                gap: 8px;
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

    <!-- ===== Header / Navbar ===== -->
    <header class="navbar">
        <div class="logo"><i class="fa-solid fa-clapperboard"></i>Cine<span>Aura</span></div>

        <button class="nav-toggle" type="button" onclick="toggleNav()" aria-label="Toggle menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <nav class="nav-links" id="navLinks">
            <a href="index.php" class="active">Home</a>
            <a href="movies.php">Movies</a>
            <a href="theatres.php">Theatre</a>
            <a href="about.php">About</a>
            <a href="index.php#contact">Contact</a>
        </nav>
        <div class="nav-actions">
            <?php if(isset($_SESSION["user_id"])){ ?>
                <div class="user-menu">
                    <button class="user-menu-toggle" type="button" onclick="toggleUserMenu()">
                        <span class="user-avatar"><?php echo strtoupper(substr($_SESSION["user_name"], 0, 1)); ?></span>
                        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="user-menu-dropdown">
                        <a href="my_bookings.php"><i class="fa-solid fa-ticket"></i> My Bookings</a>
                        <a href="profile.php"><i class="fa-solid fa-user"></i> My Profile</a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
            <?php }else{ ?>
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-filled">Register</a>
            <?php } ?>
        </div>
    </header>

    <!-- ===== Hero Banner (Coming Soon Slider) ===== -->
    <section class="hero-banner">
        <?php if($comingSoon){ ?>
            <div class="slides">

                <!-- Slide 1: Poster -->
                <div class="slide active" data-slide="0">
                    <div class="slide-poster">
                        <img class="poster-img" src="<?php echo $comingSoon["poster_url"]; ?>" alt="<?php echo htmlspecialchars($comingSoon["title"]); ?>">
                        <div class="poster-overlay"></div>
                        <div class="poster-content">
                            <span class="tag">Coming Soon</span>
                            <div class="poster-info">
                                <h1><?php echo htmlspecialchars($comingSoon["title"]); ?></h1>
                                <p class="tagline">The wait is almost over — mark your calendar and get ready for the big screen experience.</p>
                                <?php if(isset($comingSoon["release_year"])){ ?>
                                    <p class="release-date"><i class="fa-regular fa-calendar"></i> Releasing in <?php echo htmlspecialchars($comingSoon["release_year"]); ?></p>
                                <?php } ?>
                                <?php if(isset($comingSoon["genre"])){ ?>
                                    <p><i class="fa-solid fa-film"></i> <?php echo htmlspecialchars(ucfirst($comingSoon["genre"])); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: Trailer -->
                <div class="slide" data-slide="1">
                    <div class="slide-trailer">
                        <div class="trailer-bg" style="background-image: url('<?php echo $comingSoon["poster_url"]; ?>');"></div>
                        <div class="trailer-bg-overlay"></div>
                        <div class="trailer-wrap">
                            <?php if(!empty($comingSoon["trailer_url"])){ ?>
                                <iframe src="<?php echo getYoutubeEmbedUrl($comingSoon["trailer_url"]); ?>" title="Trailer" allow="autoplay; encrypted-media; fullscreen" allowfullscreen></iframe>
                            <?php }else{ ?>
                                <p class="no-comingsoon">Trailer not available yet.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>

            <button class="slider-arrow prev" onclick="changeSlide(-1)"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="slider-arrow next" onclick="changeSlide(1)"><i class="fa-solid fa-chevron-right"></i></button>

            <div class="slider-dots">
                <span class="active" onclick="goToSlide(0)"></span>
                <span onclick="goToSlide(1)"></span>
            </div>

        <?php }else{ ?>
            <div style="height:100%; display:flex; align-items:center; justify-content:center;">
                <p class="no-comingsoon">No upcoming movies added yet.</p>
            </div>
        <?php } ?>
    </section>

    <!-- ===== Now Showing ===== -->
    <section class="nowshowing-section">
        <div class="nowshowing-header">
            <div class="title-group">
                <div class="accent-bar"></div>
                <h2>Now Showing</h2>
            </div>
            <a href="movies.php" class="view-all">View All <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <?php if($nowShowingResult && mysqli_num_rows($nowShowingResult) > 0){ ?>
            <div class="movie-grid">
                <?php foreach($nowShowingResult as $movie){ ?>
                    <div class="movie-card">
                        <div class="poster">
                            <img src="<?php echo $movie["poster_url"]; ?>" alt="<?php echo htmlspecialchars($movie["title"]); ?>">
                            <div class="poster-overlay"></div>
                            <span class="genre-pill"><?php echo htmlspecialchars($movie["genre"]); ?></span>
                            <span class="poster-title"><?php echo htmlspecialchars($movie["title"]); ?></span>
                        </div>
                        <div class="card-body">
                            <a href="movie_details.php?id=<?php echo $movie["id"]; ?>" class="btn btn-filled"><i class="fa-solid fa-circle-info"></i> View Detail</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php }else{ ?>
            <p class="no-movies">No movies showing right now.</p>
        <?php } ?>
    </section>

    <!-- ===== Top Rated / Trending ===== -->
    <section class="nowshowing-section">
        <div class="nowshowing-header">
            <div class="title-group">
                <div class="accent-bar"></div>
                <h2>Top Rated</h2>
            </div>
            <a href="movies.php" class="view-all">View All <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <?php if($topRatedResult && mysqli_num_rows($topRatedResult) > 0){ ?>
            <div class="movie-grid">
                <?php foreach($topRatedResult as $movie){ ?>
                    <div class="movie-card">
                        <div class="poster">
                            <img src="<?php echo $movie["poster_url"]; ?>" alt="<?php echo htmlspecialchars($movie["title"]); ?>">
                            <div class="poster-overlay"></div>
                            <span class="genre-pill"><?php echo htmlspecialchars($movie["genre"]); ?></span>
                            <span class="rating-badge"><i class="fa-solid fa-star"></i> <?php echo $movie["avg_rating"]; ?></span>
                            <span class="poster-title"><?php echo htmlspecialchars($movie["title"]); ?></span>
                        </div>
                        <div class="card-body">
                            <a href="movie_details.php?id=<?php echo $movie["id"]; ?>" class="btn btn-filled"><i class="fa-solid fa-circle-info"></i> View Detail</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php }else{ ?>
            <p class="no-movies">No ratings available yet.</p>
        <?php } ?>
    </section>

    <!-- ===== Coming Soon Lineup ===== -->
    <section class="nowshowing-section">
        <div class="nowshowing-header">
            <div class="title-group">
                <div class="accent-bar"></div>
                <h2>Coming Soon</h2>
            </div>
        </div>

        <?php if($comingSoonListResult && mysqli_num_rows($comingSoonListResult) > 0){ ?>
            <div class="movie-grid">
                <?php foreach($comingSoonListResult as $movie){ ?>
                    <div class="movie-card">
                        <div class="poster">
                            <img src="<?php echo $movie["poster_url"]; ?>" alt="<?php echo htmlspecialchars($movie["title"]); ?>">
                            <div class="poster-overlay"></div>
                            <span class="genre-pill"><?php echo htmlspecialchars($movie["genre"]); ?></span>
                            <span class="rating-badge"><i class="fa-regular fa-calendar"></i> <?php echo htmlspecialchars($movie["release_year"]); ?></span>
                            <span class="poster-title"><?php echo htmlspecialchars($movie["title"]); ?></span>
                        </div>
                        <div class="card-body">
                            <a href="movie_details.php?id=<?php echo $movie["id"]; ?>" class="btn btn-filled"><i class="fa-solid fa-circle-info"></i> View Detail</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php }else{ ?>
            <p class="no-movies">No other upcoming movies right now.</p>
        <?php } ?>
    </section>

    <!-- ===== Stats Bar ===== -->
    <!-- "Cinemas" aur "Happy Customers" abhi static numbers hain kyunke inke
         liye database mein koi table nahi hai. Jab aap ke paas ye data ho
         (cinemas table, ya users table ki row count), inhe bhi dynamic kar sakte hain. -->
    <section class="stats-bar">
        <div class="stats-grid">
            <div class="stat-item">
                <i class="fa-solid fa-film"></i>
                <span class="stat-number"><?php echo $totalMovies; ?>+</span>
                <span class="stat-label">Movies</span>
            </div>
            <div class="stat-item">
                <i class="fa-solid fa-tags"></i>
                <span class="stat-number"><?php echo $totalGenres; ?></span>
                <span class="stat-label">Genres</span>
            </div>
            <div class="stat-item">
                <i class="fa-solid fa-building"></i>
                <span class="stat-number">25+</span>
                <span class="stat-label">Cinemas</span>
            </div>
            <div class="stat-item">
                <i class="fa-solid fa-users"></i>
                <span class="stat-number">10K+</span>
                <span class="stat-label">Happy Customers</span>
            </div>
        </div>
    </section>

    

    <!-- ===== Customer Reviews ===== -->
    <!-- Filhal ye reviews static/sample hain kyunke database mein koi reviews
         table nahi hai. Agar aap ke paas ek "reviews" table ho (user_name,
         rating, comment jaisi columns ke sath), to main isay dynamic bana
         sakta hoon. -->
    <section class="reviews-section">
        <div class="section-heading">
            <span class="eyebrow">Testimonials</span>
            <h2>What Our Users Say</h2>
        </div>
        <div class="reviews-grid">
            <div class="review-card">
                <i class="fa-solid fa-quote-left quote-icon"></i>
                <div class="review-stars">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <p class="review-text">Booking tickets on this app is so easy. Seat selection and payment are smooth from start to finish, no more waiting in long lines.</p>
                <div class="review-author">
                    <div class="review-avatar">A</div>
                    <div>
                        <div class="review-name">Ayesha Khan</div>
                        <div class="review-role">Regular Customer</div>
                    </div>
                </div>
            </div>
            <div class="review-card">
                <i class="fa-solid fa-quote-left quote-icon"></i>
                <div class="review-stars">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                </div>
                <p class="review-text">The Coming Soon section always keeps me updated on when the next big release is dropping. I even watch the trailers right here.</p>
                <div class="review-author">
                    <div class="review-avatar">B</div>
                    <div>
                        <div class="review-name">Bilal Ahmed</div>
                        <div class="review-role">Movie Enthusiast</div>
                    </div>
                </div>
            </div>
            <div class="review-card">
                <i class="fa-solid fa-quote-left quote-icon"></i>
                <div class="review-stars">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                </div>
                <p class="review-text">Picking a movie is so much easier once you can see the ratings. The whole interface feels clean and fast too.</p>
                <div class="review-author">
                    <div class="review-avatar">S</div>
                    <div>
                        <div class="review-name">Sana Malik</div>
                        <div class="review-role">Verified User</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Footer ===== -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-brand">
                <div class="logo"><i class="fa-solid fa-clapperboard"></i>Cine<span>Aura</span></div>
                <p>Your one-stop platform for hassle-free movie ticket booking. Discover now showing films, top rated picks and upcoming releases, all in one place.</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php"><i class="fa-solid fa-angle-right"></i>Home</a></li>
                    <li><a href="movies.php"><i class="fa-solid fa-angle-right"></i>Movies</a></li>
                    <li><a href="theatres.php"><i class="fa-solid fa-angle-right"></i>Theatres</a></li>
                    <li><a href="about.php"><i class="fa-solid fa-angle-right"></i>About Us</a></li>
                    <li><a href="index.php#contact"><i class="fa-solid fa-angle-right"></i>Contact</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Support</h3>
                <ul>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i>FAQs</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i>Terms of Service</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i>Privacy Policy</a></li>
                    <li><a href="#"><i class="fa-solid fa-angle-right"></i>Refund Policy</a></li>
                    <li><a href="my_bookings.php"><i class="fa-solid fa-angle-right"></i>My Bookings</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Get In Touch</h3>
                <ul class="footer-contact">
                    <li><i class="fa-solid fa-location-dot"></i>123 Cinema Street, Karachi, Pakistan</li>
                    <li><i class="fa-solid fa-phone"></i>+92 300 1234567</li>
                    <li><i class="fa-solid fa-envelope"></i>support@moviebook.com</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> CineAura. All rights reserved.</p>
            <div class="footer-bottom-links">
                <a href="#">Terms</a>
                <a href="#">Privacy</a>
                <a href="#">Cookies</a>
            </div>
        </div>
    </footer>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll(".slide");
        const dots = document.querySelectorAll(".slider-dots span");
        let autoSlideTimer;

        function showSlide(index) {
            if (slides.length === 0) return;
            if (index >= slides.length) index = 0;
            if (index < 0) index = slides.length - 1;
            slides.forEach(s => s.classList.remove("active"));
            dots.forEach(d => d.classList.remove("active"));
            slides[index].classList.add("active");
            dots[index].classList.add("active");
            currentSlide = index;
        }

        function changeSlide(direction) {
            showSlide(currentSlide + direction);
            resetAutoSlide();
        }

        function goToSlide(index) {
            showSlide(index);
            resetAutoSlide();
        }

        function resetAutoSlide() {
            clearInterval(autoSlideTimer);
            autoSlideTimer = setInterval(() => showSlide(currentSlide + 1), 10000000000);
        }

        if (slides.length > 0) {
            resetAutoSlide();
        }
    </script>


    <script>
        function toggleNav() {
            document.getElementById("navLinks").classList.toggle("active");
        }

        document.querySelectorAll(".nav-links a").forEach(function(link){
            link.addEventListener("click", function(){
                document.getElementById("navLinks").classList.remove("active");
            });
        });

        document.addEventListener("click", function(e){
            const navLinks = document.getElementById("navLinks");
            const navToggle = document.querySelector(".nav-toggle");
            if (navLinks && navLinks.classList.contains("active") &&
                !navLinks.contains(e.target) && !navToggle.contains(e.target)) {
                navLinks.classList.remove("active");
            }
        });
    </script>

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
    </script>

</body>
</html>