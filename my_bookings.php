<?php
session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: login.php");
    exit();
}

// ===== Database connection =====
include_once "connect.php";

$userid = $_SESSION["user_id"];

// Is user ki saari bookings nikal rahe hain, movie/show/theatre/seat class ke naam ke sath
$query = "SELECT bookings.*, movies.title, movies.poster_url, theatres.name AS theatre_name,
                 shows.show_date, shows.show_time, seat_classes.class_name
          FROM bookings
          JOIN movies ON bookings.movie_id = movies.id
          JOIN shows ON bookings.show_id = shows.id
          JOIN theatres ON shows.theatre_id = theatres.id
          JOIN seat_classes ON bookings.seat_class_id = seat_classes.id
          WHERE bookings.user_id = $userid
          ORDER BY bookings.id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - MovieBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        html, body { height: 100%; }

        body {
            position: relative;
            background-color: #060010;
            color: #e8def8;
            overflow-x: hidden;
        }

        a { text-decoration: none; }

        /* ===== Ambient background glow ===== */
        .bg-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.3;
            z-index: -1;
        }

        .glow-1 { width: 420px; height: 420px; background: #6b21a8; top: -120px; left: -100px; }
        .glow-2 { width: 380px; height: 380px; background: #a855f7; bottom: -160px; right: -100px; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== Navbar ===== */
        .navbar {
            background-color: #0d0518;
            border-bottom: 1px solid #2a1a3d;
            padding: 16px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
            animation: fadeInUp 0.5s ease backwards;
        }

        .logo {
            font-family: 'Bebas Neue', 'Segoe UI', Arial, sans-serif;
            font-size: 26px;
            font-weight: 400;
            letter-spacing: 1.2px;
            color: #f3e8ff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            color: #c084fc;
            animation: iconPulse 2.8s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { text-shadow: 0 0 0 rgba(168,85,247,0); transform: scale(1); }
            50% { text-shadow: 0 0 12px rgba(168,85,247,0.6); transform: scale(1.08); }
        }

        .logo span {
            background: linear-gradient(90deg, #a855f7, #e9d5ff, #a855f7);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 3.5s linear infinite;
        }

        @keyframes shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        .back-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #9584b0;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .back-link:hover {
            color: #c084fc;
            transform: translateX(-3px);
        }

        /* ===== Page ===== */
        .page-container {
            position: relative;
            z-index: 2;
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 24px 60px;
        }

        .page-heading {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 30px;
            animation: fadeInUp 0.5s ease 0.1s backwards;
        }

        .page-heading .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            box-shadow: 0 8px 20px rgba(168,85,247,0.3);
        }

        .page-heading h1 {
            font-size: 24px;
            color: #f3e8ff;
            font-weight: 800;
        }

        .page-heading .sub {
            font-size: 13px;
            color: #9584b0;
            margin-top: 2px;
        }

        .booking-card {
            background: #12081f;
            border: 1px solid #2a1a3d;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.4);
            opacity: 0;
            animation: fadeInUp 0.5s ease forwards;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }

        .booking-card:nth-child(1) { animation-delay: 0.15s; }
        .booking-card:nth-child(2) { animation-delay: 0.22s; }
        .booking-card:nth-child(3) { animation-delay: 0.29s; }
        .booking-card:nth-child(4) { animation-delay: 0.36s; }
        .booking-card:nth-child(n+5) { animation-delay: 0.43s; }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 34px rgba(168,85,247,0.2);
            border-color: transparent;
        }

        .booking-card img {
            width: 90px;
            height: 130px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }

        .booking-info {
            flex: 1;
        }

        .booking-info h3 {
            font-size: 17px;
            color: #f3e8ff;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .booking-info .detail-row {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 13.5px;
            color: #b8a8d4;
            margin-bottom: 4px;
        }

        .booking-info .detail-row span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .booking-info .detail-row i {
            color: #c084fc;
        }

        .cancel-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #f87171;
            font-size: 12.5px;
            font-weight: 700;
            padding: 7px 14px;
            border-radius: 20px;
            transition: 0.2s ease;
        }

        .cancel-btn:hover {
            background: #ef4444;
            color: #fff;
        }

        .past-tag {
            display: inline-block;
            margin-top: 10px;
            background: rgba(255,255,255,0.06);
            color: #9584b0;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
        }

        .booking-amount {
            text-align: right;
            flex-shrink: 0;
        }

        .booking-amount .amount {
            font-size: 18px;
            font-weight: 800;
            color: #f3e8ff;
        }

        .booking-amount .booking-id {
            font-size: 12px;
            color: #9584b0;
            margin-top: 4px;
        }

        .no-bookings {
            background: #12081f;
            border: 1px solid #2a1a3d;
            border-radius: 16px;
            padding: 50px 20px;
            text-align: center;
            color: #9584b0;
            animation: fadeInUp 0.5s ease 0.15s backwards;
        }

        .no-bookings a {
            display: inline-block;
            margin-top: 14px;
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            padding: 10px 22px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .no-bookings a:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(168,85,247,0.35);
        }

        @media (max-width: 600px) {
            .navbar { padding: 14px 20px; }
            .page-container { padding: 32px 20px 50px; }
            .booking-card {
                flex-direction: column;
            }
            .booking-amount {
                text-align: left;
            }
        }
    </style>
</head>
<body>

    <span class="bg-glow glow-1"></span>
    <span class="bg-glow glow-2"></span>

    <header class="navbar">
        <div class="logo"><i class="fa-solid fa-clapperboard"></i>CINE<span>AURA</span></div>
        <a href="index.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
    </header>

    <div class="page-container">
        <div class="page-heading">
            <div class="icon-box"><i class="fa-solid fa-ticket"></i></div>
            <div>
                <h1>My Bookings</h1>
                <p class="sub">All your movie tickets in one place</p>
            </div>
        </div>

        <?php if(mysqli_num_rows($result) == 0){ ?>
            <div class="no-bookings">
                <p>You haven't booked any tickets yet.</p>
                <a href="movies.php">Browse Movies</a>
            </div>
        <?php }else{ ?>
            <?php foreach($result as $booking){ ?>
                <?php $isUpcoming = strtotime($booking["show_date"]) >= strtotime(date("Y-m-d")); ?>
                <div class="booking-card">
                    <img src="<?php echo $booking["poster_url"]; ?>" alt="<?php echo $booking["title"]; ?>">
                    <div class="booking-info">
                        <h3><?php echo $booking["title"]; ?></h3>
                        <div class="detail-row">
                            <span><i class="fa-solid fa-building"></i> <?php echo $booking["theatre_name"]; ?></span>
                            <span><i class="fa-regular fa-calendar"></i> <?php echo $booking["show_date"]; ?></span>
                            <span><i class="fa-regular fa-clock"></i> <?php echo date("g:i A", strtotime($booking["show_time"])); ?></span>
                        </div>
                        <div class="detail-row">
                            <span><i class="fa-solid fa-chair"></i> <?php echo $booking["class_name"]; ?></span>
                            <span><i class="fa-solid fa-users"></i> <?php echo $booking["seats"]; ?> Seats</span>
                        </div>
                        <?php if($isUpcoming){ ?>
                            <a href="cancel_booking.php?id=<?php echo $booking["id"]; ?>" class="cancel-btn" onclick="return confirm('Are you sure you want to cancel this booking?');">
                                <i class="fa-solid fa-xmark"></i> Cancel Booking
                            </a>
                        <?php }else{ ?>
                            <span class="past-tag">Show Completed</span>
                        <?php } ?>
                    </div>
                    <div class="booking-amount">
                        <div class="amount">Rs. <?php echo number_format($booking["amount"], 2); ?></div>
                        <div class="booking-id">Booking #<?php echo $booking["id"]; ?></div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

</body>
</html>