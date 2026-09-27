<?php
session_start();

include_once "connect.php";

$movieQuery = "SELECT * FROM movies";
$movieResult = mysqli_query($conn, $movieQuery);

$selectedMovieId = "";
if(isset($_GET["movieid"])){
    $selectedMovieId = $_GET["movieid"];
}

$showResult = null;
$seatClassResult = null;

if($selectedMovieId != ""){
    $showQuery = "SELECT shows.id, theatres.name, theatres.location, shows.show_date, shows.show_time
                  FROM shows
                  JOIN theatres ON shows.theatre_id = theatres.id
                  WHERE shows.movie_id = $selectedMovieId";
    $showResult = mysqli_query($conn, $showQuery);

    $seatClassQuery = "SELECT * FROM seat_classes";
    $seatClassResult = mysqli_query($conn, $seatClassQuery);
}

$message = "";

if(isset($_SESSION["user_id"]) && isset($_POST["sub"])){

    $customername = $_POST["customername"];
    $phonenumber = $_POST["phonenumber"];
    $movieid = $_POST["movieid"];
    $showid = $_POST["showid"];
    $seatclassid = $_POST["seatclassid"];
    $adultseats = $_POST["adultseats"];
    $kidseats = $_POST["kidseats"];

    if(($adultseats + $kidseats) <= 0){
        $message = "Please select at least 1 seat.";
    }else{
        $_SESSION["pending_booking"] = array(
            "customername" => $customername,
            "phonenumber" => $phonenumber,
            "movieid" => $movieid,
            "showid" => $showid,
            "seatclassid" => $seatclassid,
            "adultseats" => $adultseats,
            "kidseats" => $kidseats
        );

        header("Location: select_seats.php");
        exit();
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ticket - MovieBook</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 20px;
            overflow-x: hidden;
        }

        a { text-decoration: none; }

        /* ===== Ambient background ===== */
        .bg-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.32;
            z-index: 0;
        }

        .glow-1 { width: 420px; height: 420px; background: #6b21a8; top: -120px; left: -100px; }
        .glow-2 { width: 380px; height: 380px; background: #a855f7; bottom: -140px; right: -100px; }

        .floating-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(2px);
            opacity: 0.5;
            animation: floatOrb 6s ease-in-out infinite;
            z-index: 0;
        }

        .orb-1 { width: 14px; height: 14px; background: #a855f7; top: 16%; left: 10%; animation-delay: 0s; }
        .orb-2 { width: 10px; height: 10px; background: #ffd700; top: 78%; left: 15%; animation-delay: 1.2s; }
        .orb-3 { width: 12px; height: 12px; background: #a855f7; top: 20%; right: 12%; animation-delay: 2.4s; }
        .orb-4 { width: 8px; height: 8px; background: #fff; top: 74%; right: 16%; animation-delay: 0.8s; }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-22px); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes iconPulse {
            0%, 100% { text-shadow: 0 0 0 rgba(168,85,247,0); transform: scale(1); }
            50% { text-shadow: 0 0 12px rgba(168,85,247,0.6); transform: scale(1.06); }
        }

        @keyframes shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== Form Card ===== */
        .form-card {
            position: relative;
            z-index: 2;
            max-width: 480px;
            width: 100%;
            background: rgba(18,8,31,0.9);
            border: 1px solid rgba(168,85,247,0.18);
            backdrop-filter: blur(12px);
            padding: 40px 38px;
            border-radius: 18px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.55);
            animation: fadeInUp 0.6s ease backwards;
        }

        .logo {
            text-align: center;
            font-family: 'Bebas Neue', 'Segoe UI', Arial, sans-serif;
            font-size: 26px;
            font-weight: 400;
            letter-spacing: 1.5px;
            color: #f3e8ff;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            animation: fadeInUp 0.6s ease 0.1s backwards;
        }

        .logo i {
            color: #c084fc;
            animation: iconPulse 2.8s ease-in-out infinite;
        }

        .logo span {
            background: linear-gradient(90deg, #a855f7, #e9d5ff, #a855f7);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 3.5s linear infinite;
        }

        .form-card h2 {
            text-align: center;
            color: #9584b0;
            font-size: 14.5px;
            font-weight: 500;
            margin-bottom: 28px;
            animation: fadeInUp 0.6s ease 0.15s backwards;
        }

        .message {
            background-color: rgba(74,222,128,0.12);
            border: 1px solid rgba(74,222,128,0.35);
            color: #4ade80;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 18px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            animation: fadeInUp 0.4s ease;
        }

        .form-group {
            margin-bottom: 18px;
            animation: fadeInUp 0.5s ease backwards;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 700;
            color: #d4c5e8;
        }

        .form-group label i {
            color: #c084fc;
            font-size: 12px;
            width: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1.5px solid #2a1a3d;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            background: #0d0518;
            color: #e8def8;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group select {
            cursor: pointer;
        }

        .form-group select option {
            background: #0d0518;
            color: #e8def8;
        }

        .form-group input::placeholder {
            color: #7a6a94;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #a855f7;
            box-shadow: 0 0 0 3px rgba(168,85,247,0.18);
        }

        .form-group input[type="submit"] {
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
            padding: 13px;
            border-radius: 10px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .form-group input[type="submit"]:hover {
            background: linear-gradient(135deg, #9333ea, #581c87);
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(168,85,247,0.4);
        }

        .seats-row {
            display: flex;
            gap: 14px;
        }

        .seats-row .form-group {
            flex: 1;
        }

        .back-link {
            text-align: center;
            margin-top: 16px;
            animation: fadeInUp 0.5s ease 0.2s backwards;
        }

        .back-link a {
            color: #c084fc;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .back-link a:hover {
            color: #e9d5ff;
            transform: translateX(-3px);
        }

        .no-shows {
            background-color: rgba(251,191,36,0.12);
            border: 1px solid rgba(251,191,36,0.35);
            color: #fbbf24;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: fadeInUp 0.4s ease;
        }

        /* ===== Login Required card ===== */
        .login-required {
            text-align: center;
            padding: 6px 0 4px;
            animation: fadeInUp 0.5s ease 0.15s backwards;
        }

        .login-required .lock-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: rgba(168,85,247,0.15);
            color: #c084fc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .login-required p {
            color: #b8a8d4;
            font-size: 14px;
            margin-bottom: 26px;
            line-height: 1.6;
        }

        .auth-buttons {
            display: flex;
            gap: 12px;
        }

        .auth-buttons a {
            flex: 1;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .auth-buttons .btn-auth-login {
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
        }

        .auth-buttons .btn-auth-login:hover {
            background: linear-gradient(135deg, #9333ea, #581c87);
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(168,85,247,0.4);
        }

        .auth-buttons .btn-auth-register {
            background: transparent;
            color: #d8b4fe;
            border: 1px solid #a855f7;
        }

        .auth-buttons .btn-auth-register:hover {
            background: #a855f7;
            color: #fff;
        }

        /* ===== Progressive form reveal ===== */
        #stepTwo {
            animation: slideDown 0.5s ease;
        }

        @media (max-width: 480px) {
            body {
                padding: 16px;
            }
            .form-card {
                padding: 28px 22px;
            }
            .seats-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>

    <span class="bg-glow glow-1"></span>
    <span class="bg-glow glow-2"></span>
    <span class="floating-orb orb-1"></span>
    <span class="floating-orb orb-2"></span>
    <span class="floating-orb orb-3"></span>
    <span class="floating-orb orb-4"></span>

    <div class="form-card">
        <div class="logo"><i class="fa-solid fa-clapperboard"></i>Movie<span>Book</span></div>

        <?php if(!isset($_SESSION["user_id"])){ ?>

            <h2>Login required to continue booking</h2>
            <div class="login-required">
                <div class="lock-icon"><i class="fa-solid fa-lock"></i></div>
                <p>Please login or create an account to book your movie tickets.</p>
                <div class="auth-buttons">
                    <a href="login.php?redirect=book_ticket.php" class="btn-auth-login">Login</a>
                    <a href="register.php?redirect=book_ticket.php" class="btn-auth-register">Register</a>
                </div>
            </div>

        <?php }else{ ?>

        <h2>Book Your Ticket</h2>

        <?php if($message){ ?>
            <div class="message"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $message; ?></div>
        <?php } ?>

        <!-- ===== Step 1: Movie select karein ===== -->
        <form action="book_ticket.php" method="get">
            <div class="form-group" style="animation-delay:0.2s;">
                <label><i class="fa-solid fa-film"></i> Select Movie</label>
                <select name="movieid" onchange="this.form.submit()" required>
                    <option value="" selected disabled>Choose a movie</option>
                    <?php foreach($movieResult as $movie){ ?>
                        <option value="<?php echo $movie["id"]; ?>" <?php if($movie["id"] == $selectedMovieId){ echo "selected"; } ?>>
                            <?php echo $movie["title"]; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>

        <!-- ===== Step 2: Movie select hone ke baad baaki form ===== -->
        <?php if($selectedMovieId != ""){ ?>
            <div id="stepTwo">
            <?php if(mysqli_num_rows($showResult) == 0){ ?>
                <div class="no-shows"><i class="fa-solid fa-triangle-exclamation"></i> No shows are currently available for this movie.</div>
            <?php }else{ ?>

                <form action="book_ticket.php" method="post">
                    <input type="hidden" name="movieid" value="<?php echo $selectedMovieId; ?>">

                    <div class="form-group" style="animation-delay:0.25s;">
                        <label><i class="fa-solid fa-user"></i> Your Name</label>
                        <input type="text" name="customername" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group" style="animation-delay:0.3s;">
                        <label><i class="fa-solid fa-phone"></i> Phone Number</label>
                        <input type="text" name="phonenumber" placeholder="e.g. 0300-1234567" required>
                    </div>

                    <div class="form-group" style="animation-delay:0.35s;">
                        <label><i class="fa-solid fa-clapperboard"></i> Select Show (Theatre - Date - Time)</label>
                        <select name="showid" required>
                            <option value="" selected disabled>Choose a show</option>
                            <?php foreach($showResult as $show){ ?>
                                <option value="<?php echo $show["id"]; ?>">
                                    <?php echo $show["name"]; ?> (<?php echo $show["location"]; ?>) — <?php echo $show["show_date"]; ?> at <?php echo date("g:i A", strtotime($show["show_time"])); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group" style="animation-delay:0.4s;">
                        <label><i class="fa-solid fa-chair"></i> Select Seat Class</label>
                        <select name="seatclassid" required>
                            <option value="" selected disabled>Choose a class</option>
                            <?php foreach($seatClassResult as $sc){ ?>
                                <option value="<?php echo $sc["id"]; ?>">
                                    <?php echo $sc["class_name"]; ?> — Adult Rs. <?php echo $sc["adult_price"]; ?> / Kid Rs. <?php echo $sc["kid_price"]; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="seats-row">
                        <div class="form-group" style="animation-delay:0.45s;">
                            <label><i class="fa-solid fa-person"></i> Adult Seats</label>
                            <input type="number" name="adultseats" min="0" value="0" required>
                        </div>
                        <div class="form-group" style="animation-delay:0.45s;">
                            <label><i class="fa-solid fa-child"></i> Kid Seats</label>
                            <input type="number" name="kidseats" min="0" value="0">
                        </div>
                    </div>

                    <div class="form-group" style="animation-delay:0.5s;">
                        <input type="submit" value="Confirm Booking" name="sub">
                    </div>
                </form>

            <?php } ?>
            </div>
        <?php } ?>

        <?php } ?>

        <div class="back-link">
            <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
        </div>
    </div>

</body>
</html>