<?php
session_start();

// ===== Using the same database connection you already have =====
include_once "../connect.php";

$message = "";

if(isset($_POST["sub"])){

    // Step 1: Form se values le rahe hain
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Step 2: Fields khali to nahi
    if($email == "" || $password == ""){
        $message = "Email aur password dono zaroori hain.";
    }

    // Step 3: Email ka format sahi hai ya nahi
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $message = "Sahi email address likhein.";
    }

    // Step 4: Password sirf spaces se na bana ho
    elseif(trim($password) == ""){
        $message = "Password sirf spaces se nahi ho sakta.";
    }

    // Step 5: Sab sahi hai, to admins table mein user dhoondo
    else{

        $safeEmail = mysqli_real_escape_string($conn, $email);

        $query = "SELECT * FROM admins WHERE email = '$safeEmail'";
        $result = mysqli_query($conn, $query);
        $admin = mysqli_fetch_assoc($result);

        // password_verify() typed password ko database ke hash se match karta hai
        if($admin && password_verify($password, $admin["password"])){
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_name"] = $admin["name"];

            echo "<script>window.location.href = 'admin.php'</script>";
            exit();
        }else{
            $message = "The email or password is incorrect.";
        }
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MovieBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
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

        /* ===== Admin Login Background (dark, grid, security-themed) ===== */
        body.auth-body {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow: hidden;
            background: radial-gradient(circle at 20% 20%, #2e1065 0%, #0d0518 55%, #030008 100%);
        }

        /* Moving grid lines in the background, gives a "control panel" feel */
        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(168,85,247,0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(168,85,247,0.08) 1px, transparent 1px);
            background-size: 42px 42px;
            animation: gridDrift 22s linear infinite;
            z-index: 0;
        }

        @keyframes gridDrift {
            from { background-position: 0 0; }
            to { background-position: 42px 42px; }
        }

        /* Glowing radial blobs */
        .glow-blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.35;
            z-index: 0;
        }

        .glow-1 {
            width: 340px;
            height: 340px;
            background: #a855f7;
            top: -80px;
            left: -80px;
            animation: floatBlob 9s ease-in-out infinite;
        }

        .glow-2 {
            width: 300px;
            height: 300px;
            background: #7c3aed;
            bottom: -100px;
            right: -100px;
            animation: floatBlob 11s ease-in-out infinite reverse;
        }

        @keyframes floatBlob {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, -20px); }
        }

        /* Small scanning line effect */
        .scan-line {
            position: fixed;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(168,85,247,0.65), transparent);
            animation: scanMove 4.5s ease-in-out infinite;
            z-index: 1;
        }

        @keyframes scanMove {
            0% { top: 0%; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(26px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== Login Card (glassmorphism) ===== */
        .auth-card {
            position: relative;
            z-index: 2;
            max-width: 400px;
            width: 100%;
            background: rgba(20, 16, 40, 0.65);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255,255,255,0.08);
            padding: 40px 36px;
            border-radius: 18px;
            box-shadow: 0 30px 70px rgba(0,0,0,0.5);
            animation: fadeInUp 0.7s ease backwards;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #9584b0;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 22px;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: #c084fc;
        }

        /* ===== Shield Icon ===== */
        .auth-icon {
            width: 62px;
            height: 62px;
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            margin: 0 auto 18px;
            box-shadow: 0 12px 28px rgba(168,85,247,0.45);
            animation: fadeInUp 0.6s ease 0.1s backwards, pulseGlow 3s ease-in-out infinite 1s;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 12px 28px rgba(168,85,247,0.45); }
            50% { box-shadow: 0 12px 40px rgba(168,85,247,0.75); }
        }

        /* ===== Logo ===== */
        .logo {
            text-align: center;
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 4px;
            animation: fadeInUp 0.6s ease 0.15s backwards;
        }

        .logo span {
            color: #c084fc;
        }

        .auth-card h2 {
            text-align: center;
            font-size: 15px;
            font-weight: 600;
            color: #c9b8dc;
            margin-bottom: 8px;
            animation: fadeInUp 0.6s ease 0.2s backwards;
        }

        .restricted-tag {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #e9d5ff;
            background: rgba(168,85,247,0.15);
            border: 1px solid rgba(168,85,247,0.35);
            padding: 7px 14px;
            border-radius: 20px;
            width: fit-content;
            margin: 0 auto 26px;
            animation: fadeInUp 0.6s ease 0.25s backwards;
        }

        .message {
            background-color: rgba(220,38,38,0.15);
            border: 1px solid rgba(220,38,38,0.35);
            color: #fca5a5;
            padding: 11px 15px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 9px;
            animation: fadeInUp 0.4s ease;
        }

        .form-group {
            margin-bottom: 18px;
            animation: fadeInUp 0.6s ease backwards;
        }

        .form-group:nth-of-type(1) { animation-delay: 0.3s; }
        .form-group:nth-of-type(2) { animation-delay: 0.36s; }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #c9b8dc;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i.field-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #9584b0;
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .input-wrap input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            background: rgba(168,85,247,0.06);
            border: 1.5px solid rgba(168,85,247,0.15);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            color: #fff;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .input-wrap input::placeholder {
            color: #9584b0;
        }

        .input-wrap input:focus {
            border-color: #a855f7;
            background: rgba(168,85,247,0.1);
            box-shadow: 0 0 0 4px rgba(168,85,247,0.18);
        }

        .input-wrap input:focus ~ i.field-icon,
        .input-wrap:focus-within i.field-icon {
            color: #c084fc;
        }

        .input-wrap .toggle-password {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            color: #9584b0;
            font-size: 13px;
            cursor: pointer;
            background: none;
            border: none;
            transition: color 0.2s ease;
        }

        .input-wrap .toggle-password:hover {
            color: #c084fc;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 14.5px;
            padding: 14px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: 0.25s ease;
            animation: fadeInUp 0.6s ease 0.42s backwards;
            margin-top: 8px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(168,85,247,0.5);
        }

        .footnote {
            text-align: center;
            font-size: 11.5px;
            color: #9584b0;
            margin-top: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            animation: fadeInUp 0.6s ease 0.48s backwards;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body class="auth-body">

    <div class="grid-overlay"></div>
    <span class="glow-blob glow-1"></span>
    <span class="glow-blob glow-2"></span>
    <div class="scan-line"></div>

    <div class="auth-card">
        <a href="../index.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Site</a>

        <div class="auth-icon"><i class="fa-solid fa-user-shield"></i></div>
        <div class="logo">Movie<span>Book</span></div>
        <h2>Admin Control Panel</h2>
        <div class="restricted-tag"><i class="fa-solid fa-lock"></i> Restricted Access</div>

        <?php if($message){ ?>
            <div class="message"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $message; ?></div>
        <?php } ?>

        <form action="admin_login.php" method="post">

            <div class="form-group">
                <label>Email</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope field-icon"></i>
                    <input type="email" name="email" placeholder="admin@moviebook.com" required>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock field-icon"></i>
                    <input type="password" name="password" id="passwordField" placeholder="Enter your password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="fa-solid fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="submit-btn" name="sub" value="1">
                <i class="fa-solid fa-right-to-bracket"></i> Login to Dashboard
            </button>

        </form>

        <p class="footnote"><i class="fa-solid fa-shield-halved"></i> This area is monitored and restricted to authorized personnel only.</p>
    </div>

    <script>
        function togglePassword() {
            const field = document.getElementById("passwordField");
            const icon = document.getElementById("toggleIcon");
            if (field.type === "password") {
                field.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                field.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>

</body>
</html>