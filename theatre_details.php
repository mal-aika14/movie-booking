<?php
session_start();

include_once "connect.php";
include_once "include/header.php";
// ===== Database connection =====


// ===== Step 1: URL se theatre ki ID nikalna =====
// Jaise link hota hai: theatre-details.php?id=3
// To hum us "3" ko yahan se pakarte hain

$theatreId = 0; // pehle default 0 rakh dete hain

if(isset($_GET["id"])){
    $theatreId = intval($_GET["id"]); // POST se id le kar number bana lete hain
}


// ===== Step 2: Us ID ki theatre database se nikalna =====

$theatreQuery = "SELECT * FROM theatres WHERE id = $theatreId";
$theatreResult = mysqli_query($conn, $theatreQuery);
$theatre = mysqli_fetch_assoc($theatreResult); // ek hi row milegi (ya null agar na mile)


// Agar theatre mili hi nahi (galat id thi) to wapas theatres list pe bhej dein
if(!$theatre){
    header("Location: theatres.php");
    exit;
}


// ===== Step 3: Is theatre ki saari movies aur unke shows nikalna =====
// shows table mein movie_id aur theatre_id dono hain, is liye JOIN kar rahe hain
// taake movie ka naam, poster waghera bhi sath mil jaye

$showsQuery = "SELECT shows.id AS show_id, shows.show_date, shows.show_time,
                      movies.id AS movie_id, movies.title, movies.genre, movies.poster_url, movies.avg_rating
               FROM shows
               JOIN movies ON shows.movie_id = movies.id
               WHERE shows.theatre_id = $theatreId
               ORDER BY movies.title ASC, shows.show_date ASC, shows.show_time ASC";

$showsResult = mysqli_query($conn, $showsQuery);


// ===== Step 4: Shows ko movie ke hisaab se group karna =====
// Matlab: agar ek movie ke 5 shows hain (alag date/time pe), to un sab ko
// ek hi movie ke andar "shows" array mein daal dena, taake page pe
// har movie ek dafa dikhe aur uske neeche uske saare showtimes ho

$movieGroups = array(); // yahan groups store honge

while($row = mysqli_fetch_assoc($showsResult)){

    $mid = $row["movie_id"]; // is show ki movie ki id

    // Agar is movie ka group abhi tak nahi bana, to naya bana do
    if(!isset($movieGroups[$mid])){
        $movieGroups[$mid] = array(
            "title" => $row["title"],
            "genre" => $row["genre"],
            "poster_url" => $row["poster_url"],
            "avg_rating" => $row["avg_rating"],
            "shows" => array() // ye khali array hai, shows yahan add honge
        );
    }

    // Ab is show ko us movie ke "shows" array mein daal do
    $movieGroups[$mid]["shows"][] = array(
        "show_id" => $row["show_id"],
        "date" => $row["show_date"],
        "time" => $row["show_time"]
    );
}


// ===== Step 5: Kitni movies aur kitne shows hain, unki ginti karna =====

$movieCount = count($movieGroups); // total kitni alag movies hain

// note: upar wale while loop ne pointer end tak move kar diya hoga,
// is liye num_rows lene ke liye hum query dobara chala lete hain (safe tareeqa)
$showsResult2 = mysqli_query($conn, $showsQuery);
$showCount = mysqli_num_rows($showsResult2);


// ===== Step 6: Har theatre ke liye alag background image choose karna =====
// Har theatre ko hamesha wahi wali image milegi (id ke hisaab se fix),
// taake refresh karne pe image change na ho, lekin alag theatre pe alag image dikhe

$heroImages = array(
    "https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?fm=jpg&q=75&w=1800&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1595769816263-9b910be24d5f?fm=jpg&q=75&w=1800&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1717915604557-94283edbcc1b?fm=jpg&q=75&w=1800&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1536440136628-849c177e76a1?fm=jpg&q=75&w=1800&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1514306191717-452ec28c7814?fm=jpg&q=75&w=1800&auto=format&fit=crop"
);

$imageIndex = $theatreId % count($heroImages);
// "%" ka matlab hai remainder, is se hamesha 0 se 4 ke beech ka number milta hai

$heroImage = $heroImages[$imageIndex];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($theatre["name"]); ?> - MovieBook</title>
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
            overflow-x: hidden;
        }

        a { text-decoration: none; }

        /* ===== Buttons ===== */
        .btn {
            display: inline-block;
            padding: 12px 26px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.25s ease;
            border: none;
        }

        .btn-filled {
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
        }

        .btn-filled:hover {
            background: linear-gradient(135deg, #9333ea, #581c87);
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(168,85,247,0.45);
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

       

        /* ===== Theatre Hero ===== */
        .theatre-hero {
            position: relative;
            height: 380px;
            display: flex;
            align-items: flex-end;
            overflow: hidden;
        }

        .theatre-hero .hero-bg {
            position: absolute;
            inset: 0;
            background-image: url('<?php echo $heroImage; ?>');
            background-size: cover;
            background-position: center;
            transform: scale(1.06);
            animation: slowZoom 16s ease-in-out infinite alternate;
        }

        @keyframes slowZoom {
            from { transform: scale(1.06); }
            to { transform: scale(1); }
        }

        .theatre-hero .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.95) 5%, rgba(20,8,35,0.55) 60%, rgba(46,16,101,0.35) 100%);
        }

        .theatre-hero .back-link {
            position: absolute;
            top: 24px;
            left: 60px;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #b8a8d4;
            font-size: 14px;
            transition: color 0.2s ease;
            animation: fadeInUp 0.6s ease backwards;
        }

        .theatre-hero .back-link:hover { color: #fff; }

        .theatre-hero .hero-content {
            position: relative;
            z-index: 2;
            width: 100%;
            padding: 0 60px 34px;
            color: #fff;
        }

        .theatre-hero .hero-inner {
            max-width: 1300px;
            margin: 0 auto;
        }

        .theatre-hero h1 {
            font-size: 38px;
            margin-bottom: 10px;
            animation: fadeInUp 0.7s ease 0.1s backwards;
        }

        .theatre-hero .hero-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            color: #b8a8d4;
            font-size: 14px;
            margin-bottom: 20px;
            animation: fadeInUp 0.7s ease 0.2s backwards;
        }

        .theatre-hero .hero-meta span {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .theatre-hero .amenity-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            animation: fadeInUp 0.7s ease 0.3s backwards;
        }

        .theatre-hero .amenity-pill {
            background: rgba(168,85,247,0.15);
            border: 1px solid rgba(168,85,247,0.3);
            backdrop-filter: blur(6px);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 7px 16px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .theatre-hero .amenity-pill i { color: #c084fc; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== Stat strip ===== */
        .stat-strip {
            background: #12081f;
            border-bottom: 1px solid #2a1a3d;
        }

        .stat-strip-inner {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            padding: 20px 60px;
            gap: 40px;
        }

        .stat-strip .stat-block {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-strip .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #9333ea, #6b21a8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .stat-strip .stat-num {
            font-size: 19px;
            font-weight: 800;
            color: #f3e8ff;
        }

        .stat-strip .stat-lbl {
            font-size: 12px;
            color: #9584b0;
        }

        /* ===== Page container ===== */
        .movies-section {
            max-width: 1300px;
            margin: 0 auto;
            padding: 44px 60px 60px;
        }

        .section-heading {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 28px;
        }

        .accent-bar {
            width: 5px;
            height: 22px;
            background: linear-gradient(180deg, #a855f7, #6b21a8);
            border-radius: 4px;
        }

        .section-heading h2 {
            font-size: 22px;
            color: #f3e8ff;
            font-weight: 700;
        }

        /* ===== Movie showtime cards ===== */
        .movie-show-card {
            display: flex;
            gap: 24px;
            background: #12081f;
            border-radius: 16px;
            border: 1px solid #2a1a3d;
            box-shadow: 0 6px 18px rgba(0,0,0,0.5);
            padding: 22px;
            margin-bottom: 22px;
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }

        .movie-show-card:hover {
            box-shadow: 0 18px 36px rgba(168,85,247,0.3);
            transform: translateY(-4px);
        }

        .movie-show-card:nth-child(1) { animation-delay: 0.05s; }
        .movie-show-card:nth-child(2) { animation-delay: 0.15s; }
        .movie-show-card:nth-child(3) { animation-delay: 0.25s; }
        .movie-show-card:nth-child(4) { animation-delay: 0.35s; }
        .movie-show-card:nth-child(n+5) { animation-delay: 0.45s; }

        .movie-show-card .poster-thumb {
            width: 130px;
            aspect-ratio: 2 / 3;
            border-radius: 12px;
            object-fit: cover;
            flex-shrink: 0;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .movie-show-card .movie-info-block {
            flex: 1;
            min-width: 0;
        }

        .movie-show-card .movie-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }

        .movie-show-card .movie-title {
            font-size: 19px;
            font-weight: 700;
            color: #f3e8ff;
        }

        .movie-show-card .genre-tag {
            background: rgba(139,92,246,0.15);
            color: #c084fc;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .movie-show-card .rating-inline {
            color: #ffd700;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 14px;
        }

        .date-group {
            margin-bottom: 12px;
        }

        .date-group:last-child { margin-bottom: 0; }

        .date-group .date-label {
            font-size: 11px;
            font-weight: 700;
            color: #9584b0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .time-slots {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
        }

        .time-slot {
            border: 1px solid #2a1a3d;
            background: #0d0518;
            color: #e8def8;
            font-size: 13px;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 8px;
            transition: 0.2s ease;
        }

        .time-slot:hover {
            background: #a855f7;
            border-color: #a855f7;
            color: #fff;
            transform: translateY(-2px);
        }

        .no-shows {
            color: #9584b0;
            font-size: 14px;
            padding: 50px 0;
            text-align: center;
        }

        /* ===== Footer ===== */
        .footer {
            background-color: #030008;
            color: #b8a8d4;
            text-align: center;
            padding: 24px;
            font-size: 14px;
        }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
           
            .theatre-hero { height: auto; padding-top: 60px; }
            .theatre-hero .back-link { left: 20px; }
            .theatre-hero .hero-content { padding: 0 20px 28px; }
            .theatre-hero h1 { font-size: 26px; }
            .stat-strip-inner { flex-wrap: wrap; gap: 20px; padding: 18px 20px; }
            .movies-section { padding: 34px 20px 44px; }
            .movie-show-card { flex-direction: column; }
            .movie-show-card .poster-thumb { width: 100%; max-width: 220px; }
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

    <!-- ===== Theatre Hero ===== -->
    <section class="theatre-hero">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <a href="theatres.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Theatres</a>

        <div class="hero-content">
            <div class="hero-inner">
                <h1><?php echo htmlspecialchars($theatre["name"]); ?></h1>
                <div class="hero-meta">
                    <span><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($theatre["location"]); ?></span>
                    <span><i class="fa-solid fa-film"></i> <?php echo $movieCount; ?> Movies Showing</span>
                </div>
                <div class="amenity-row">
                    <span class="amenity-pill"><i class="fa-solid fa-couch"></i> Recliner Seats</span>
                    <span class="amenity-pill"><i class="fa-solid fa-volume-high"></i> Dolby Sound</span>
                    <span class="amenity-pill"><i class="fa-solid fa-square-parking"></i> Parking</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Stat strip ===== -->
    <section class="stat-strip">
        <div class="stat-strip-inner">
            <div class="stat-block">
                <div class="stat-icon"><i class="fa-solid fa-film"></i></div>
                <div>
                    <div class="stat-num"><?php echo $movieCount; ?></div>
                    <div class="stat-lbl">Movies Showing</div>
                </div>
            </div>
            <div class="stat-block">
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                <div>
                    <div class="stat-num"><?php echo $showCount; ?></div>
                    <div class="stat-lbl">Total Showtimes</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Movies & Showtimes ===== -->
    <section class="movies-section">
        <div class="section-heading">
            <div class="accent-bar"></div>
            <h2>Now Showing at <?php echo htmlspecialchars($theatre["name"]); ?></h2>
        </div>

        <?php if($movieCount == 0){ ?>
            <p class="no-shows">No movies are currently scheduled at this theatre.</p>
        <?php }else{ ?>
            <?php foreach($movieGroups as $movieId => $movie){ ?>
                <div class="movie-show-card">
                    <img class="poster-thumb" src="<?php echo $movie["poster_url"]; ?>" alt="<?php echo htmlspecialchars($movie["title"]); ?>">
                    <div class="movie-info-block">
                        <div class="movie-title-row">
                            <span class="movie-title"><?php echo htmlspecialchars($movie["title"]); ?></span>
                            <span class="genre-tag"><?php echo htmlspecialchars(ucfirst($movie["genre"])); ?></span>
                        </div>
                        <?php if(isset($movie["avg_rating"])){ ?>
                            <div class="rating-inline"><i class="fa-solid fa-star"></i> <?php echo $movie["avg_rating"]; ?></div>
                        <?php } ?>

                        <?php
                        $dateGroups = array();
                        foreach($movie["shows"] as $show){
                            $dateGroups[$show["date"]][] = $show;
                        }
                        ?>

                        <?php foreach($dateGroups as $date => $shows){ ?>
                            <div class="date-group">
                                <div class="date-label"><?php echo date("D, d M Y", strtotime($date)); ?></div>
                                <div class="time-slots">
                                    <?php foreach($shows as $show){ ?>
                                        <a href="book_ticket.php?movieid=<?php echo $movieId; ?>" class="time-slot">
                                            <?php echo date("h:i A", strtotime($show["time"])); ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
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
    </script>

<?php
include_once "include/footer.php";
?>
</body>
</html>