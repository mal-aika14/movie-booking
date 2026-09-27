<?php
session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: admin_login.php");
    exit();
}

include_once "../connect.php";

$reviewId = 0;
if(isset($_GET["id"])){
    $reviewId = intval($_GET["id"]);
}

if($reviewId > 0){

    // Delete se pehle movie_id nikal lo, taake avg_rating update ho sake
    $findQuery = "SELECT movie_id FROM reviews WHERE id = $reviewId";
    $findResult = mysqli_query($conn, $findQuery);
    $reviewRow = mysqli_fetch_assoc($findResult);

    if($reviewRow){
        $movieId = $reviewRow["movie_id"];

        $deleteQuery = "DELETE FROM reviews WHERE id = $reviewId";
        mysqli_query($conn, $deleteQuery);

        // Baaki reviews ka naya average nikal ke movie ki avg_rating update karo
        $avgQuery = "SELECT AVG(rating) AS new_avg FROM reviews WHERE movie_id = $movieId";
        $avgResult = mysqli_query($conn, $avgQuery);
        $avgRow = mysqli_fetch_assoc($avgResult);
        $newAvg = $avgRow["new_avg"] ? round($avgRow["new_avg"], 1) : 0;

        mysqli_query($conn, "UPDATE movies SET avg_rating = $newAvg WHERE id = $movieId");
    }
}

header("Location: view_reviews.php");
exit();
?>