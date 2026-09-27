<?php
session_start();

include_once "../connect.php";
include_once "side.php";

$movieCountQuery = "SELECT * FROM movies";
$movieCountResult = mysqli_query($conn, $movieCountQuery);
$totalMovies = mysqli_num_rows($movieCountResult);

$theatreQuery = "SELECT * FROM theatres";
$theatreResult = mysqli_query($conn, $theatreQuery);
$totalTheatres = mysqli_num_rows($theatreResult);

$showQuery = "SELECT * FROM shows";
$showResult = mysqli_query($conn, $showQuery);
$totalShows = mysqli_num_rows($showResult);

$userQuery = "SELECT * FROM users";
$userResult = mysqli_query($conn, $userQuery);
$totalUsers = mysqli_num_rows($userResult);

$bookingCountQuery = "SELECT * FROM bookings";
$bookingCountResult = mysqli_query($conn, $bookingCountQuery);
$totalBookings = mysqli_num_rows($bookingCountResult);

// Top 5 rated movies
$topMoviesQuery = "SELECT * FROM movies ORDER BY avg_rating DESC LIMIT 5";
$topMoviesResult = mysqli_query($conn, $topMoviesQuery);

// Upcoming shows - movie/theatre naam ke sath, date ke hisaab se sorted
$upcomingQuery = "SELECT shows.id, movies.title, movies.poster_url, theatres.name, shows.show_date, shows.show_time
                   FROM shows
                   JOIN movies ON shows.movie_id = movies.id
                   JOIN theatres ON shows.theatre_id = theatres.id
                   WHERE shows.show_date >= CURDATE()
                   ORDER BY shows.show_date ASC, shows.show_time ASC
                   LIMIT 5";
$upcomingResult = mysqli_query($conn, $upcomingQuery);

//  Chart 1: Shows per Month (Jan-Dec, poore saal ka overview) 
$monthNames = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
$monthCounts = array_fill(0, 12, 0); // 12 mahine, default 0

$monthlyQuery = "SELECT MONTH(show_date) as m, COUNT(*) as total FROM shows GROUP BY MONTH(show_date)";
$monthlyResult = mysqli_query($conn, $monthlyQuery);
foreach($monthlyResult as $row){
    $monthIndex = (int)$row["m"] - 1; // MySQL month 1-12, array index 0-11
    $monthCounts[$monthIndex] = (int)$row["total"];
}

// Chart 2: Movies by Genre 
$genreQuery = "SELECT genre, COUNT(*) as total FROM movies GROUP BY genre";
$genreResult = mysqli_query($conn, $genreQuery);

$genreLabels = array();
$genreCounts = array();
foreach($genreResult as $row){
    $genreLabels[] = $row["genre"];
    $genreCounts[] = (int)$row["total"];
}
?>
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
            content: "\f200";
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
        .stats-grid .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stats-grid .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stats-grid .stat-card:nth-child(4) { animation-delay: 0.4s; }

        .stat-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.08);
        }

        /* ===== Chart Cards Animation ===== */
        .charts-grid .chart-card {
            opacity: 0;
            transform: translateY(16px);
            animation: fadeSlideUp 0.55s ease forwards;
        }
        .charts-grid:nth-of-type(1) .chart-card:nth-child(1) { animation-delay: 0.45s; }
        .charts-grid:nth-of-type(1) .chart-card:nth-child(2) { animation-delay: 0.55s; }
        .charts-grid:nth-of-type(2) .chart-card:nth-child(1) { animation-delay: 0.65s; }
        .charts-grid:nth-of-type(2) .chart-card:nth-child(2) { animation-delay: 0.75s; }

        .chart-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .chart-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.07);
        }

        /* ===== Table Rows Animation ===== */
        .admin-table tbody tr {
            opacity: 0;
            animation: fadeIn 0.4s ease forwards;
            transition: background-color 0.2s ease;
        }
        .admin-table tbody tr:nth-child(1) { animation-delay: 0.8s; }
        .admin-table tbody tr:nth-child(2) { animation-delay: 0.85s; }
        .admin-table tbody tr:nth-child(3) { animation-delay: 0.9s; }
        .admin-table tbody tr:nth-child(4) { animation-delay: 0.95s; }
        .admin-table tbody tr:nth-child(5) { animation-delay: 1s; }

        .admin-table tbody tr:hover {
            background-color: rgba(168, 85, 247, 0.05);
        }

        /* ===== Top Rated Movies List Animation ===== */
        .top-movie-item {
            opacity: 0;
            transform: translateX(-10px);
            animation: fadeSlideRight 0.4s ease forwards;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }
        .top-movie-item:nth-child(1) { animation-delay: 0.8s; }
        .top-movie-item:nth-child(2) { animation-delay: 0.88s; }
        .top-movie-item:nth-child(3) { animation-delay: 0.96s; }
        .top-movie-item:nth-child(4) { animation-delay: 1.04s; }
        .top-movie-item:nth-child(5) { animation-delay: 1.12s; }

        .top-movie-item:hover {
            transform: translateX(3px);
        }

        @keyframes fadeSlideRight {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* ===== Topbar bell subtle pulse when there are bookings ===== */
        .topbar-bell .badge {
            animation: pulseIcon 2s ease-in-out infinite;
        }

        /* ===== Buttons ===== */
        .btn-add {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }
    
        </style>
</head>
<body class="dashboard-body">


    <!-- ===== Main content ===== -->
    <div class="admin-content">

        <!-- ===== Top bar: search + profile ===== -->
        <div class="topbar">
            <form class="topbar-search" action="view_movies.php" method="GET">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Search movies...">
            </form>
            <div class="topbar-right">
                <a href="view_bookings.php" class="topbar-bell" title="View new bookings">
                    <i class="fa-solid fa-bell"></i>
                    <span class="badge"><?php echo $totalBookings; ?></span>
                </a>
                <div class="topbar-profile">
                    <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION["admin_name"], 0, 1)); ?></div>
                    <div>
                        <p class="name">Welcome, <?php echo $_SESSION["admin_name"]; ?></p>
                        <p class="role">Admin</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-topbar">
            <div class="page-heading">
                <h1>Dashboard</h1>
                <p class="page-subtitle">Overview of your cinema's performance</p>
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
            <div class="stat-card">
                <div class="stat-icon users"><i class="fa-solid fa-users"></i></div>
                <div>
                    <p class="stat-label">Registered Users</p>
                    <p class="stat-value"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </section>

        <!-- ===== Charts ===== -->
        <section class="charts-grid">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h2><i class="fa-solid fa-chart-line"></i> Shows Overview</h2>
                    <i class="fa-solid fa-ellipsis-vertical dots"></i>
                </div>
                <div class="chart-card-body">
                    <canvas id="showsChart" height="260"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-card-header">
                    <h2><i class="fa-solid fa-chart-pie"></i> Movies by Genre</h2>
                    <i class="fa-solid fa-ellipsis-vertical dots"></i>
                </div>
                <div class="chart-card-body">
                    <canvas id="genreChart" height="260"></canvas>
                </div>
            </div>
        </section>

        <!-- ===== Upcoming Shows + Top Rated Movies ===== -->
        <section class="charts-grid">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h2><i class="fa-solid fa-clock"></i> Upcoming Shows</h2>
                    <a href="view_shows.php" class="btn-add"><i class="fa-solid fa-ticket"></i> View All</a>
                </div>

                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Poster</th>
                                <th>Movie</th>
                                <th>Theatre</th>
                                <th>Date</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($upcomingResult) == 0){ ?>
                                <tr><td colspan="5" style="text-align:center; color:#64748b; padding:24px;">There are no upcoming shows.
</td></tr>
                            <?php } ?>
                            <?php foreach($upcomingResult as $show){ ?>
                                <tr>
                                    <td><img src="../<?php echo $show["poster_url"]; ?>" alt="<?php echo $show["title"]; ?>"></td>
                                    <td><?php echo $show["title"]; ?></td>
                                    <td><?php echo $show["name"]; ?></td>
                                    <td><?php echo $show["show_date"]; ?></td>
                                    <td><?php echo $show["show_time"]; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <h2><i class="fa-solid fa-star"></i> Top Rated Movies</h2>
                </div>
                <div class="chart-card-body">
                    <div class="top-movies-list">
                        <?php $rank = 1; foreach($topMoviesResult as $tm){ ?>
                            <div class="top-movie-item">
                                <div class="top-movie-rank"><?php echo $rank; ?></div>
                                <img src="../<?php echo $tm["poster_url"]; ?>" alt="<?php echo $tm["title"]; ?>">
                                <div class="top-movie-info">
                                    <p class="title"><?php echo $tm["title"]; ?></p>
                                    <p class="genre"><?php echo $tm["genre"]; ?></p>
                                </div>
                                <div class="top-movie-rating"><i class="fa-solid fa-star"></i> <?php echo $tm["avg_rating"]; ?></div>
                            </div>
                        <?php $rank++; } ?>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        // ===== Sidebar toggle (mobile) — same pattern as index.php's hamburger =====
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
        }

        document.querySelectorAll(".side-links a").forEach(function(link){
            link.addEventListener("click", function(){
                document.getElementById("sidebar").classList.remove("active");
            });
        });

        document.addEventListener("click", function(e){
            const sidebar = document.getElementById("sidebar");
            const toggleBtn = document.querySelector(".sidebar-toggle");
            if (sidebar && sidebar.classList.contains("active") &&
                !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove("active");
            }
        });

        // ===== Shows Overview - Area/Line chart (poore saal ka trend) =====
        const showsCtx = document.getElementById('showsChart').getContext('2d');

        const gradient = showsCtx.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(168,85,247,0.35)');
        gradient.addColorStop(1, 'rgba(168,85,247,0.02)');

        new Chart(showsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($monthNames); ?>,
                datasets: [{
                    label: 'Shows',
                    data: <?php echo json_encode($monthCounts); ?>,
                    borderColor: '#a855f7',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#a855f7',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#9584b0' },
                        grid: { color: 'rgba(168,85,247,0.1)' }
                    },
                    x: {
                        ticks: { color: '#9584b0' },
                        grid: { display: false }
                    }
                }
            }
        });

        // ===== Movies by Genre - Donut chart =====
        const genreCtx = document.getElementById('genreChart');
        new Chart(genreCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($genreLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($genreCounts); ?>,
                    backgroundColor: ['#a855f7', '#c084fc', '#9333ea', '#8b5cf6', '#d8b4fe', '#6b21a8'],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 12 }, padding: 16, color: '#d4c5e8' } }
                }
            }
        });
    </script>

</body>
</html>