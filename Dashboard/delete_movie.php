<?php
// ===== Database connection =====
include_once "../connect.php";

$id = $_GET["id"];

$query = "DELETE FROM movies WHERE id = $id";
$result = mysqli_query($conn, $query);

if($result){
    echo "<script>window.location.href = 'admin.php'</script>";
}
?>