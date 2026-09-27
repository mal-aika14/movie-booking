<?php
session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: login.php");
    exit();
}

include_once "connect.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$userid = $_SESSION["user_id"];

$query = "SELECT * FROM bookings WHERE id = $id AND user_id = $userid";
$result = mysqli_query($conn, $query);
$booking = mysqli_fetch_assoc($result);

if($booking){
    mysqli_query($conn, "DELETE FROM booked_seats WHERE booking_id = $id");

    mysqli_query($conn, "DELETE FROM bookings WHERE id = $id");
}

header("Location: my_bookings.php");
exit();
?>