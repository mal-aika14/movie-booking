<?php
session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: login.php");
    exit();
}

include_once "connect.php";



$userid = $_SESSION["user_id"];

$query = "SELECT * FROM users WHERE id = $userid";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$message = "";

if(isset($_POST["sub"])){

    $name = $_POST["name"];
    $email = $_POST["email"];

    $updatequery = "UPDATE users SET name='$name', email='$email' WHERE id=$userid";
    $updateresult = mysqli_query($conn, $updatequery);

    if($updateresult){
        $_SESSION["user_name"] = $name;
        $message = "Profile updated successfully!";
        // Naya data dobara load kar lete hain taake form update dikhe
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
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
    <title>My Profile - MovieBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        html, body {
            height: 100%;
        }

        body {
            position: relative;
            background-color: #060010;
            color: #e8def8;
            overflow-x: hidden;
        }

        a { text-decoration: none; }

        /* ===== Ambient background glow ===== */
        .bg-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.35;
            z-index: -1;
        }

        .glow-1 {
            width: 420px;
            height: 420px;
            background: #6b21a8;
            top: -120px;
            left: -100px;
        }

        .glow-2 {
            width: 380px;
            height: 380px;
            background: #a855f7;
            bottom: -140px;
            right: -100px;
        }

        .floating-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(2px);
            opacity: 0.5;
            animation: floatOrb 6s ease-in-out infinite;
            z-index: -1;
        }

        .orb-1 { width: 14px; height: 14px; background: #a855f7; top: 18%; left: 10%; animation-delay: 0s; }
        .orb-2 { width: 10px; height: 10px; background: #ffd700; top: 75%; left: 16%; animation-delay: 1.2s; }
        .orb-3 { width: 12px; height: 12px; background: #a855f7; top: 22%; right: 12%; animation-delay: 2.4s; }
        .orb-4 { width: 8px; height: 8px; background: #fff; top: 72%; right: 18%; animation-delay: 0.8s; }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-22px); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(28px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes ringPulse {
            0% { box-shadow: 0 0 0 0 rgba(168,85,247,0.45); }
            70% { box-shadow: 0 0 0 14px rgba(168,85,247,0); }
            100% { box-shadow: 0 0 0 0 rgba(168,85,247,0); }
        }

        /* ===== Navbar ===== */
        .navbar {
            background-color: #0d0518;
            border-bottom: 1px solid #2a1a3d;
            padding: 16px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
            animation: fadeInUp 0.5s ease backwards;
        }

        .logo {
            font-family: 'Bebas Neue', 'Segoe UI', Arial, sans-serif;
            font-size: 26px;
            font-weight: 400;
            letter-spacing: 1.2px;
            color: #f3e8ff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            color: #c084fc;
            animation: ringPulseGlow 2.8s ease-in-out infinite;
        }

        @keyframes ringPulseGlow {
            0%, 100% { text-shadow: 0 0 0 rgba(168,85,247,0); transform: scale(1); }
            50% { text-shadow: 0 0 12px rgba(168,85,247,0.6); transform: scale(1.08); }
        }

        .logo span {
            background: linear-gradient(90deg, #a855f7, #e9d5ff, #a855f7);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 3.5s linear infinite;
        }

        @keyframes shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        .back-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #9584b0;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .back-link:hover {
            color: #c084fc;
            transform: translateX(-3px);
        }

        /* ===== Page ===== */
        .page-container {
            position: relative;
            z-index: 2;
            max-width: 520px;
            margin: 0 auto;
            padding: 60px 24px;
        }

        .form-card {
            background: rgba(18,8,31,0.85);
            border: 1px solid #2a1a3d;
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 40px 36px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.5);
            animation: fadeInUp 0.6s ease 0.1s backwards;
        }

        .avatar-row {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }

        .avatar-circle {
            width: 82px;
            height: 82px;
            border-radius: 50%;
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 16px;
            animation: fadeInUp 0.6s ease 0.2s backwards, ringPulse 2.5s ease-in-out 1s infinite;
        }

        .form-card h1 {
            font-size: 21px;
            color: #f3e8ff;
            text-align: center;
            font-weight: 800;
            animation: fadeInUp 0.6s ease 0.25s backwards;
        }

        .form-card .subtext {
            text-align: center;
            font-size: 13px;
            color: #9584b0;
            margin-top: 6px;
            animation: fadeInUp 0.6s ease 0.3s backwards;
        }

        .message {
            background-color: rgba(52,211,153,0.12);
            border: 1px solid rgba(52,211,153,0.35);
            color: #34d399;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-top: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            animation: fadeInUp 0.4s ease;
        }

        form {
            margin-top: 26px;
        }

        .form-group {
            margin-bottom: 20px;
            animation: fadeInUp 0.6s ease backwards;
        }

        .form-group:nth-of-type(1) { animation-delay: 0.35s; }
        .form-group:nth-of-type(2) { animation-delay: 0.4s; }
        .form-group:nth-of-type(3) { animation-delay: 0.45s; }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #d4c5e8;
        }

        .form-group label i {
            color: #c084fc;
            font-size: 12px;
            width: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1.5px solid #2a1a3d;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            background: #1a0f2e;
            color: #e8def8;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #a855f7;
            box-shadow: 0 0 0 4px rgba(168,85,247,0.16);
        }

        .form-group input[type="submit"] {
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
            padding: 13px;
            border-radius: 10px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(168,85,247,0.4);
        }

        @media (max-width: 480px) {
            .navbar { padding: 14px 20px; }
            .page-container { padding: 40px 20px; }
            .form-card { padding: 30px 22px; }
        }
    </style>
</head>
<body>

    <span class="bg-glow glow-1"></span>
    <span class="bg-glow glow-2"></span>
    <span class="floating-orb orb-1"></span>
    <span class="floating-orb orb-2"></span>
    <span class="floating-orb orb-3"></span>
    <span class="floating-orb orb-4"></span>

    <header class="navbar">
        <div class="logo"><i class="fa-solid fa-clapperboard"></i>CINE<span>AURA</span></div>
        <a href="index.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
    </header>

    <div class="page-container">
        <div class="form-card">

            <div class="avatar-row">
                <div class="avatar-circle"><?php echo strtoupper(substr($user["name"], 0, 1)); ?></div>
                <h1>My Profile</h1>
                <p class="subtext">Manage your personal information</p>
            </div>

            <?php if($message){ ?>
                <div class="message"><i class="fa-solid fa-circle-check"></i> <?php echo $message; ?></div>
            <?php } ?>

            <form action="profile.php" method="post">
                <div class="form-group">
                    <label><i class="fa-solid fa-user"></i> Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user["name"]); ?>" required>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>" required>
                </div>

                <div class="form-group">
                    <input type="submit" value="Save Changes" name="sub">
                </div>
            </form>

        </div>
    </div>

</body>
</html>