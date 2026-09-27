<?php
session_start();



// ===== Database connection =====
include_once "../connect.php";

$message = "";

if(isset($_POST["sub"])){

    $movieimgname = $_FILES["movieimage"]["name"];
    $moviepath = $_FILES["movieimage"]["tmp_name"];
    $ismoved = move_uploaded_file($moviepath, "../images/".$movieimgname);

    if($ismoved){
        $title = $_POST["title"];
        $genre = $_POST["genre"];
        $posterurl = "images/".$movieimgname;
        $trailerurl = $_POST["trailerurl"];
        $description = $_POST["description"];
        $director = $_POST["director"];
        $releaseyear = $_POST["releaseyear"];
        $castnames = $_POST["castnames"];

        // Checkbox tick hai to 1, warna 0
        $iscomingsoon = isset($_POST["comingsoon"]) ? 1 : 0;

        // Rating ab admin manually nahi deta, reviews se calculate hogi (0 se start)
        $rating = 0;

        $query = "INSERT INTO `movies`(`title`, `genre`, `poster_url`, `avg_rating`, `trailer_url`, `description`, `director`, `release_year`, `cast_names`, `is_coming_soon`) VALUES ('$title','$genre','$posterurl','$rating','$trailerurl','$description','$director','$releaseyear','$castnames','$iscomingsoon')";

        $result = mysqli_query($conn, $query);

        if($result){
            echo "<script>window.location.href = 'view_movies.php'</script>";
        }else{
            $message = "Something went wrong. Please try again.";
        }
    }else{
        $message = "The image could not be uploaded.";
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Movie - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

    <div class="form-card">
        <a href="view_movies.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Movies</a>

        <div class="form-header">
            <div class="icon-box"><i class="fa-solid fa-film"></i></div>
            <div>
                <h2>Add New Movie</h2>
                <p>Add a new movie with a poster</p>
            </div>
        </div>

        <?php if($message){ ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <form action="add_movie.php" method="post" enctype="multipart/form-data">

            <div class="form-group">
                <label><i class="fa-solid fa-heading"></i> Movie Title</label>
                <input type="text" name="title" placeholder="Enter movie title" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-tags"></i> Genre</label>
                <input type="text" name="genre" placeholder="e.g. Action, Thriller" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-image"></i> Poster Image</label>
                <input type="file" name="movieimage" required>
            </div>

            <div class="form-group">
                <label><i class="fa-brands fa-youtube"></i> Trailer Link (YouTube)</label>
                <input type="text" name="trailerurl" placeholder="e.g. https://www.youtube.com/watch?v=xxxxx">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-align-left"></i> Description</label>
                <textarea name="description" rows="3" placeholder="Write about the movie’s story." style="width:100%; padding:11px 14px; border:1px solid #e0e0e0; border-radius:8px; font-size:14px; font-family:inherit;"></textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-clapperboard"></i> Director</label>
                <input type="text" name="director" placeholder="e.g. Christopher Nolan">
            </div>

            <div class="form-group">
                <label><i class="fa-regular fa-calendar"></i> Release Year</label>
                <input type="number" name="releaseyear" placeholder="e.g. 2024">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-users"></i> Cast (separate with commas)</label>
                <input type="text" name="castnames" placeholder="e.g. Tom Cruise, Emma Stone">
            </div>

            <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="comingsoon" style="width:auto;">
                    <span><i class="fa-solid fa-clock"></i> This is a ‘Coming Soon’ movie (not released yet)</span>
                </label>
            </div>

            <div class="form-group">
                <input type="submit" value="Add Movie" name="sub">
            </div>

        </form>
    </div>

</body>
</html>