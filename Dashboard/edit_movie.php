<?php
session_start();



include_once "../connect.php";

$id = $_GET["id"];

$query = "SELECT * FROM movies WHERE id = $id";
$result = mysqli_query($conn, $query);
$movie = mysqli_fetch_assoc($result);

$message = "";

if(isset($_POST["sub"])){

    $title = $_POST["title"];
    $genre = $_POST["genre"];
    $rating = $_POST["rating"];
    $posterurl = $movie["poster_url"]; // default: purani image hi rahegi
    $trailerurl = $_POST["trailerurl"];
    $description = $_POST["description"];
    $director = $_POST["director"];
    $releaseyear = $_POST["releaseyear"];
    $castnames = $_POST["castnames"];
    $iscomingsoon = isset($_POST["comingsoon"]) ? 1 : 0;

    // Agar admin ne nayi image select ki hai to usay update karo
    if($_FILES["movieimage"]["name"] != ""){
        $movieimgname = $_FILES["movieimage"]["name"];
        $moviepath = $_FILES["movieimage"]["tmp_name"];
        move_uploaded_file($moviepath, "../images/".$movieimgname);
        $posterurl = "images/".$movieimgname;
    }

    $updatequery = "UPDATE movies SET title='$title', genre='$genre', poster_url='$posterurl', avg_rating='$rating', trailer_url='$trailerurl', description='$description', director='$director', release_year='$releaseyear', cast_names='$castnames', is_coming_soon='$iscomingsoon' WHERE id=$id";
    $updateresult = mysqli_query($conn, $updatequery);

    if($updateresult){
        echo "<script>window.location.href = 'view_movies.php'</script>";
    }else{
        $message = "Something went wrong. Please try again.";
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

    <div class="form-card">
        <a href="view_movies.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Movies</a>

        <div class="form-header">
            <div class="icon-box"><i class="fa-solid fa-pen-to-square"></i></div>
            <div>
                <h2>Edit Movie</h2>
                <p>Update the movie details.</p>
            </div>
        </div>

        <?php if($message){ ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <div class="current-poster">
            <img src="../<?php echo $movie["poster_url"]; ?>" alt="">
            <span>Current Poster</span>
        </div>

        <form action="edit_movie.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">

            <div class="form-group">
                <label><i class="fa-solid fa-heading"></i> Movie Title</label>
                <input type="text" name="title" value="<?php echo $movie["title"]; ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-tags"></i> Genre</label>
                <input type="text" name="genre" value="<?php echo $movie["genre"]; ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-star"></i> Rating (out of 5)</label>
                <input type="number" step="0.1" min="0" max="5" name="rating" value="<?php echo $movie["avg_rating"]; ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-image"></i>Poster Image (select only if you want to change it)</label>
                <input type="file" name="movieimage">
            </div>

            <div class="form-group">
                <label><i class="fa-brands fa-youtube"></i> Trailer Link (YouTube)</label>
                <input type="text" name="trailerurl" value="<?php echo $movie["trailer_url"]; ?>" placeholder="e.g. https://www.youtube.com/watch?v=xxxxx">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-align-left"></i> Description</label>
                <textarea name="description" rows="3" style="width:100%; padding:11px 14px; border:1px solid #e0e0e0; border-radius:8px; font-size:14px; font-family:inherit;"><?php echo $movie["description"]; ?></textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-clapperboard"></i> Director</label>
                <input type="text" name="director" value="<?php echo $movie["director"]; ?>">
            </div>

            <div class="form-group">
                <label><i class="fa-regular fa-calendar"></i> Release Year</label>
                <input type="number" name="releaseyear" value="<?php echo $movie["release_year"]; ?>">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-users"></i>Cast (separate with commas)</label>
                <input type="text" name="castnames" value="<?php echo $movie["cast_names"]; ?>">
            </div>

            <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="comingsoon" style="width:auto;" <?php if($movie["is_coming_soon"] == 1){ echo "checked"; } ?>>
                    <span><i class="fa-solid fa-clock"></i>This is a ‘Coming Soon’ movie (not released yet)</span>
                </label>
            </div>

            <div class="form-group">
                <input type="submit" value="Update Movie" name="sub">
            </div>

        </form>
    </div>

</body>
</html>