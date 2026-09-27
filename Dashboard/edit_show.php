<?php
session_start();


// ===== Database connection =====
include_once "../connect.php";

$id = $_GET["id"];

$query = "SELECT * FROM shows WHERE id = $id";
$result = mysqli_query($conn, $query);
$show = mysqli_fetch_assoc($result);

// Dropdown ke liye movies aur theatres nikal rahe hain
$movieQuery = "SELECT * FROM movies";
$movieResult = mysqli_query($conn, $movieQuery);

$theatreQuery = "SELECT * FROM theatres";
$theatreResult = mysqli_query($conn, $theatreQuery);

$message = "";

if(isset($_POST["sub"])){

    $movieid = $_POST["movieid"];
    $theatreid = $_POST["theatreid"];
    $showdate = $_POST["showdate"];
    $showtime = $_POST["showtime"];

    $updatequery = "UPDATE shows SET movie_id='$movieid', theatre_id='$theatreid', show_date='$showdate', show_time='$showtime' WHERE id=$id";
    $updateresult = mysqli_query($conn, $updatequery);

    if($updateresult){
        echo "<script>window.location.href = 'view_shows.php'</script>";
    }else{
        $message = "Something went wrong. Please try again.
";
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Show - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

    <div class="form-card">
        <a href="view_shows.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Shows</a>

        <div class="form-header">
            <div class="icon-box"><i class="fa-solid fa-pen-to-square"></i></div>
            <div>
                <h2>Edit Show</h2>
                <p>Update the show details.
</p>
            </div>
        </div>

        <?php if($message){ ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <form action="edit_show.php?id=<?php echo $id; ?>" method="post">

            <div class="form-group">
                <label><i class="fa-solid fa-film"></i> Select Movie</label>
                <select name="movieid" required>
                    <?php foreach($movieResult as $movie){ ?>
                        <option value="<?php echo $movie["id"]; ?>" <?php if($movie["id"] == $show["movie_id"]){ echo "selected"; } ?>>
                            <?php echo $movie["title"]; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-building"></i> Select Theatre</label>
                <select name="theatreid" required>
                    <?php foreach($theatreResult as $theatre){ ?>
                        <option value="<?php echo $theatre["id"]; ?>" <?php if($theatre["id"] == $show["theatre_id"]){ echo "selected"; } ?>>
                            <?php echo $theatre["name"]; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-regular fa-calendar"></i> Show Date</label>
                    <input type="date" name="showdate" value="<?php echo $show["show_date"]; ?>" required>
                </div>

                <div class="form-group">
                    <label><i class="fa-regular fa-clock"></i> Show Time</label>
                    <input type="time" name="showtime" value="<?php echo $show["show_time"]; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <input type="submit" value="Update Show" name="sub">
            </div>

        </form>
    </div>

</body>
</html>