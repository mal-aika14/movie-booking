<?php
session_start();


// ===== Database connection =====
include_once "../connect.php";

$message = "";

if(isset($_POST["sub"])){

    // Step 1: Get values from the form
    $classname = trim($_POST["classname"]);
    $adultprice = trim($_POST["adultprice"]);
    $kidprice = trim($_POST["kidprice"]);

    // Step 2: Check fields are not empty
    if($classname == "" || $adultprice == "" || $kidprice == ""){
        $message = "All fields are required.";
    }

    // Step 3: Class name must contain at least one letter (not just numbers like "123")
    elseif(!preg_match("/[a-zA-Z]/", $classname)){
        $message = "Class name cannot be only numbers.";
    }

    // Step 4: Prices must be valid numbers
    elseif(!is_numeric($adultprice) || !is_numeric($kidprice)){
        $message = "Adult rate and kid rate must be numbers.";
    }

    // Step 5: Prices cannot be negative
    elseif($adultprice < 0 || $kidprice < 0){
        $message = "Prices cannot be negative.";
    }

    // Step 6: Everything is fine, save to the database
    else{

        $safeClassname = mysqli_real_escape_string($conn, $classname);
        $safeAdultprice = mysqli_real_escape_string($conn, $adultprice);
        $safeKidprice = mysqli_real_escape_string($conn, $kidprice);

        $query = "INSERT INTO `seat_classes`(`class_name`, `adult_price`, `kid_price`) VALUES ('$safeClassname','$safeAdultprice','$safeKidprice')";
        $result = mysqli_query($conn, $query);

        if($result){
            echo "<script>window.location.href = 'view_seatclasses.php'</script>";
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
    <title>Add Seat Class - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* ===== Black-Purple Theme: Centered form card ===== */
        body.form-page-body {
            background-color: #0d0a14;
            background-image:
                radial-gradient(circle at 15% 20%, rgba(155, 77, 255, 0.12), transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(114, 9, 183, 0.15), transparent 45%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .form-card {
            background: #16121f;
            border: 1px solid rgba(155, 77, 255, 0.18);
            border-radius: 16px;
            box-shadow: 0 10px 34px rgba(0,0,0,0.5), 0 0 40px rgba(114, 9, 183, 0.08);
            padding: 40px;
            max-width: 460px;
            width: 100%;
        }

        .form-card .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #8a839c;
            font-size: 14px;
            margin-bottom: 24px;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .form-card .back-link:hover {
            color: #b06aff;
        }

        .form-card .card-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 30px;
        }

        .form-card .header-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: linear-gradient(135deg, #9b4dff, #6a11cb);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 6px 18px rgba(155, 77, 255, 0.35);
        }

        .form-card .card-header h1 {
            font-size: 21px;
            color: #f2eefc;
            margin-bottom: 4px;
        }

        .form-card .card-header p {
            font-size: 13px;
            color: #8a839c;
        }

        .form-card .field-group {
            margin-bottom: 20px;
        }

        .form-card .field-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #e6e0f5;
            margin-bottom: 10px;
        }

        .form-card .field-group label i {
            color: #b06aff;
            font-size: 13px;
        }

        .form-card .field-group input {
            width: 100%;
            padding: 13px 16px;
            background: #0d0a14;
            border: 1px solid #2e2740;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            color: #f2eefc;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-card .field-group input::placeholder {
            color: #635b78;
        }

        .form-card .field-group input:focus {
            border-color: #9b4dff;
            box-shadow: 0 0 0 3px rgba(155, 77, 255, 0.18);
        }

        .form-card .error-message {
            background: rgba(233, 69, 96, 0.12);
            border: 1px solid rgba(233, 69, 96, 0.35);
            color: #ff8fa3;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .form-card .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #9b4dff, #6a11cb);
            color: #fff;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: filter 0.2s ease, transform 0.2s ease;
        }

        .form-card .submit-btn:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="form-page-body">

    <div class="form-card">
        <a href="view_seatclasses.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Seat Classes</a>

        <div class="card-header">
            <div class="header-icon"><i class="fa-solid fa-chair"></i></div>
            <div>
                <h1>Add New Seat Class</h1>
                <p>Set the class name and its rates.</p>
            </div>
        </div>

        <?php if($message){ ?>
            <div class="error-message"><?php echo $message; ?></div>
        <?php } ?>

        <form method="POST" action="add_seatclass.php">
            <div class="field-group">
                <label><i class="fa-solid fa-chair"></i> Class Name</label>
                <input type="text" name="classname" placeholder="e.g. Silver, Gold, Platinum" required>
            </div>

            <div class="field-group">
                <label><i class="fa-solid fa-user"></i> Adult Rate (Rs.)</label>
                <input type="number" step="0.01" min="0" name="adultprice" placeholder="e.g. 500" required>
            </div>

            <div class="field-group">
                <label><i class="fa-solid fa-child"></i> Kid Rate (Rs.)</label>
                <input type="number" step="0.01" min="0" name="kidprice" placeholder="e.g. 300" required>
            </div>

            <button type="submit" name="sub" value="1" class="submit-btn">Add Seat Class</button>
        </form>
    </div>

</body>
</html>