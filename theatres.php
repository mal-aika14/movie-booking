<?php
session_start();
include_once "include/header.php";

include_once "connect.php";


$theatresQuery = "SELECT theatres.*,
                          COUNT(DISTINCT shows.movie_id) AS movie_count,
                          COUNT(shows.id) AS show_count
                   FROM theatres
                   LEFT JOIN shows ON shows.theatre_id = theatres.id
                   GROUP BY theatres.id
                   ORDER BY theatres.name ASC";
$theatresResult = mysqli_query($conn, $theatresQuery);

$totalTheatresQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM theatres");
$totalTheatresRow = mysqli_fetch_assoc($totalTheatresQuery);
$totalTheatres = $totalTheatresRow["total"];

$totalShowsQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM shows");
$totalShowsRow = mysqli_fetch_assoc($totalShowsQuery);
$totalShows = $totalShowsRow["total"];

$cardImages = array(
    "https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?fm=jpg&q=70&w=900&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1595769816263-9b910be24d5f?fm=jpg&q=70&w=900&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1614014783958-369b9429da66?fm=jpg&q=70&w=900&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1536440136628-849c177e76a1?fm=jpg&q=70&w=900&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1514306191717-452ec28c7814?fm=jpg&q=70&w=900&auto=format&fit=crop"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theatres - MovieBook</title>
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

        a {
            text-decoration: none;
        }
        .btnn {
    display: inline-block;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s ease;
    border: none;
}
        .btn-outlinee {
    background-color: transparent;
    color: #d8b4fe;
    border: 1px solid #a855f7;
}

.btn-outlinee:hover {
    background-color: #a855f7;
    color: #fff;
}

        

        
       
        /* ===== Hero ===== */
        .theatres-hero {
            position: relative;
            height: 460px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }

        .theatres-hero .hero-bg {
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1717915604557-94283edbcc1b?fm=jpg&q=75&w=1800&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            transform: scale(1.08);
            animation: slowZoom 18s ease-in-out infinite alternate;
        }

        @keyframes slowZoom {
            from { transform: scale(1.08); }
            to { transform: scale(1); }
        }

        .theatres-hero .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.92), rgba(46,16,101,0.85));
        }

        .theatres-hero .floating-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(2px);
            opacity: 0.5;
            animation: floatOrb 6s ease-in-out infinite;
        }

        .orb-1 {
            width: 18px;
            height: 18px;
            background: #a855f7;
            top: 22%;
            left: 12%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 12px;
            height: 12px;
            background: #ffd700;
            top: 65%;
            left: 20%;
            animation-delay: 1.2s;
        }

        .orb-3 {
            width: 14px;
            height: 14px;
            background: #a855f7;
            top: 30%;
            right: 15%;
            animation-delay: 2.4s;
        }

        .orb-4 {
            width: 10px;
            height: 10px;
            background: #fff;
            top: 70%;
            right: 22%;
            animation-delay: 0.8s;
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

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-22px); }
        }

        .theatres-hero .hero-content {
            position: relative;
            z-index: 2;
            color: #fff;
            padding: 0 24px;
            animation: fadeInUp 0.8s ease backwards;
        }

        .theatres-hero .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(168,85,247,0.18);
            border: 1px solid rgba(168,85,247,0.5);
            color: #d8b4fe;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 7px 18px;
            border-radius: 30px;
            margin-bottom: 20px;
            animation: fadeInUp 0.7s ease 0.1s backwards;
        }

        .theatres-hero h1 {
            font-size: 42px;
            margin-bottom: 14px;
            animation: fadeInUp 0.7s ease 0.2s backwards;
        }

        .theatres-hero p {
            color: #cfc0e6;
            font-size: 16px;
            max-width: 560px;
            margin: 0 auto 30px;
            animation: fadeInUp 0.7s ease 0.3s backwards;
        }

        .theatres-hero .hero-actions {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.7s ease 0.4s backwards;
        }

        .theatres-hero .hero-stats {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 40px;
            justify-content: center;
            margin-top: 38px;
            animation: fadeInUp 0.7s ease 0.5s backwards;
        }

        .theatres-hero .hero-stats .stat {
            text-align: center;
        }

        .theatres-hero .hero-stats .stat-num {
            font-size: 24px;
            font-weight: 800;
            color: #fff;
        }

        .theatres-hero .hero-stats .stat-lbl {
            font-size: 12px;
            color: #cfc0e6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== Page container ===== */
        .theatres-section {
            max-width: 1300px;
            margin: 0 auto;
            padding: 60px 24px 70px;
        }

        .theatres-header {
            text-align: center;
            margin-bottom: 44px;
        }

        .theatres-header .eyebrow {
            font-size: 13px;
            font-weight: 700;
            color: #c084fc;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: block;
            margin-bottom: 10px;
        }

        .theatres-header h2 {
            font-size: 28px;
            color: #f3e8ff;
            font-weight: 800;
        }

        .theatres-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 28px;
        }

        .theatre-card {
            position: relative;
            background: #12081f;
            border: 1px solid #2a1a3d;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
            transition: transform 0.4s ease, box-shadow 0.4s ease, border-color 0.4s ease;
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
        }

        .theatre-card:nth-child(1) { animation-delay: 0.05s; }
        .theatre-card:nth-child(2) { animation-delay: 0.15s; }
        .theatre-card:nth-child(3) { animation-delay: 0.25s; }
        .theatre-card:nth-child(4) { animation-delay: 0.35s; }
        .theatre-card:nth-child(5) { animation-delay: 0.45s; }
        .theatre-card:nth-child(n+6) { animation-delay: 0.55s; }

        .theatre-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 24px 44px rgba(168,85,247,0.3);
            border-color: transparent;
        }

        .theatre-card .card-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .theatre-card .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }

        .theatre-card:hover .card-image img {
            transform: scale(1.1);
        }

        .theatre-card .card-image .image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(6,0,16,0.95) 0%, rgba(6,0,16,0.15) 60%);
        }

        .theatre-card .card-image .badge-pill {
            position: absolute;
            top: 14px;
            right: 14px;
            background: rgba(168,85,247,0.95);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .theatre-card .card-image .card-title-wrap {
            position: absolute;
            bottom: 14px;
            left: 18px;
            right: 18px;
        }

        .theatre-card .card-image .theatre-name {
            color: #fff;
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 4px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.4);
        }

        .theatre-card .card-image .theatre-location {
            color: #e8e0f2;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .theatre-card .card-body-lower {
            padding: 18px 20px 20px;
        }

        .theatre-card .amenities {
            display: flex;
            gap: 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .theatre-card .amenities span {
            font-size: 12px;
            color: #b8a8d4;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .theatre-card .amenities i {
            color: #c084fc;
            font-size: 12px;
        }

        /* ===== Mini embedded map ===== */
        .theatre-card .map-wrap {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 18px;
            border: 1px solid #2a1a3d;
        }

        .theatre-card .map-wrap iframe {
            width: 100%;
            height: 150px;
            border: none;
            display: block;
        }

        .theatre-card .map-wrap .map-open-link {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: #1a0f2e;
            color: #d4c5e8;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s ease;
        }

        .theatre-card .map-wrap .map-open-link:hover {
            background: #a855f7;
            color: #fff;
        }

        .theatre-card .stat-row {
            display: flex;
            gap: 12px;
            margin-bottom: 18px;
        }

        .theatre-card .stat-chip {
            flex: 1;
            background: #1a0f2e;
            border: 1px solid #2a1a3d;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
        }

        .theatre-card .stat-chip .num {
            font-size: 17px;
            font-weight: 800;
            color: #f3e8ff;
        }

        .theatre-card .stat-chip .lbl {
            font-size: 10px;
            color: #9584b0;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .theatre-card .card-footer a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 10px;
        }

        .no-theatres {
            color: #9584b0;
            font-size: 14px;
            padding: 40px 0;
            text-align: center;
            grid-column: 1 / -1;
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
           
            .theatres-hero {
                height: auto;
                padding: 50px 20px;
            }
            .theatres-hero h1 {
                font-size: 28px;
            }
            .theatres-hero .hero-stats {
                gap: 24px;
                flex-wrap: wrap;
            }
            .theatres-section {
                padding: 44px 20px 50px;
            }
        }
    </style>
</head>
<body>

   
    <!-- ===== Hero ===== -->
    <section class="theatres-hero">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <span class="floating-orb orb-1"></span>
        <span class="floating-orb orb-2"></span>
        <span class="floating-orb orb-3"></span>
        <span class="floating-orb orb-4"></span>

        <div class="hero-content">
            <span class="hero-eyebrow"><i class="fa-solid fa-clapperboard"></i> Our Cinemas</span>
            <h1>Find Your Perfect Screen</h1>
            <p>From blockbuster premieres to cozy weekend shows — explore every cinema in our network and pick the one closest to you.</p>
            <div class="hero-actions">
                <a href="movies.php" class="btn btn-filled"><i class="fa-solid fa-ticket"></i> Browse Movies</a>
                <a href="#theatreList" class="btnn btn-outlinee"><i class="fa-solid fa-arrow-down"></i> See All Theatres</a>
            </div>

            <div class="hero-stats">
                <div class="stat">
                    <div class="stat-num"><?php echo $totalTheatres; ?>+</div>
                    <div class="stat-lbl">Cinemas</div>
                </div>
                <div class="stat">
                    <div class="stat-num"><?php echo $totalShows; ?>+</div>
                    <div class="stat-lbl">Showtimes</div>
                </div>
                <div class="stat">
                    <div class="stat-num">4.8<i class="fa-solid fa-star" style="font-size:14px; color:#ffd700; margin-left:4px;"></i></div>
                    <div class="stat-lbl">Avg. Rating</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Theatres Grid ===== -->
    <section class="theatres-section" id="theatreList">
        <div class="theatres-header">
            <span class="eyebrow">Explore</span>
            <h2>All Our Partner Theatres</h2>
        </div>

        <?php if(!$theatresResult || mysqli_num_rows($theatresResult) == 0){ ?>
            <div class="theatres-grid">
                <p class="no-theatres">No theatres have been added yet.</p>
            </div>
        <?php }else{ ?>
            <div class="theatres-grid">
                <?php $i = 0; foreach($theatresResult as $theatre){ ?>
                    <?php
                    $img = $cardImages[$i % count($cardImages)]; $i++;
                    // Location text ko Google Maps embed / open-link ke liye URL-encode kar rahe hain
                    $mapQuery = urlencode($theatre["name"] . ", " . $theatre["location"]);
                    ?>
                    <div class="theatre-card">
                        <div class="card-image">
                            <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($theatre["name"]); ?>">
                            <div class="image-overlay"></div>
                            <span class="badge-pill"><i class="fa-solid fa-film"></i> <?php echo $theatre["movie_count"]; ?> Movies</span>
                            <div class="card-title-wrap">
                                <div class="theatre-name"><?php echo htmlspecialchars($theatre["name"]); ?></div>
                                <div class="theatre-location"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($theatre["location"]); ?></div>
                            </div>
                        </div>
                        <div class="card-body-lower">

                            <!-- ===== Mini Map (Google Maps embed, koi API key nahi chahiye) ===== -->
                            <div class="map-wrap">
                                <iframe
                                    src="https://www.google.com/maps?q=<?php echo $mapQuery; ?>&output=embed"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                                <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $mapQuery; ?>" target="_blank" class="map-open-link">
                                    <i class="fa-solid fa-up-right-from-square"></i> Open in Maps
                                </a>
                            </div>

                            <div class="amenities">
                                <span><i class="fa-solid fa-couch"></i> Recliner Seats</span>
                                <span><i class="fa-solid fa-volume-high"></i> Dolby Sound</span>
                                <span><i class="fa-solid fa-square-parking"></i> Parking</span>
                            </div>
                            <div class="stat-row">
                                <div class="stat-chip">
                                    <div class="num"><?php echo $theatre["movie_count"]; ?></div>
                                    <div class="lbl">Movies</div>
                                </div>
                                <div class="stat-chip">
                                    <div class="num"><?php echo $theatre["show_count"]; ?></div>
                                    <div class="lbl">Shows</div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="theatre_details.php?id=<?php echo $theatre["id"]; ?>" class="btn btn-filled"><i class="fa-solid fa-ticket"></i> View Showtimes</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </section>

   <?php
include_once "include/footer.php";
   ?>

</body>
</html>