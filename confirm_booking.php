<?php
session_start();

include_once "connect.php";

if(!isset($_SESSION["pending_booking"])){
    header("Location: book_ticket.php");
    exit();
}

$booking = $_SESSION["pending_booking"];



$selectedSeats = "";
if(isset($_POST["selectedseats"])){
    $selectedSeats = $_POST["selectedseats"];
}

$seatArray = array();
if($selectedSeats != ""){
    $seatArray = explode(",", $selectedSeats);
}

$totalSeatsNeeded = intval($booking["adultseats"]) + intval($booking["kidseats"]);

if(count($seatArray) != $totalSeatsNeeded){
    header("Location: select_seats.php?error=1");
    exit();
}

$showid = intval($booking["showid"]);



$paymentmethod = "Cash";
if(isset($_POST["paymentmethod"])){
    $paymentmethod = $_POST["paymentmethod"];
}
$paymentmethod = mysqli_real_escape_string($conn, $paymentmethod);



$bookedQuery = "SELECT seat_number FROM booked_seats WHERE show_id = $showid";
$bookedResult = mysqli_query($conn, $bookedQuery);

$alreadyBooked = array();
while($row = mysqli_fetch_assoc($bookedResult)){
    $alreadyBooked[] = $row["seat_number"];
}

$conflict = array_intersect($seatArray, $alreadyBooked);

if(count($conflict) > 0){
    header("Location: select_seats.php?error=1");
    exit();
}



$seatclassid = intval($booking["seatclassid"]);

$priceQuery = "SELECT adult_price, kid_price FROM seat_classes WHERE id = $seatclassid";
$priceResult = mysqli_query($conn, $priceQuery);
$priceRow = mysqli_fetch_assoc($priceResult);

$adultPrice = $priceRow["adult_price"];
$kidPrice = $priceRow["kid_price"];
$adultseats = intval($booking["adultseats"]);
$kidseats = intval($booking["kidseats"]);

$amount = ($adultPrice * $adultseats) + ($kidPrice * $kidseats);
$seats = $adultseats + $kidseats;



$userid = "NULL";
if(isset($_SESSION["user_id"])){
    $userid = intval($_SESSION["user_id"]);
}

$customername = $booking["customername"];
$phonenumber = $booking["phonenumber"];
$movieid = intval($booking["movieid"]);



$query = "INSERT INTO `bookings`(`user_id`, `customer_name`, `number`, `movie_id`, `show_id`, `seat_class_id`, `seats`, `amount`, `payment_method`) VALUES ($userid,'$customername','$phonenumber','$movieid','$showid','$seatclassid','$seats','$amount','$paymentmethod')";
$result = mysqli_query($conn, $query);

if($result){

    $newBookingId = mysqli_insert_id($conn);

    foreach($seatArray as $seatNumber){
        $safeSeat = mysqli_real_escape_string($conn, $seatNumber);
        mysqli_query($conn, "INSERT INTO booked_seats (show_id, booking_id, seat_number) VALUES ($showid, $newBookingId, '$safeSeat')");
    }

    unset($_SESSION["pending_booking"]);

    $_SESSION["last_booking_id"] = $newBookingId;

    header("Location: booking_success.php");
    exit();

}else{
    header("Location: select_seats.php?error=1");
    exit();
}
?>