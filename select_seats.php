<?php
session_start();

// ===== Database connection =====
include_once "connect.php";

// Agar koi pending booking session mein nahi hai to seedha book_ticket.php pe bhej do
if(!isset($_SESSION["pending_booking"])){
    header("Location: book_ticket.php");
    exit();
}

$booking = $_SESSION["pending_booking"];
$totalSeatsNeeded = intval($booking["adultseats"]) + intval($booking["kidseats"]);

// ===== Movie, show, aur seat class ki detail nikal rahe hain (dikhane ke liye) =====
$movieQuery = "SELECT * FROM movies WHERE id = " . intval($booking["movieid"]);
$movieResult = mysqli_query($conn, $movieQuery);
$movie = mysqli_fetch_assoc($movieResult);

$showQuery = "SELECT shows.*, theatres.name AS theatre_name
              FROM shows
              JOIN theatres ON shows.theatre_id = theatres.id
              WHERE shows.id = " . intval($booking["showid"]);
$showResult = mysqli_query($conn, $showQuery);
$show = mysqli_fetch_assoc($showResult);

$seatClassQuery = "SELECT * FROM seat_classes WHERE id = " . intval($booking["seatclassid"]);
$seatClassResult = mysqli_query($conn, $seatClassQuery);
$seatClass = mysqli_fetch_assoc($seatClassResult);

// ===== Is show ke liye jo seats already book ho chuki hain, unki list nikal rahe hain =====
$bookedQuery = "SELECT seat_number FROM booked_seats WHERE show_id = " . intval($booking["showid"]);
$bookedResult = mysqli_query($conn, $bookedQuery);
$bookedSeats = array();
foreach($bookedResult as $row){
    $bookedSeats[] = $row["seat_number"];
}

// ===== Seat map generate kar rahe hain (6 rows: A-F, 10 seats har row mein) =====
$rows = array("A", "B", "C", "D", "E", "F");
$seatsPerRow = 10;

$message = "";
if(isset($_GET["error"])){
    $message = "Please select exactly " . $totalSeatsNeeded . " seat(s) before continuing.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats - MovieBook</title>
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
        }

        a { text-decoration: none; }

        /* ===== Top bar ===== */
        .top-bar {
            background: linear-gradient(135deg, #000000, #2e1065);
            padding: 20px 30px;
            color: #fff;
        }

        .top-bar-inner {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .top-bar .back-link {
            color: #b8a8d4;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .top-bar h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .top-bar .sub {
            font-size: 13px;
            color: #b8a8d4;
        }

        /* ===== Page container ===== */
        .page-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px 20px 60px;
        }

        .error-message {
            background: #3a1420;
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .instructions {
            text-align: center;
            margin-bottom: 26px;
        }

        .instructions .seats-needed {
            font-size: 15px;
            font-weight: 700;
            color: #f3e8ff;
        }

        .instructions .seats-needed span {
            color: #c084fc;
        }

        /* ===== Screen ===== */
        .screen-wrap {
            text-align: center;
            margin-bottom: 40px;
        }

        .screen {
            width: 90%;
            max-width: 500px;
            height: 8px;
            background: linear-gradient(90deg, transparent, #a855f7, transparent);
            border-radius: 50%;
            margin: 0 auto 8px;
        }

        .screen-label {
            font-size: 11px;
            color: #9584b0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* ===== Seat grid ===== */
        .seat-map {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 36px;
        }

        .seat-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .row-label {
            width: 20px;
            font-size: 12px;
            font-weight: 700;
            color: #9584b0;
            text-align: center;
        }

        .seat {
            width: 32px;
            height: 32px;
            border-radius: 8px 8px 4px 4px;
            border: 1.5px solid #2a1a3d;
            background: #12081f;
            color: #9584b0;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.15s ease;
        }

        .seat:hover:not(.booked) {
            border-color: #a855f7;
            transform: translateY(-2px);
        }

        .seat.selected {
            background: #a855f7;
            border-color: #a855f7;
            color: #fff;
        }

        .seat.booked {
            background: #241636;
            border-color: #241636;
            color: #6b5c80;
            cursor: not-allowed;
        }

        /* ===== Legend ===== */
        .legend {
            display: flex;
            justify-content: center;
            gap: 26px;
            margin-bottom: 34px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: #c9b8dc;
        }

        .legend-box {
            width: 20px;
            height: 20px;
            border-radius: 5px;
            border: 1.5px solid #2a1a3d;
            background: #12081f;
        }

        .legend-box.selected { background: #a855f7; border-color: #a855f7; }
        .legend-box.booked { background: #241636; border-color: #241636; }

        /* ===== Summary bar ===== */
        .summary-bar {
            background: #12081f;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.5);
            padding: 22px 26px;
            max-width: 500px;
            margin: 0 auto;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #c9b8dc;
            margin-bottom: 10px;
        }

        .summary-row strong {
            color: #f3e8ff;
        }

        .selected-seats-text {
            font-size: 13px;
            color: #9584b0;
            margin-bottom: 16px;
            min-height: 18px;
        }

        /* ===== Payment method ===== */
        .payment-section {
            margin-bottom: 18px;
            padding-top: 14px;
            border-top: 1px solid #2a1a3d;
        }

        .payment-label {
            font-size: 13px;
            font-weight: 700;
            color: #d4c5e8;
            margin-bottom: 10px;
        }

        .payment-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #0d0518;
            border: 1.5px solid #2a1a3d;
            border-radius: 10px;
            padding: 12px 14px;
            cursor: pointer;
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .payment-option:has(input:checked) {
            border-color: #a855f7;
            background: rgba(168,85,247,0.08);
        }

        .payment-option input {
            accent-color: #a855f7;
            width: 16px;
            height: 16px;
        }

        .payment-option span {
            font-size: 14px;
            color: #e8def8;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .payment-option span i {
            color: #c084fc;
        }

        .confirm-btn {
            width: 100%;
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .confirm-btn:hover {
            background: linear-gradient(135deg, #9333ea, #581c87);
        }

        .confirm-btn:disabled {
            background: #2a1f38;
            color: #6b5c80;
            cursor: not-allowed;
        }

        @media (max-width: 500px) {
            .seat {
                width: 26px;
                height: 26px;
            }
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <div class="top-bar-inner">
            <a href="book_ticket.php" class="back-link"><i class="fa-solid fa-arrow-left"></i></a>
            <div>
                <h1><?php echo htmlspecialchars($movie["title"]); ?></h1>
                <div class="sub">
                    <?php echo htmlspecialchars($show["theatre_name"]); ?> &bull;
                    <?php echo date("D, d M Y", strtotime($show["show_date"])); ?> &bull;
                    <?php echo date("g:i A", strtotime($show["show_time"])); ?> &bull;
                    <?php echo htmlspecialchars($seatClass["class_name"]); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="page-container">

        <?php if($message){ ?>
            <div class="error-message"><?php echo $message; ?></div>
        <?php } ?>

        <div class="instructions">
            <p class="seats-needed">Please select <span><?php echo $totalSeatsNeeded; ?></span> seat(s)</p>
        </div>

        <div class="screen-wrap">
            <div class="screen"></div>
            <div class="screen-label">Screen this way</div>
        </div>

        <form action="confirm_booking.php" method="post" id="seatForm">
            <input type="hidden" name="selectedseats" id="selectedSeatsInput" value="">

            <div class="seat-map">
                <?php foreach($rows as $row){ ?>
                    <div class="seat-row">
                        <span class="row-label"><?php echo $row; ?></span>
                        <?php for($i = 1; $i <= $seatsPerRow; $i++){ ?>
                            <?php
                            $seatNumber = $row . $i;
                            $isBooked = in_array($seatNumber, $bookedSeats);
                            ?>
                            <div class="seat <?php echo $isBooked ? 'booked' : ''; ?>" data-seat="<?php echo $seatNumber; ?>">
                                <?php echo $i; ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

            <div class="legend">
                <div class="legend-item"><div class="legend-box"></div> Available</div>
                <div class="legend-item"><div class="legend-box selected"></div> Selected</div>
                <div class="legend-item"><div class="legend-box booked"></div> Booked</div>
            </div>

            <div class="summary-bar">
                <div class="summary-row">
                    <span>Seat Class</span>
                    <strong><?php echo htmlspecialchars($seatClass["class_name"]); ?></strong>
                </div>
                <div class="summary-row">
                    <span>Seats Required</span>
                    <strong><?php echo $totalSeatsNeeded; ?> (<?php echo $booking["adultseats"]; ?> Adult, <?php echo $booking["kidseats"]; ?> Kid)</strong>
                </div>
                <div class="selected-seats-text" id="selectedSeatsText">No seats selected yet</div>

                <!-- ===== Payment Method ===== -->
                <div class="payment-section">
                    <p class="payment-label">Payment Method</p>
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="paymentmethod" value="Cash" checked>
                            <span><i class="fa-solid fa-money-bill-wave"></i> Cash on Arrival</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="paymentmethod" value="Online">
                            <span><i class="fa-solid fa-credit-card"></i> Online Payment</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="confirm-btn" id="confirmBtn" disabled>Confirm Seats</button>
            </div>
        </form>

    </div>

    <script>
        const totalNeeded = <?php echo $totalSeatsNeeded; ?>;
        let selectedSeats = [];

        document.querySelectorAll(".seat:not(.booked)").forEach(function(seatEl){
            seatEl.addEventListener("click", function(){
                const seatNumber = this.getAttribute("data-seat");

                if(this.classList.contains("selected")){
                    // Deselect
                    this.classList.remove("selected");
                    selectedSeats = selectedSeats.filter(s => s !== seatNumber);
                }else{
                    // Select (agar limit poori na ho chuki ho)
                    if(selectedSeats.length < totalNeeded){
                        this.classList.add("selected");
                        selectedSeats.push(seatNumber);
                    }
                }

                updateSummary();
            });
        });

        function updateSummary(){
            const textEl = document.getElementById("selectedSeatsText");
            const btn = document.getElementById("confirmBtn");
            const hiddenInput = document.getElementById("selectedSeatsInput");

            if(selectedSeats.length === 0){
                textEl.textContent = "No seats selected yet";
            }else{
                textEl.textContent = "Selected: " + selectedSeats.join(", ");
            }

            hiddenInput.value = selectedSeats.join(",");

            btn.disabled = selectedSeats.length !== totalNeeded;
        }
    </script>

</body>
</html>