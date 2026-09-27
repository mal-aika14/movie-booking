<?php
include_once "include/header.php";

include_once "connect.php";

// ===== Search logic =====
$search = "";
if(isset($_POST["search"])){
    $search = $_POST["search"];
}

// Har genre dynamically nikal rahe hain (jitni bhi admin ne movies add ki hain)
// Coming soon movies (release_year >= current year) ko yahan shamil nahi kar rahe,
// taake sirf already-released movies ke genre tabs banein.
$genreListQuery = "SELECT DISTINCT genre FROM movies WHERE release_year < YEAR(CURDATE()) ORDER BY genre ASC";
$genreListResult = mysqli_query($conn, $genreListQuery);

$genres = array();
while($g = mysqli_fetch_assoc($genreListResult)){
    $genres[] = $g["genre"];
}

// ===== Posters nikal rahe hain (search banner ke peeche animated "poster wall"
// banane ke liye — apni hi movies ke posters use ho rahe hain) =====
$wallPostersQuery = "SELECT poster_url FROM movies WHERE poster_url != '' ORDER BY id DESC LIMIT 14";
$wallPostersResult = mysqli_query($conn, $wallPostersQuery);

$wallPosters = array();
while($p = mysqli_fetch_assoc($wallPostersResult)){
    $wallPosters[] = $p["poster_url"];
}

// Agar posters kam hon (14 se kam) to unhi ko dobara add kar ke wall bhari kar dete hain
$totalPosters = count($wallPosters);
if($totalPosters > 0){
    $i = 0;
    while(count($wallPosters) < 14){
        $wallPosters[] = $wallPosters[$i];
        $i++;

        // agar $i posters ki original count se bahar chala jaye to wapas 0 se shuru
        if($i >= $totalPosters){
            $i = 0;
        }
    }
}
?>
   
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie - CineAura</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

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


        /* ===== Search Banner (with animated poster wall) ===== */
        .search-banner {
            position: relative;
            background: #04000a;
            padding: 70px 24px 60px;
            text-align: center;
            overflow: hidden;
        }

        .poster-wall {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 14px;
            opacity: 2.5;
        }

        .poster-row {
            display: flex;
            gap: 14px;
            width: max-content;
            animation: scrollLeft 60s linear infinite;
        }

        .poster-row.row-reverse {
            animation-name: scrollRight;
        }

        .poster-row img {
            width: 130px;
            height: 190px;
            object-fit: cover;
            border-radius: 10px;
            flex-shrink: 0;
            filter: saturate(0.85);
        }

        @keyframes scrollLeft {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        @keyframes scrollRight {
            from { transform: translateX(-50%); }
            to { transform: translateX(0); }
        }

        .search-banner .banner-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.9), rgba(46,16,101,0.88));
        }

        .search-banner .banner-content {
            position: relative;
            z-index: 2;
        }

        .search-banner .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(168,85,247,0.15);
            border: 1px solid rgba(168,85,247,0.4);
            color: #d8b4fe;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 7px 18px;
            border-radius: 30px;
            margin-bottom: 18px;
        }

        .search-banner h1 {
            color: #fff;
            font-size: 34px;
            margin-bottom: 10px;
            font-weight: 800;
        }

        .search-banner h1 span {
            color: #c084fc;
        }

        .search-banner p {
            color: #b8a8d4;
            font-size: 15px;
            margin-bottom: 30px;
        }

        .search-form {
            display: flex;
            max-width: 550px;
            margin: 0 auto;
            background: #12081f;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 12px 34px rgba(0,0,0,0.6);
        }

        .search-form input {
            flex: 1;
            border: none;
            padding: 15px 20px;
            font-size: 14px;
            outline: none;
            background: #12081f;
            color: #e8def8;
        }

        .search-form button {
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            border: none;
            padding: 0 26px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-form button:hover {
            background: linear-gradient(135deg, #9333ea, #581c87);
        }

        .search-hint {
            margin-top: 16px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .search-hint span {
            color: #b8a8d4;
            font-size: 12px;
            padding-top: 6px;
        }

        .search-hint a {
            background: rgba(168,85,247,0.15);
            border: 1px solid rgba(168,85,247,0.3);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            transition: 0.2s ease;
        }

        .search-hint a:hover {
            background: #a855f7;
            border-color: #a855f7;
        }

        /* ===== Page container ===== */
        .genre-section {
            max-width: 1300px;
            margin: 0 auto;
            padding: 34px 24px 4px;
        }

        .genre-header {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 20px;
        }

        .genre-header h2 {
            font-size: 22px;
            color: #f3e8ff;
            text-transform: capitalize;
            font-weight: 700;
        }

        .genre-header .accent-bar {
            width: 5px;
            height: 22px;
            background: linear-gradient(180deg, #a855f7, #6b21a8);
            border-radius: 4px;
        }

        .genre-header .count {
            font-size: 13px;
            color: #9584b0;
        }

        .genre-scroll {
            display: flex;
            gap: 22px;
            overflow-x: auto;
            padding: 6px 6px 18px;
            scroll-behavior: smooth;
        }

        .genre-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .genre-scroll::-webkit-scrollbar-track {
            background: #12081f;
            border-radius: 10px;
        }

        .genre-scroll::-webkit-scrollbar-thumb {
            background: #a855f7;
            border-radius: 10px;
        }

        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 26px;
            padding: 6px 6px 18px;
        }

        /* ===== Movie card (premium style) ===== */
        .movie-card {
            flex: 0 0 210px;
            background: #12081f;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,0.5);
            border: 1px solid #2a1a3d;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            position: relative;
        }

        .movie-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 32px rgba(168,85,247,0.35);
            border-color: transparent;
        }

        .poster {
            position: relative;
            overflow: hidden;
        }

        .poster img {
            width: 100%;
            aspect-ratio: 2 / 3;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }

        .movie-card:hover .poster img {
            transform: scale(1.08);
        }

        .poster-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0) 55%);
        }

        .rating-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(4px);
            color: #ffd700;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .genre-pill {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(139,92,246,0.9);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .poster-title {
            position: absolute;
            bottom: 12px;
            left: 14px;
            right: 14px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            text-shadow: 0 2px 6px rgba(0,0,0,0.5);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-body {
            padding: 14px;
        }

        .card-body a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 10px;
            font-size: 13px;
            border-radius: 8px;
        }

        .no-movies {
            color: #9584b0;
            font-size: 14px;
            padding: 30px 0;
            text-align: center;
        }

        /* ===== Footer ===== */
        .footer {
            background-color: #030008;
            color: #b8a8d4;
            text-align: center;
            padding: 24px;
            font-size: 14px;
            margin-top: 50px;
        }

        /* ===== Animations ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .search-banner .eyebrow,
        .search-banner h1,
        .search-banner p,
        .search-form,
        .search-hint {
            animation: fadeInUp 0.6s ease backwards;
        }

        .search-banner .eyebrow { animation-delay: 0.05s; }
        .search-banner p { animation-delay: 0.15s; }
        .search-form { animation-delay: 0.25s; }
        .search-hint { animation-delay: 0.35s; }

        .genre-section {
            animation: fadeIn 0.6s ease backwards;
        }

        .movie-card {
            animation: fadeInUp 0.5s ease backwards;
        }

        /* Har card thora late aaye taake wave jaisa effect bane */
        .genre-scroll .movie-card:nth-child(1), .movie-grid .movie-card:nth-child(1) { animation-delay: 0.05s; }
        .genre-scroll .movie-card:nth-child(2), .movie-grid .movie-card:nth-child(2) { animation-delay: 0.1s; }
        .genre-scroll .movie-card:nth-child(3), .movie-grid .movie-card:nth-child(3) { animation-delay: 0.15s; }
        .genre-scroll .movie-card:nth-child(4), .movie-grid .movie-card:nth-child(4) { animation-delay: 0.2s; }
        .genre-scroll .movie-card:nth-child(5), .movie-grid .movie-card:nth-child(5) { animation-delay: 0.25s; }
        .genre-scroll .movie-card:nth-child(6), .movie-grid .movie-card:nth-child(6) { animation-delay: 0.3s; }
        .genre-scroll .movie-card:nth-child(n+7), .movie-grid .movie-card:nth-child(n+7) { animation-delay: 0.35s; }

        /* Button ka smooth pop hover */
        .card-body a {
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .card-body a:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(168,85,247,0.45);
        }

        /* Rating badge halka pulse jab card hover ho */
        .movie-card:hover .rating-badge {
            animation: pulse 0.6s ease;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }

        /* Genre pill thora slide-in hover pe */
        .genre-pill {
            transition: transform 0.2s ease;
        }

        .movie-card:hover .genre-pill {
            transform: translateX(3px);
        }

        /* Navbar links ka underline animation */
        /* .nav-links a {
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

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        } */

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            
            .search-banner {
                padding: 50px 16px 40px;
            }
            .search-banner h1 {
                font-size: 22px;
            }
            .poster-row img {
                width: 90px;
                height: 130px;
            }
            .search-hint {
                gap: 8px;
            }
            .movie-card {
                flex: 0 0 150px;
            }
            .movie-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 16px;
            }
        }
    </style>
    

</head>
<body>


    <!-- Search Banner  -->
    <section class="search-banner">
        <?php if(count($wallPosters) > 0){ ?>
            <div class="poster-wall">
                <div class="poster-row">
                    <?php foreach($wallPosters as $p){ ?><img src="<?php echo $p; ?>" alt=""><?php } ?>
                    <?php foreach($wallPosters as $p){ ?><img src="<?php echo $p; ?>" alt=""><?php } ?>
                </div>
                <div class="poster-row row-reverse">
                    <?php foreach(array_reverse($wallPosters) as $p){ ?><img src="<?php echo $p; ?>" alt=""><?php } ?>
                    <?php foreach(array_reverse($wallPosters) as $p){ ?><img src="<?php echo $p; ?>" alt=""><?php } ?>
                </div>
            </div>
        <?php } ?>
        <div class="banner-overlay"></div>

        <div class="banner-content">
            <span class="eyebrow"><i class="fa-solid fa-clapperboard"></i> Every Movie, One Place</span>
            <h1>What Are You <span>In The Mood</span> For?</h1>
            <p>Search by title, or just tell us the vibe — comedy, horror, action, we've got it all.</p>
            <form class="search-form" action="movies.php" method="POST">
                <input type="text" name="search" placeholder="Search for a movie..." value="">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            </form>
            <?php if(count($genres) > 0){ ?>
                <div class="search-hint">
                    <span>Popular:</span>
                    <?php foreach(array_slice($genres, 0, 6) as $g){ ?>
                        <a href="movies.php?search=<?php echo urlencode($g); ?>"><?php echo htmlspecialchars(ucfirst($g)); ?></a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </section>

    <?php if($search != ""){ ?>

        <!-- ===== Search Results (flat grid) ===== -->
        <section class="genre-section">
            <div class="genre-header">
                <div class="accent-bar"></div>
                <h2>Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>
            </div>

            <?php
            // Coming soon movies (release_year >= current year) ko search results mein bhi
            // exclude kar rahe hain. Search ab title AND genre dono se match karti hai,
            // taake "comedy" ya "horror" likhne pe us genre ki saari movies aa jayein.
            $safeSearch = mysqli_real_escape_string($conn, $search);
            $searchQuery = "SELECT * FROM movies WHERE (title LIKE '%$safeSearch%' OR genre LIKE '%$safeSearch%') AND release_year < YEAR(CURDATE()) ORDER BY id DESC";
            $searchResult = mysqli_query($conn, $searchQuery);
            ?>

            <?php if(mysqli_num_rows($searchResult) == 0){ ?>
                <p class="no-movies">No movies found. Try a different title or genre.</p>
            <?php }else{ ?>
                <div class="movie-grid">
                    <?php foreach($searchResult as $movie){ ?>
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
            <?php } ?>
        </section>

    <?php }else{ ?>

        <!-- ===== Genre-wise Rows ===== -->
        <?php if(count($genres) == 0){ ?>
            <section class="genre-section">
                <p class="no-movies">No movies have been added yet.</p>
            </section>
        <?php } ?>

        <?php foreach($genres as $genre){ ?>
            <?php
            // Yahan bhi coming soon movies exclude ki hain (release_year < current year)
            $movieQuery = "SELECT * FROM movies WHERE genre = '$genre' AND release_year < YEAR(CURDATE()) ORDER BY id DESC";
            $movieResult = mysqli_query($conn, $movieQuery);
            $movieCount = mysqli_num_rows($movieResult);
            ?>
            <section class="genre-section">
                <div class="genre-header">
                    <div class="accent-bar"></div>
                    <h2><?php echo ucfirst($genre); ?></h2>
                    <span class="count">(<?php echo $movieCount; ?>)</span>
                </div>
                <div class="genre-scroll">
                    <?php foreach($movieResult as $movie){ ?>
                        <div class="movie-card">
                            <div class="poster">
                                <img src="<?php echo $movie["poster_url"]; ?>" alt="<?php echo htmlspecialchars($movie["title"]); ?>">
                                <div class="poster-overlay"></div>
                                <span class="rating-badge"><i class="fa-solid fa-star"></i> <?php echo $movie["avg_rating"]; ?></span>
                                <span class="poster-title"><?php echo htmlspecialchars($movie["title"]); ?></span>
                            </div>
                            <div class="card-body">
                                <a href="movie_details.php?id=<?php echo $movie["id"]; ?>" class="btn btn-filled"><i class="fa-solid fa-circle-info"></i> View Detail</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </section>
        <?php } ?>

    <?php } ?>
<?php
    include_once "include/footer.php";
    
    ?>


</body>
</html>