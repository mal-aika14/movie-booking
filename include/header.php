
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        /* ===== Logged-in user menu (name + dropdown) ===== */
        .user-menu {
            position: relative;
        }

        .user-menu-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f7f7fa;
            border: 1px solid #eee;
            padding: 8px 16px 8px 8px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
            transition: 0.2s ease;
            font-family: inherit;
        }

        .user-menu-toggle:hover {
            background: #f0f0f3;
        }

        .user-menu-toggle .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e94560, #c73650);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-menu-toggle i.fa-chevron-down {
            font-size: 11px;
            color: #999;
            transition: transform 0.2s ease;
        }

        .user-menu.open .user-menu-toggle i.fa-chevron-down {
            transform: rotate(180deg);
        }

        .user-menu-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
            min-width: 190px;
            padding: 8px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: 0.2s ease;
            z-index: 300;
        }

        .user-menu.open .user-menu-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-menu-dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13.5px;
            color: #333;
            font-weight: 500;
            transition: 0.15s ease;
        }

        .user-menu-dropdown a:hover {
            background: #f7f7fa;
            color: #e94560;
        }

        .user-menu-dropdown a i {
            width: 16px;
            color: #999;
        }

        .user-menu-dropdown a:hover i {
            color: #e94560;
        }

        .user-menu-dropdown .dropdown-divider {
            height: 1px;
            background: #f0f0f3;
            margin: 6px 4px;
        }
    </style>

</head>
<body>

   
    <!-- ===== Header / Navbar ===== -->
    <header class="navbar">
        <div class="logo"><i class="fa-solid fa-clapperboard"></i>Cine<span>Aura</span></div>

        <button class="nav-toggle" type="button" onclick="toggleNav()" aria-label="Toggle menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <nav class="nav-links" id="navLinks">
            <a href="index.php">Home</a>
            <a href="movies.php">Movies</a>
            <a href="theatres.php">Theatre</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
        </nav>
        <div class="nav-actions">
            <?php if(isset($_SESSION["user_id"])){ ?>
                <div class="user-menu">
                    <button class="user-menu-toggle" type="button" onclick="toggleUserMenu()">
                        <span class="user-avatar"><?php echo strtoupper(substr($_SESSION["user_name"], 0, 1)); ?></span>
                        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="user-menu-dropdown">
                        <a href="my_bookings.php"><i class="fa-solid fa-ticket"></i> My Bookings</a>
                        <a href="profile.php"><i class="fa-solid fa-user"></i> My Profile</a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
            <?php }else{ ?>
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-filled">Register</a>
            <?php } ?>
        </div>
    </header>


    <script>
        function toggleNav() {
            document.getElementById("navLinks").classList.toggle("active");
        }

        document.querySelectorAll(".nav-links a").forEach(function(link){
            link.addEventListener("click", function(){
                document.getElementById("navLinks").classList.remove("active");
            });
        });

        document.addEventListener("click", function(e){
            const navLinks = document.getElementById("navLinks");
            const navToggle = document.querySelector(".nav-toggle");
            if (navLinks && navLinks.classList.contains("active") &&
                !navLinks.contains(e.target) && !navToggle.contains(e.target)) {
                navLinks.classList.remove("active");
            }
        });
    </script>

    <script>
        function toggleUserMenu() {
            document.querySelector(".user-menu").classList.toggle("open");
        }
        document.addEventListener("click", function(e) {
            const menu = document.querySelector(".user-menu");
            if (menu && !menu.contains(e.target)) {
                menu.classList.remove("open");
            }
        });
    </script>