<?php
session_start();

include_once "connect.php";

$bookingid = 0;
if(isset($_SESSION["last_booking_id"])){
    $bookingid = intval($_SESSION["last_booking_id"]);
}

if($bookingid == 0){
    header("Location: index.php");
    exit();
}

$query = "SELECT bookings.*, movies.title, movies.poster_url, theatres.name AS theatre_name,
                 shows.show_date, shows.show_time, seat_classes.class_name
          FROM bookings
          JOIN movies ON bookings.movie_id = movies.id
          JOIN shows ON bookings.show_id = shows.id
          JOIN theatres ON shows.theatre_id = theatres.id
          JOIN seat_classes ON bookings.seat_class_id = seat_classes.id
          WHERE bookings.id = $bookingid";
$result = mysqli_query($conn, $query);
$booking = mysqli_fetch_assoc($result);

if(!$booking){
    header("Location: index.php");
    exit();
}

$seatsQuery = "SELECT seat_number FROM booked_seats WHERE booking_id = $bookingid";
$seatsResult = mysqli_query($conn, $seatsQuery);

$seatNumbers = array();
foreach($seatsResult as $row){
    $seatNumbers[] = $row["seat_number"];
}
$seatNumbersText = implode(", ", $seatNumbers);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - MovieBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background-color: #060010;
            color: #e8def8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        a { text-decoration: none; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            80% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        .success-wrap {
            max-width: 460px;
            width: 100%;
        }

        /* ===== Success icon + heading (upar wala hissa) ===== */
        .success-header {
            text-align: center;
            margin-bottom: 24px;
            animation: fadeInUp 0.5s ease;
        }

        .success-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #4ade80, #16a34a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: #fff;
            margin: 0 auto 16px;
            animation: popIn 0.5s ease 0.1s backwards;
        }

        .success-header h1 {
            font-size: 22px;
            color: #f3e8ff;
            margin-bottom: 6px;
        }

        .success-header p {
            font-size: 13.5px;
            color: #9584b0;
        }

        /* ===== Ticket card ===== */
        .ticket {
            background: #12081f;
            border: 1px solid #2a1a3d;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.55);
            animation: fadeInUp 0.6s ease 0.15s backwards;
        }

        .ticket-top {
            display: flex;
            gap: 16px;
            padding: 22px;
        }

        .ticket-top img {
            width: 70px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .ticket-top h2 {
            font-size: 18px;
            color: #f3e8ff;
            margin-bottom: 8px;
        }

        .ticket-top .detail-line {
            font-size: 13px;
            color: #b8a8d4;
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 5px;
        }

        .ticket-top .detail-line i {
            color: #c084fc;
            width: 14px;
        }

        /* ===== Dashed divider, jaise asal ticket mein hota hai ===== */
        .ticket-divider {
            position: relative;
            border-top: 2px dashed #2a1a3d;
            margin: 0 22px;
        }

        .ticket-divider::before,
        .ticket-divider::after {
            content: '';
            position: absolute;
            top: -12px;
            width: 24px;
            height: 24px;
            background: #060010;
            border-radius: 50%;
        }

        .ticket-divider::before { left: -34px; }
        .ticket-divider::after { right: -34px; }

        .ticket-bottom {
            padding: 22px;
        }

        .ticket-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 18px;
        }

        .ticket-field .label {
            font-size: 11px;
            color: #9584b0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .ticket-field .value {
            font-size: 14.5px;
            font-weight: 700;
            color: #f3e8ff;
        }

        .ticket-field.full {
            grid-column: 1 / -1;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(168,85,247,0.08);
            border: 1px solid rgba(168,85,247,0.25);
            border-radius: 10px;
            padding: 14px 16px;
        }

        .amount-row .label {
            font-size: 13px;
            color: #c9b8dc;
        }

        .amount-row .value {
            font-size: 20px;
            font-weight: 800;
            color: #c084fc;
        }

        .booking-id-badge {
            text-align: center;
            font-size: 12.5px;
            color: #9584b0;
            margin-top: 16px;
        }

        .booking-id-badge strong {
            color: #f3e8ff;
        }

        /* ===== Screenshot hint ===== */
        .screenshot-hint {
            text-align: center;
            font-size: 12.5px;
            color: #9584b0;
            margin-top: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            animation: fadeInUp 0.5s ease 0.3s backwards;
        }

        .screenshot-hint i {
            color: #c084fc;
        }

        /* ===== Buttons ===== */
        .action-row {
            display: flex;
            gap: 12px;
            margin-top: 22px;
            animation: fadeInUp 0.5s ease 0.35s backwards;
        }

        .action-row a {
            flex: 1;
            text-align: center;
            padding: 13px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
        }

        .btn-primary {
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
        }

        .btn-secondary {
            background: #12081f;
            border: 1px solid #2a1a3d;
            color: #d4c5e8;
        }

        @media (max-width: 480px) {
            .ticket-top { flex-direction: column; }
            .action-row { flex-direction: column; }
        }
    </style>
</head>
<body>

    <div class="success-wrap">

        <div class="success-header">
            <div class="success-icon"><i class="fa-solid fa-check"></i></div>
            <h1>Booking Confirmed!</h1>
            <p>Your tickets are ready. See you at the movies!</p>
        </div>

        <div class="ticket">
            <div class="ticket-top">
                <img src="<?php echo htmlspecialchars($booking["poster_url"]); ?>" alt="<?php echo htmlspecialchars($booking["title"]); ?>">
                <div>
                    <h2><?php echo htmlspecialchars($booking["title"]); ?></h2>
                    <div class="detail-line"><i class="fa-solid fa-building"></i> <?php echo htmlspecialchars($booking["theatre_name"]); ?></div>
                    <div class="detail-line"><i class="fa-regular fa-calendar"></i> <?php echo date("D, d M Y", strtotime($booking["show_date"])); ?></div>
                    <div class="detail-line"><i class="fa-regular fa-clock"></i> <?php echo date("g:i A", strtotime($booking["show_time"])); ?></div>
                </div>
            </div>

            <div class="ticket-divider"></div>

            <div class="ticket-bottom">
                <div class="ticket-grid">
                    <div class="ticket-field">
                        <div class="label">Booked For</div>
                        <div class="value"><?php echo htmlspecialchars($booking["customer_name"]); ?></div>
                    </div>
                    <div class="ticket-field">
                        <div class="label">Phone</div>
                        <div class="value"><?php echo htmlspecialchars($booking["number"]); ?></div>
                    </div>
                    <div class="ticket-field">
                        <div class="label">Seat Class</div>
                        <div class="value"><?php echo htmlspecialchars($booking["class_name"]); ?></div>
                    </div>
                    <div class="ticket-field">
                        <div class="label">Payment</div>
                        <div class="value"><?php echo htmlspecialchars($booking["payment_method"]); ?></div>
                    </div>
                    <div class="ticket-field full">
                        <div class="label">Seats (<?php echo $booking["seats"]; ?>)</div>
                        <div class="value"><?php echo htmlspecialchars($seatNumbersText); ?></div>
                    </div>
                </div>

                <div class="amount-row">
                    <span class="label">Total Amount</span>
                    <span class="value">Rs. <?php echo number_format($booking["amount"], 2); ?></span>
                </div>

                <div class="booking-id-badge">Booking ID: <strong>#<?php echo $booking["id"]; ?></strong></div>
            </div>
        </div>

        <p class="screenshot-hint"><i class="fa-solid fa-camera"></i> Take a screenshot of this ticket as your proof of booking</p>

        <div class="action-row">
            <a href="my_bookings.php" class="btn-secondary"><i class="fa-solid fa-ticket"></i> My Bookings</a>
            <a href="index.php" class="btn-primary"><i class="fa-solid fa-house"></i> Back to Home</a>
        </div>

    </div>

</body>
</html>