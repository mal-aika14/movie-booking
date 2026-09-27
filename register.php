<?php
session_start();

// ===== Database connection =====
include_once "connect.php";

// ===== Register ke baad login page pe kahan se aaya tha wahi bhej dena =====
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
$success = false;



if(isset($_POST["sub"])){

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];



    if($name == "" || $email == "" || $password == ""){
        $message = "All fields are required.";
    }

    elseif(!preg_match("/^[a-zA-Z ]+$/", $name)){
        $message = "The name should contain letters only; numbers and symbols are not allowed.";

    }

    elseif(strlen($name) < 3){
        $message = "The name must be at least 3 characters long.
";
    }

    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $message = "The email format is invalid.
";
    }

    elseif(trim($password) == ""){
        $message = "The password cannot consist only of spaces.
";
    }

    elseif(strpos($password, " ") !== false){
        $message = "Spaces are not allowed in the password.
";
    }

    elseif($password != $confirmPassword){
        $message = "Password and Confirm Password do not match.";
    }

    elseif(strlen($password) < 6){
        $message = "Password must be at least 6 characters long.";
    }

    else{

        $safeEmail = mysqli_real_escape_string($conn, $email);

        $checkQuery = "SELECT id FROM users WHERE email = '$safeEmail'";
        $checkResult = mysqli_query($conn, $checkQuery);

        $existingUser = mysqli_fetch_assoc($checkResult);

        if($existingUser){
            $message = "An account with this email already exists. Please log in.";
        }
        else{
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $safeName = mysqli_real_escape_string($conn, $name);

            $insertQuery = "INSERT INTO users (name, email, password) VALUES ('$safeName', '$safeEmail', '$hashedPassword')";
            $insertResult = mysqli_query($conn, $insertQuery);

            if($insertResult){
                $success = true;
                $message = "Account created successfully! You can now log in.";
            }
            else{
                $message = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MovieBook</title>
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

        /* ===== Background (pure HTML/CSS, cinema photo + overlay) ===== */
        body {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 70px 24px 40px;
        }

        .auth-bg {
            position: fixed;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1595769816263-9b910be24d5f?fm=jpg&q=75&w=1800&auto=format&fit=crop');
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

        .orb-1 { width: 16px; height: 16px; background: #a855f7; top: 15%; left: 12%; animation-delay: 0s; }
        .orb-2 { width: 11px; height: 11px; background: #ffd700; top: 72%; left: 18%; animation-delay: 1.2s; }
        .orb-3 { width: 13px; height: 13px; background: #c084fc; top: 20%; right: 14%; animation-delay: 2.4s; }
        .orb-4 { width: 9px; height: 9px; background: #fff; top: 70%; right: 20%; animation-delay: 0.8s; }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-22px); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== Auth Card ===== */
        .auth-card {
            position: relative;
            max-width: 440px;
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #9584b0;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 22px;
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

        .auth-card .logo i { color: #c084fc; }
        .auth-card .logo span { color: #c084fc; }

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
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: fadeInUp 0.4s ease;
        }

        .message.error {
            background-color: #3a1420;
            border: 1px solid #5a2030;
            color: #fca5a5;
        }

        .message.success {
            background-color: #10331f;
            border: 1px solid #1e5c3a;
            color: #4ade80;
        }

        .message i { font-size: 15px; }

        .form-group {
            margin-bottom: 18px;
            animation: fadeInUp 0.6s ease backwards;
        }

        .form-group:nth-of-type(1) { animation-delay: 0.28s; }
        .form-group:nth-of-type(2) { animation-delay: 0.34s; }
        .form-group:nth-of-type(3) { animation-delay: 0.4s; }
        .form-group:nth-of-type(4) { animation-delay: 0.46s; }

        .input-wrap { position: relative; }

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

        .input-wrap .toggle-password:hover { color: #c084fc; }

        .password-hint {
            font-size: 11.5px;
            color: #9584b0;
            margin-top: 6px;
            padding-left: 4px;
        }

        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 22px;
            font-size: 12.5px;
            color: #c9b8dc;
            animation: fadeInUp 0.6s ease 0.5s backwards;
        }

        .terms-row input { margin-top: 3px; }

        .terms-row a {
            color: #c084fc;
            font-weight: 600;
            text-decoration: none;
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
            animation: fadeInUp 0.6s ease 0.55s backwards;
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
            animation: fadeInUp 0.6s ease 0.6s backwards;
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
            animation: fadeInUp 0.6s ease 0.65s backwards;
        }

        .switch-link a {
            color: #c084fc;
            font-weight: 700;
            text-decoration: none;
        }

        .switch-link a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .auth-card { padding: 34px 24px; }
        }
    </style>
</head>
<body>

    <!-- ===== Cinema background (HTML/CSS, no PHP) ===== -->
    <div class="auth-bg"></div>
    <div class="auth-overlay"></div>
    <span class="floating-orb orb-1"></span>
    <span class="floating-orb orb-2"></span>
    <span class="floating-orb orb-3"></span>
    <span class="floating-orb orb-4"></span>

    <div class="auth-card">
        <a href="index.php" class="back-home"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>

        <div class="logo"><i class="fa-solid fa-clapperboard"></i>Movie<span>Book</span></div>
        <p class="tagline">Every Movie, One Place</p>

        <h2>Create Your Account</h2>
        <p class="subheading">Sign up to start booking your favorite movies</p>

        <?php if($message){ ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <i class="fa-solid <?php echo $success ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <?php if(!$success){ ?>
        <form action="register.php" method="post" id="registerForm">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTo); ?>">

            <div class="form-group">
                <div class="input-wrap">
                    <i class="fa-solid fa-user field-icon"></i>
                    <input type="text" name="name" placeholder="Full name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                </div>
                <p class="password-hint" id="nameError" style="display:none; color:#fca5a5;">The name should contain letters only.
</p>
            </div>

            <div class="form-group">
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope field-icon"></i>
                    <input type="email" name="email" placeholder="Email address" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrap">
                    <i class="fa-solid fa-lock field-icon"></i>
                    <input type="password" name="password" id="passwordField" placeholder="Password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('passwordField','toggleIcon1')">
                        <i class="fa-solid fa-eye" id="toggleIcon1"></i>
                    </button>
                </div>
                <p class="password-hint">Must be at least 6 characters, no spaces</p>
                <p class="password-hint" id="passwordError" style="display:none; color:#fca5a5;">Spaces are not allowed in the password
.</p>
            </div>

            <div class="form-group">
                <div class="input-wrap">
                    <i class="fa-solid fa-lock field-icon"></i>
                    <input type="password" name="confirm_password" id="confirmField" placeholder="Confirm password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('confirmField','toggleIcon2')">
                        <i class="fa-solid fa-eye" id="toggleIcon2"></i>
                    </button>
                </div>
            </div>

            <div class="terms-row">
                <input type="checkbox" required>
                <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
            </div>

            <button type="submit" class="submit-btn" name="sub" value="1">
                <i class="fa-solid fa-user-plus"></i> Create Account
            </button>

        </form>
        <?php } ?>

        <div class="divider">OR</div>

        <p class="switch-link">Already have an account? <a href="login.php?redirect=<?php echo urlencode($redirectTo); ?>">Login here</a></p>
    </div>

    <script>
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
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
        var registerForm = document.getElementById("registerForm");

        registerForm.onsubmit = function(e){

            var nameValue = registerForm.name.value.trim();
            var passwordValue = registerForm.password.value;

            var nameError = document.getElementById("nameError");
            var passwordError = document.getElementById("passwordError");

            var isOk = true;

            // Name check: sirf letters aur space allow hain
            var nameRegex = /^[a-zA-Z ]+$/;
            if(nameRegex.test(nameValue) == false){
                nameError.style.display = "block";
                isOk = false;
            }else{
                nameError.style.display = "none";
            }

            // Password check: space nahi honi chahiye
            if(passwordValue.indexOf(" ") !== -1){
                passwordError.style.display = "block";
                isOk = false;
            }else{
                passwordError.style.display = "none";
            }

            if(isOk == false){
                e.preventDefault();
            }
        };
    </script>

</body>
</html>