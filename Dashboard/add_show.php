<?php
session_start();


// ===== Database connection =====
include_once "../connect.php";

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

    $query = "INSERT INTO `shows`(`movie_id`, `theatre_id`, `show_date`, `show_time`) VALUES ('$movieid','$theatreid','$showdate','$showtime')";
    $result = mysqli_query($conn, $query);

    if($result){
        echo "<script>window.location.href = 'view_shows.php'</script>";
    }else{
        $message = "Kuch masla ho gaya, dobara koshish karein.";
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Show - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

    <div class="form-card">
        <a href="view_shows.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Shows</a>

        <div class="form-header">
            <div class="icon-box"><i class="fa-solid fa-ticket"></i></div>
            <div>
                <h2>Add New Show</h2>
                <p>Select the movie, theatre, and showtime.
</p>
            </div>
        </div>

        <?php if($message){ ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <form action="add_show.php" method="post">

            <div class="form-group">
                <label><i class="fa-solid fa-film"></i> Select Movie</label>
                <select name="movieid" required>
                    <option value="" selected disabled>Choose a movie</option>
                    <?php foreach($movieResult as $movie){ ?>
                        <option value="<?php echo $movie["id"]; ?>"><?php echo $movie["title"]; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-building"></i> Select Theatre</label>
                <select name="theatreid" required>
                    <option value="" selected disabled>Choose a theatre</option>
                    <?php foreach($theatreResult as $theatre){ ?>
                        <option value="<?php echo $theatre["id"]; ?>"><?php echo $theatre["name"]; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-regular fa-calendar"></i> Show Date</label>
                    <input type="date" name="showdate" required>
                </div>

                <div class="form-group">
                    <label><i class="fa-regular fa-clock"></i> Show Time</label>
                    <input type="time" name="showtime" required>
                </div>
            </div>

            <div class="form-group">
                <input type="submit" value="Add Show" name="sub">
            </div>

        </form>
    </div>

</body>
</html>