<?php


include_once "../connect.php";

$id = $_GET["id"];

$query = "DELETE FROM seat_classes WHERE id = $id";
$result = mysqli_query($conn,$query);

if($result){
    echo "<script>window.location.href = 'view_seatclasses.php'</script>";
}

?>

