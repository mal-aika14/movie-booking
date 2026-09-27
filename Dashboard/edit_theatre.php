<?php
session_start();


// ===== Database connection =====
include_once "../connect.php";

// Step 1: We now get the id from POST instead of the URL (GET)
// If no id was sent, there is nothing to edit, so go back to the list
if(!isset($_POST["id"])){
    header("Location: view_theatres.php");
    exit();
}

$id = (int)$_POST["id"];

$query = "SELECT * FROM theatres WHERE id = $id";
$result = mysqli_query($conn, $query);
$theatre = mysqli_fetch_assoc($result);

// If no theatre was found with this id, go back to the list
if(!$theatre){
    header("Location: view_theatres.php");
    exit();
}

$message = "";

if(isset($_POST["sub"])){

    // Step 2: Get values from the form
    $name = trim($_POST["name"]);
    $location = trim($_POST["location"]);

    // Step 3: Check fields are not empty
    if($name == "" || $location == ""){
        $message = "Theatre name and location are both required.";
    }

    // Step 4: Name must be at least 3 characters long
    elseif(strlen($name) < 3){
        $message = "Theatre name must be at least 3 characters long.";
    }

    // Step 5: Name must contain at least one letter (not just numbers like "123")
    elseif(!preg_match("/[a-zA-Z]/", $name)){
        $message = "Theatre name cannot be only numbers.";
    }

    // Step 6: Everything is fine, update the database
    else{

        $safeName = mysqli_real_escape_string($conn, $name);
        $safeLocation = mysqli_real_escape_string($conn, $location);

        $updatequery = "UPDATE theatres SET name='$safeName', location='$safeLocation' WHERE id=$id";
        $updateresult = mysqli_query($conn, $updatequery);

        if($updateresult){
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
    <title>Edit Theatre - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

    <div class="form-card">
        <a href="view_theatres.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Theatres</a>

        <div class="form-header">
            <div class="icon-box"><i class="fa-solid fa-pen-to-square"></i></div>
            <div>
                <h2>Edit Theatre</h2>
                <p>Update the theatre details.
</p>
            </div>
        </div>

        <?php if($message){ ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <form action="edit_theatre.php" method="post">

            <!-- Step 7: id is now sent as a hidden field instead of in the URL -->
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="form-group">
                <label><i class="fa-solid fa-building"></i> Theatre Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($theatre["name"]); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-location-dot"></i> Location</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($theatre["location"]); ?>" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Update Theatre" name="sub">
            </div>

        </form>
    </div>

</body>
</html>