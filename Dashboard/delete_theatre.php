<?php
// ===== Database connection =====
include_once "../connect.php";

$id = $_POST["id"];

$query = "DELETE FROM theatres WHERE id = $id";
$result = mysqli_query($conn, $query);

if($result){
    echo "<script>window.location.href = 'view_theatres.php'</script>";
}
?>