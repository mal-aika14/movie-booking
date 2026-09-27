<?php

include_once "include/header.php";

include_once "connect.php";

$totalMoviesResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM movies");
$totalMovies = $totalMoviesResult ? mysqli_fetch_assoc($totalMoviesResult)["total"] : 0;

$totalGenresResult = mysqli_query($conn, "SELECT COUNT(DISTINCT genre) AS total FROM movies");
$totalGenres = $totalGenresResult ? mysqli_fetch_assoc($totalGenresResult)["total"] : 0;

$totalTheatresResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM theatres");
$totalTheatres = $totalTheatresResult ? mysqli_fetch_assoc($totalTheatresResult)["total"] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - MovieBook</title>
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

        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(28px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slowZoom {
            from { transform: scale(1.08); }
            to { transform: scale(1); }
        }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-22px); }
        }

        /* ===== Hero ===== */
        .about-hero {
            position: relative;
            height: 420px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }

        .about-hero .hero-bg {
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?fm=jpg&q=75&w=1800&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            transform: scale(1.08);
            animation: slowZoom 18s ease-in-out infinite alternate;
        }

        .about-hero .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.93), rgba(46,16,101,0.88));
        }

        .about-hero .floating-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(2px);
            opacity: 0.5;
            animation: floatOrb 6s ease-in-out infinite;
        }

        .orb-1 { width: 16px; height: 16px; background: #a855f7; top: 25%; left: 14%; animation-delay: 0s; }
        .orb-2 { width: 11px; height: 11px; background: #ffd700; top: 68%; left: 22%; animation-delay: 1.2s; }
        .orb-3 { width: 13px; height: 13px; background: #a855f7; top: 30%; right: 16%; animation-delay: 2.4s; }
        .orb-4 { width: 9px; height: 9px; background: #fff; top: 65%; right: 24%; animation-delay: 0.8s; }

        .about-hero .hero-content {
            position: relative;
            z-index: 2;
            color: #fff;
            padding: 0 24px;
        }

        .about-hero .hero-eyebrow {
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
            animation: fadeInUp 0.7s ease 0.05s backwards;
        }

        .about-hero h1 {
            font-size: 42px;
            margin-bottom: 14px;
            animation: fadeInUp 0.7s ease 0.15s backwards;
        }

        .about-hero p {
            color: #cfc0e6;
            font-size: 16px;
            max-width: 580px;
            margin: 0 auto;
            animation: fadeInUp 0.7s ease 0.25s backwards;
        }

        /* ===== Story section ===== */
        .story-section {
            max-width: 1300px;
            margin: 0 auto;
            padding: 70px 24px;
            display: flex;
            align-items: center;
            gap: 60px;
        }

        .story-section .story-img-wrap {
            position: relative;
            flex: 1 1 420px;
            max-width: 480px;
        }

        .story-section .story-img-wrap img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 24px 50px rgba(0,0,0,0.5);
            display: block;
        }

        .story-section .floating-badge {
            position: absolute;
            bottom: -22px;
            right: -22px;
            background: #12081f;
            border: 1px solid #2a1a3d;
            border-radius: 16px;
            padding: 18px 22px;
            box-shadow: 0 16px 34px rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .story-section .floating-badge .badge-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .story-section .floating-badge .badge-num {
            font-size: 18px;
            font-weight: 800;
            color: #f3e8ff;
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

        .story-section .floating-badge .badge-lbl {
            font-size: 11px;
            color: #9584b0;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .story-section .story-text {
            flex: 1 1 420px;
        }

        .story-section .eyebrow {
            font-size: 13px;
            font-weight: 700;
            color: #c084fc;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .story-section .eyebrow .accent-bar {
            width: 5px;
            height: 20px;
            background: linear-gradient(180deg, #a855f7, #6b21a8);
            border-radius: 4px;
        }

        .story-section h2 {
            font-size: 30px;
            color: #f3e8ff;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .story-section p {
            color: #b8a8d4;
            font-size: 14.5px;
            margin-bottom: 16px;
            line-height: 1.8;
        }

        /* ===== Values section ===== */
        .values-section {
            background: #060010;
            padding: 70px 24px;
        }

        .values-inner {
            max-width: 1300px;
            margin: 0 auto;
        }

        .section-heading-center {
            text-align: center;
            margin-bottom: 46px;
        }

        .section-heading-center .eyebrow {
            font-size: 13px;
            font-weight: 700;
            color: #c084fc;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: block;
            margin-bottom: 10px;
        }

        .section-heading-center h2 {
            font-size: 28px;
            color: #f3e8ff;
            font-weight: 800;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 26px;
        }

        .value-card {
            background: #12081f;
            border: 1px solid #2a1a3d;
            border-radius: 18px;
            padding: 32px 26px;
            text-align: center;
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
            transition: transform 0.35s ease, box-shadow 0.35s ease, background 0.35s ease, border-color 0.35s ease;
        }

        .value-card:nth-child(1) { animation-delay: 0.05s; }
        .value-card:nth-child(2) { animation-delay: 0.15s; }
        .value-card:nth-child(3) { animation-delay: 0.25s; }
        .value-card:nth-child(4) { animation-delay: 0.35s; }

        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(168,85,247,0.25);
            background: #1a0f2e;
            border-color: transparent;
        }

        .value-card .value-icon {
            width: 62px;
            height: 62px;
            border-radius: 16px;
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 20px;
            transition: transform 0.35s ease;
        }

        .value-card:hover .value-icon {
            transform: rotate(-8deg) scale(1.08);
        }

        .value-card h3 {
            font-size: 17px;
            color: #f3e8ff;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .value-card p {
            color: #b8a8d4;
            font-size: 13.5px;
            line-height: 1.6;
        }

        /* ===== Stats bar ===== */
        .stats-bar {
            position: relative;
            background-image: linear-gradient(135deg, rgba(0,0,0,0.92), rgba(46,16,101,0.92)), url('https://images.unsplash.com/photo-1717915604557-94283edbcc1b?fm=jpg&q=70&w=1600&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 60px 24px;
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
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
        }

        .stat-item:nth-child(1) { animation-delay: 0.05s; }
        .stat-item:nth-child(2) { animation-delay: 0.15s; }
        .stat-item:nth-child(3) { animation-delay: 0.25s; }
        .stat-item:nth-child(4) { animation-delay: 0.35s; }

        .stat-item i {
            font-size: 24px;
            color: #c084fc;
            margin-bottom: 6px;
            background: rgba(255,255,255,0.08);
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
            color: #cfc0e6;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===== Team section ===== */
        .team-section {
            max-width: 1300px;
            margin: 0 auto;
            padding: 70px 24px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 26px;
        }

        .team-card {
            background: #12081f;
            border: 1px solid #2a1a3d;
            border-radius: 18px;
            overflow: hidden;
            text-align: center;
            box-shadow: 0 6px 18px rgba(0,0,0,0.4);
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }

        .team-card:nth-child(1) { animation-delay: 0.05s; }
        .team-card:nth-child(2) { animation-delay: 0.15s; }
        .team-card:nth-child(3) { animation-delay: 0.25s; }
        .team-card:nth-child(4) { animation-delay: 0.35s; }

        .team-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 36px rgba(168,85,247,0.25);
        }

        .team-card .team-avatar {
            height: 220px;
            background: linear-gradient(135deg, #0d0518, #2e1065);
            overflow: hidden;
        }

        .team-card .team-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }

        .team-card:hover .team-avatar img {
            transform: scale(1.08);
        }

        .team-card .team-info {
            padding: 20px;
        }

        .team-card .team-name {
            font-size: 16px;
            font-weight: 700;
            color: #f3e8ff;
            margin-bottom: 4px;
        }

        .team-card .team-role {
            font-size: 12.5px;
            color: #c084fc;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .team-card .team-socials {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .team-card .team-socials a {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #1a0f2e;
            color: #9584b0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            transition: 0.2s ease;
        }

        .team-card .team-socials a:hover {
            background: #a855f7;
            color: #fff;
        }

        /* ===== CTA section ===== */
        .cta-section {
            position: relative;
            background: linear-gradient(135deg, #0d0518, #2e1065);
            padding: 70px 24px;
            text-align: center;
            overflow: hidden;
        }

        .cta-section .cta-orb {
            position: absolute;
            border-radius: 50%;
            background: rgba(168,85,247,0.18);
            filter: blur(30px);
        }

        .cta-orb-1 { width: 220px; height: 220px; top: -60px; left: -60px; }
        .cta-orb-2 { width: 260px; height: 260px; bottom: -80px; right: -60px; }

        .cta-section .cta-content {
            position: relative;
            z-index: 2;
        }

        .cta-section h2 {
            color: #fff;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 14px;
        }

        .cta-section p {
            color: #cfc0e6;
            font-size: 15px;
            max-width: 480px;
            margin: 0 auto 28px;
        }

        .cta-section .cta-actions {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
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
        @media (max-width: 900px) {
            .story-section {
                flex-direction: column;
                padding: 50px 24px;
            }
            .story-section .floating-badge {
                right: 12px;
            }
        }

        @media (max-width: 768px) {
            
            .about-hero { height: auto; padding: 50px 20px; }
            .about-hero h1 { font-size: 28px; }
            .story-section { padding: 40px 20px 70px; }
            .story-section h2 { font-size: 24px; }
            .values-section, .team-section, .cta-section { padding: 50px 20px; }
            .stats-bar { background-attachment: scroll; padding: 44px 20px; }
        }
    </style>
</head>
<body>

   

    <!-- ===== Hero ===== -->
    <section class="about-hero">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <span class="floating-orb orb-1"></span>
        <span class="floating-orb orb-2"></span>
        <span class="floating-orb orb-3"></span>
        <span class="floating-orb orb-4"></span>

        <div class="hero-content">
            <span class="hero-eyebrow"><i class="fa-solid fa-clapperboard"></i> About MovieBook</span>
            <h1>Bringing Every Screen Closer To You</h1>
            <p>We started with a simple idea: booking a movie ticket should be as enjoyable as watching the movie itself.</p>
        </div>
    </section>

    <!-- ===== Our Story ===== -->
    <section class="story-section">
        <div class="story-img-wrap">
            <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?fm=jpg&q=75&w=900&auto=format&fit=crop" alt="Cinema experience">
            <div class="floating-badge">
                <div class="badge-icon"><i class="fa-solid fa-heart"></i></div>
                <div>
                    <div class="badge-num">10K+</div>
                    <div class="badge-lbl">Happy Movie-goers</div>
                </div>
            </div>
        </div>
        <div class="story-text">
            <div class="eyebrow"><span class="accent-bar"></span>Our Story</div>
            <h2>Built By Movie Lovers, For Movie Lovers</h2>
            <p>MovieBook began with a group of friends who were tired of standing in long ticket lines just to find their favorite show was sold out. We thought — there had to be a better way.</p>
            <p>Today, we've grown into a platform that connects movie-goers with the latest releases, upcoming blockbusters, and every cinema in our network — all in one clean, simple experience.</p>
            <p>Whether you're planning a solo movie night or a big group outing, MovieBook makes sure the only thing you have to worry about is grabbing the popcorn.</p>
            <a href="movies.php" class="btn btn-filled"><i class="fa-solid fa-ticket"></i> Explore Movies</a>
        </div>
    </section>

    <!-- ===== Our Values ===== -->
    <section class="values-section">
        <div class="values-inner">
            <div class="section-heading-center">
                <span class="eyebrow">What We Stand For</span>
                <h2>The Values Behind MovieBook</h2>
            </div>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon"><i class="fa-solid fa-bolt"></i></div>
                    <h3>Fast & Simple</h3>
                    <p>From search to seat selection, booking a ticket takes less than a minute.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class="fa-solid fa-shield-heart"></i></div>
                    <h3>Trusted & Secure</h3>
                    <p>Your bookings and data are always handled with care and full transparency.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class="fa-solid fa-film"></i></div>
                    <h3>Every Movie, Every Genre</h3>
                    <p>From blockbuster action to indie dramas, we bring the full spectrum of cinema to you.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon"><i class="fa-solid fa-users"></i></div>
                    <h3>Community First</h3>
                    <p>Built around real feedback from real movie-goers, one release at a time.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Stats ===== -->
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
                <span class="stat-number"><?php echo $totalTheatres; ?>+</span>
                <span class="stat-label">Cinemas</span>
            </div>
            <div class="stat-item">
                <i class="fa-solid fa-users"></i>
                <span class="stat-number">10K+</span>
                <span class="stat-label">Happy Customers</span>
            </div>
        </div>
    </section>

    <!-- ===== Team ===== -->
    <!-- Team members abhi static/sample hain — apne asal team ke naam,
         roles aur photos se replace kar dein. -->
    <section class="team-section">
        <div class="section-heading-center">
            <span class="eyebrow">Meet The Team</span>
            <h2>The People Behind The Screen</h2>
        </div>
        <!-- ===== Team member images (HTML se lagayi hui) =====
             "images/team/..." wala path apni image files ke actual location
             se replace kar dein (jaise "images/ali.jpg", ya kisi bhi folder
             ka naam jahan aap ne photos rakhi hain). -->
        <div class="team-grid">
            <div class="team-card">
                <div class="team-avatar"><img src="images/CEEO.jpeg" alt="Ali Khan"></div>
                <div class="team-info">
                    <div class="team-name">Ali Khan</div>
                    <div class="team-role">Founder & CEO</div>
                    <div class="team-socials">
                        <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="team-card">
                <div class="team-avatar"><img src="images/product.jpeg" alt="Sara Malik"></div>
                <div class="team-info">
                    <div class="team-name">Sara Malik</div>
                    <div class="team-role">Product Manager</div>
                    <div class="team-socials">
                        <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="team-card">
                <div class="team-avatar"><img src="images/developer.jpeg" alt="Hamza Raza"></div>
                <div class="team-info">
                    <div class="team-name">Hamza Raza</div>
                    <div class="team-role">Lead Developer</div>
                    <div class="team-socials">
                        <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#"><i class="fa-brands fa-github"></i></a>
                    </div>
                </div>
            </div>
            <div class="team-card">
                <div class="team-avatar"><img src="images/sales.jpeg" alt="Zara Ahmed"></div>
                <div class="team-info">
                    <div class="team-name">Zara Ahmed</div>
                    <div class="team-role">Customer Experience</div>
                    <div class="team-socials">
                        <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="cta-section">
        <span class="cta-orb cta-orb-1"></span>
        <span class="cta-orb cta-orb-2"></span>
        <div class="cta-content">
            <h2>Ready For Your Next Movie Night?</h2>
            <p>Browse the latest releases, pick your favorite theatre, and book your seats in minutes.</p>
            <div class="cta-actions">
                <a href="movies.php" class="btn btn-filled"><i class="fa-solid fa-ticket"></i> Browse Movies</a>
                <a href="theatres.php" class="btn btn-outline" style="color:#fff; border-color:#fff;"><i class="fa-solid fa-building"></i> Find a Theatre</a>
            </div>
        </div>
    </section>

    <?php
    include_once "include/footer.php";
    
    ?>

</body>
</html>