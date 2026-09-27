<?php
session_start();


// ===== Database connection =====
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

// Bookings ko movies ke naam ke sath JOIN kar rahe hain
$bookingQuery = "SELECT bookings.id, bookings.customer_name, bookings.number, movies.title,
                  theatres.name AS theatre_name, shows.show_date, shows.show_time,
                  seat_classes.class_name, bookings.seats, bookings.amount, bookings.booking_date
                  FROM bookings
                  JOIN movies ON bookings.movie_id = movies.id
                  LEFT JOIN shows ON bookings.show_id = shows.id
                  LEFT JOIN theatres ON shows.theatre_id = theatres.id
                  LEFT JOIN seat_classes ON bookings.seat_class_id = seat_classes.id";
$bookingResult = mysqli_query($conn, $bookingQuery);
$totalBookings = mysqli_num_rows($bookingResult);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* ===== Page heading (icon + gradient title + subtitle), same style as other admin pages ===== */
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
            content: "\f46d";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            -webkit-text-fill-color: initial;
            color: #c084fc;
            background: rgba(168, 85, 247, 0.15);
            padding: 10px;
            border-radius: 12px;
            font-size: 1.3rem;
            animation: pulseIcon 2.5s ease-in-out infinite;
        }

        .page-heading .page-subtitle {
            margin: 6px 0 0 62px;
            color: #9584b0;
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
    </style>
</head>
<body class="dashboard-body">

   
    <!-- ===== Main content ===== -->
    <div class="admin-content">

        <div class="admin-topbar">
            <div class="page-heading">
                <h1>Bookings</h1>
                <p class="page-subtitle">View all ticket bookings made on MovieBook</p>
            </div>
        </div>

        <!-- ===== Stats ===== -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                <div>
                    <p class="stat-label">Total Bookings</p>
                    <p class="stat-value"><?php echo $totalBookings; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-film"></i></div>
                <div>
                    <p class="stat-label">Total Movies</p>
                    <p class="stat-value"><?php echo $totalMovies; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-building"></i></div>
                <div>
                    <p class="stat-label">Total Theatres</p>
                    <p class="stat-value"><?php echo $totalTheatres; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-ticket"></i></div>
                <div>
                    <p class="stat-label">Total Shows</p>
                    <p class="stat-value"><?php echo $totalShows; ?></p>
                </div>
            </div>
        </section>

        <!-- ===== All Bookings Table ===== -->
        <section class="manage-section">
            <div class="manage-header">
                <h2><i class="fa-solid fa-clipboard-list"></i> All Bookings (<?php echo $totalBookings; ?>)</h2>
            </div>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Phone Number</th>
                            <th>Movie</th>
                            <th>Theatre</th>
                            <th>Show Date/Time</th>
                            <th>Class</th>
                            <th>Seats</th>
                            <th>Amount</th>
                            <th>Booking Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($totalBookings == 0){ ?>
                            <tr><td colspan="10" style="text-align:center; color:#9584b0; padding:30px;">Abhi tak koi booking nahi hui.</td></tr>
                        <?php } ?>
                        <?php foreach($bookingResult as $booking){ ?>
                            <tr>
                                <td>#<?php echo $booking["id"]; ?></td>
                                <td><span class="customer-name"><?php echo $booking["customer_name"]; ?></span></td>
                                <td><?php echo $booking["number"]; ?></td>
                                <td><span class="movie-badge"><?php echo $booking["title"]; ?></span></td>
                                <td><?php echo $booking["theatre_name"] ?: '-'; ?></td>
                                <td><?php echo $booking["show_date"] ? $booking["show_date"]." ".date("g:i A", strtotime($booking["show_time"])) : '-'; ?></td>
                                <td><?php echo $booking["class_name"] ?: '-'; ?></td>
                                <td><?php echo $booking["seats"]; ?></td>
                                <td><span class="amount-value">Rs. <?php echo $booking["amount"]; ?></span></td>
                                <td><?php echo $booking["booking_date"]; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>

</body>
</html>