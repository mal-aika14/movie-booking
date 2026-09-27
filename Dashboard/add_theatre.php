<?php
session_start();


include_once "../connect.php";

$message = "";

if(isset($_POST["sub"])){

    // Step 1: Get values from the form
    $name = trim($_POST["name"]);
    $location = trim($_POST["location"]);

    // Step 2: Check fields are not empty
    if($name == "" || $location == ""){
        $message = "Theatre name and location are both required.";
    }

    // Step 3: Name must be at least 3 characters long
    elseif(strlen($name) < 3){
        $message = "Theatre name must be at least 3 characters long.";
    }

    // Step 4: Name must contain at least one letter (not just numbers like "123")
    elseif(!preg_match("/[a-zA-Z]/", $name)){
        $message = "Theatre name cannot be only numbers.";
    }

    // Step 5: Everything is fine, save to the database
    else{

        $safeName = mysqli_real_escape_string($conn, $name);
        $safeLocation = mysqli_real_escape_string($conn, $location);

        $query = "INSERT INTO `theatres`(`name`, `location`) VALUES ('$safeName','$safeLocation')";
        $result = mysqli_query($conn, $query);

        if($result){
            echo "<script>window.location.href = 'view_theatres.php'</script>";
            exit();
        }else{
            $message = "Something went wrong, please try again.";
        }
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Theatre - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

    <div class="form-card">
        <a href="view_theatres.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Theatres</a>

        <div class="form-header">
            <div class="icon-box"><i class="fa-solid fa-building"></i></div>
            <div>
                <h2>Add New Theatre</h2>
                <p>Add a new theatre with its name and location.
</p>
            </div>
        </div>

        <?php if($message){ ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <form action="add_theatre.php" method="post">

            <div class="form-group">
                <label><i class="fa-solid fa-building"></i> Theatre Name</label>
                <input type="text" name="name" placeholder="e.g. Cinepax Cinema" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-location-dot"></i> Location</label>
                <input type="text" name="location" placeholder="e.g. DHA Phase 6, Karachi" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Add Theatre" name="sub">
            </div>

        </form>
    </div>

</body>
</html>