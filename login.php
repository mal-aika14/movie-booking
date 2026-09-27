<?php
session_start();
include_once "connect.php";

$redirectTo = "index.php";
if(isset($_POST["redirect"]) && $_POST["redirect"] != ""){
    $redirectTo = $_POST["redirect"];
}elseif(isset($_GET["redirect"]) && $_GET["redirect"] != ""){
    $redirectTo = $_GET["redirect"];
}
if(!preg_match('/^[a-zA-Z0-9_\-]+\.php$/', $redirectTo)){
    $redirectTo = "index.php";
}

$message = "";

if(isset($_POST["sub"])){

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if($email == "" || $password == ""){
        $message = "Email aur password dono zaroori hain.";
    }

    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $message = "Sahi email address likhein.";
    }

    elseif(trim($password) == ""){
        $message = "Password sirf spaces se nahi ho sakta.";
    }

    else{

        $safeEmail = mysqli_real_escape_string($conn, $email);

        $query = "SELECT * FROM users WHERE email = '$safeEmail'";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);

        if($user && password_verify($password, $user["password"])){
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];

            echo "<script>window.location.href = '" . htmlspecialchars($redirectTo) . "'</script>";
            exit();
        }
        else{
            $message = "Email ya password ghalat hai.";
        }
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MovieBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 24px;
        }

        .auth-bg {
            position: fixed;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?fm=jpg&q=75&w=1800&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            transform: scale(1.08);
            animation: slowZoom 20s ease-in-out infinite alternate;
            z-index: -2;
        }

        .auth-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.88), rgba(46,16,101,0.82));
            z-index: -1;
        }

        @keyframes slowZoom {
            from { transform: scale(1.08); }
            to { transform: scale(1); }
        }

        .floating-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(2px);
            opacity: 0.55;
            animation: floatOrb 6s ease-in-out infinite;
            z-index: -1;
        }

        .orb-1 { width: 16px; height: 16px; background: #a855f7; top: 18%; left: 12%; animation-delay: 0s; }
        .orb-2 { width: 11px; height: 11px; background: #ffd700; top: 70%; left: 18%; animation-delay: 1.2s; }
        .orb-3 { width: 13px; height: 13px; background: #c084fc; top: 25%; right: 14%; animation-delay: 2.4s; }
        .orb-4 { width: 9px; height: 9px; background: #fff; top: 68%; right: 20%; animation-delay: 0.8s; }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-22px); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-card {
            position: relative;
            max-width: 420px;
            width: 100%;
            background: rgba(18,8,31,0.97);
            backdrop-filter: blur(10px);
            padding: 42px 38px;
            border-radius: 20px;
            border: 1px solid rgba(168,85,247,0.2);
            box-shadow: 0 30px 70px rgba(0,0,0,0.6);
            animation: fadeInUp 0.7s ease backwards;
        }

        .auth-card .back-home {
            position: absolute;
            top: -46px;
            left: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .auth-card .back-home:hover {
            color: #c084fc;
        }

        .auth-card .logo {
            text-align: center;
            font-size: 26px;
            font-weight: 800;
            color: #f3e8ff;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            animation: fadeInUp 0.6s ease 0.1s backwards;
        }

        .auth-card .logo i {
            color: #c084fc;
        }

        .auth-card .logo span {
            color: #c084fc;
        }

        .auth-card .tagline {
            text-align: center;
            font-size: 13px;
            color: #9584b0;
            margin-bottom: 28px;
            animation: fadeInUp 0.6s ease 0.15s backwards;
        }

        .auth-card h2 {
            margin-bottom: 4px;
            color: #f3e8ff;
            font-size: 22px;
            font-weight: 800;
            text-align: center;
            animation: fadeInUp 0.6s ease 0.2s backwards;
        }

        .auth-card .subheading {
            text-align: center;
            font-size: 13.5px;
            color: #9584b0;
            margin-bottom: 26px;
            animation: fadeInUp 0.6s ease 0.25s backwards;
        }

        .message {
            background-color: #3a1420;
            border: 1px solid #5a2030;
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: fadeInUp 0.4s ease;
        }

        .message i {
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 18px;
            animation: fadeInUp 0.6s ease backwards;
        }

        .form-group:nth-of-type(1) { animation-delay: 0.3s; }
        .form-group:nth-of-type(2) { animation-delay: 0.38s; }

        .input-wrap {
            position: relative;
        }

        .input-wrap i.field-icon {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            color: #9584b0;
            font-size: 15px;
            transition: color 0.2s ease;
        }

        .input-wrap input {
            width: 100%;
            padding: 13px 16px 13px 44px;
            border: 1.5px solid #2a1a3d;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            background: #0d0518;
            color: #e8def8;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-wrap input:focus {
            border-color: #a855f7;
            box-shadow: 0 0 0 4px rgba(168,85,247,0.18);
        }

        .input-wrap input:focus ~ i.field-icon,
        .input-wrap:focus-within i.field-icon {
            color: #c084fc;
        }

        .input-wrap .toggle-password {
            position: absolute;
            top: 50%;
            right: 16px;
            transform: translateY(-50%);
            color: #9584b0;
            font-size: 14px;
            cursor: pointer;
            transition: color 0.2s ease;
            background: none;
            border: none;
        }

        .input-wrap .toggle-password:hover {
            color: #c084fc;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            font-size: 13px;
            animation: fadeInUp 0.6s ease 0.42s backwards;
        }

        .form-options label {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #c9b8dc;
            cursor: pointer;
        }

        .form-options a {
            color: #c084fc;
            font-weight: 600;
            text-decoration: none;
        }

        .form-options a:hover {
            text-decoration: underline;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
            padding: 14px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: 0.25s ease;
            animation: fadeInUp 0.6s ease 0.48s backwards;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #9333ea, #581c87);
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(168,85,247,0.45);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 26px 0;
            color: #9584b0;
            font-size: 12px;
            animation: fadeInUp 0.6s ease 0.5s backwards;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #2a1a3d;
        }

        .switch-link {
            text-align: center;
            font-size: 14px;
            color: #c9b8dc;
            animation: fadeInUp 0.6s ease 0.55s backwards;
        }

        .switch-link a {
            color: #c084fc;
            font-weight: 700;
            text-decoration: none;
        }

        .switch-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 34px 24px;
            }
            .auth-card .back-home {
                top: -40px;
            }
        }
    </style>
</head>
<body>

    <div class="auth-bg"></div>
    <div class="auth-overlay"></div>
    <span class="floating-orb orb-1"></span>
    <span class="floating-orb orb-2"></span>
    <span class="floating-orb orb-3"></span>
    <span class="floating-orb orb-4"></span>

    <div class="auth-card">
        

        <div class="logo"><i class="fa-solid fa-clapperboard"></i>Movie<span>Book</span></div>
        <p class="tagline">Every Movie, One Place</p>

        <h2>Welcome Back</h2>
        <p class="subheading">Login to continue booking your favorite movies</p>

        <?php if($message){ ?>
            <div class="message"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $message; ?></div>
        <?php } ?>

        <form action="login.php" method="post" id="loginForm">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTo); ?>">

            <div class="form-group">
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope field-icon"></i>
                    <input type="email" name="email" placeholder="Email address" required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrap">
                    <i class="fa-solid fa-lock field-icon"></i>
                    <input type="password" name="password" id="passwordField" placeholder="Password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="fa-solid fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-options">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="submit-btn" name="sub" value="1">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </button>

        </form>

        <div class="divider">OR</div>

        <p class="switch-link">Don't have an account? <a href="register.php?redirect=<?php echo urlencode($redirectTo); ?>">Register here</a></p>
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

        // ===== Simple frontend validation (JS) =====
        var loginForm = document.getElementById("loginForm");

        loginForm.onsubmit = function(e){

            var passwordValue = loginForm.password.value;
            var isOk = true;

            // Password sirf spaces se na bana ho
            if(passwordValue.trim() == ""){
                isOk = false;
                alert("Password sirf spaces se nahi ho sakta.");
            }

            if(isOk == false){
                e.preventDefault();
            }
        };
    </script>

</body>
</html>