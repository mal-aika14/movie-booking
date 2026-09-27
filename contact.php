<?php
session_start();

include_once "include/header.php";
include_once "connect.php";

$formSubmitted = false;
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if($name != "" && $email != "" && $message != ""){

        // Message ko "messages" table mein save kar rahe hain, taake admin
        // panel se dekha ja sake
        $safeName = mysqli_real_escape_string($conn, $name);
        $safeEmail = mysqli_real_escape_string($conn, $email);
        $safeSubject = mysqli_real_escape_string($conn, $subject);
        $safeMessage = mysqli_real_escape_string($conn, $message);

        mysqli_query($conn, "INSERT INTO messages (name, email, subject, message) VALUES ('$safeName','$safeEmail','$safeSubject','$safeMessage')");

        $formSubmitted = true;
    }
}

// Location ka text jo map mein search karne ke liye use hoga
$officeLocation = "DHA Phase 8, Karachi, Pakistan";
$mapQuery = urlencode($officeLocation);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - MovieBook</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* ===== Reset ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background-color: #060010;
            color: #e8def8;
            line-height: 1.5;
            overflow-x: hidden;
        }

        a { text-decoration: none; }

        

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(28px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slowZoom {
            from { transform: scale(1.08); }
            to { transform: scale(1); }
        }

        @keyframes floatOrb {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-22px); }
        }

        /* ===== Hero ===== */
        .contact-hero {
            position: relative;
            height: 340px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }

        .contact-hero .hero-bg {
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1514306191717-452ec28c7814?fm=jpg&q=75&w=1800&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            transform: scale(1.08);
            animation: slowZoom 18s ease-in-out infinite alternate;
        }

        .contact-hero .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.93), rgba(46,16,101,0.88));
        }

        .contact-hero .floating-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(2px);
            opacity: 0.5;
            animation: floatOrb 6s ease-in-out infinite;
        }

        .orb-1 { width: 16px; height: 16px; background: #a855f7; top: 28%; left: 15%; animation-delay: 0s; }
        .orb-2 { width: 11px; height: 11px; background: #ffd700; top: 65%; left: 24%; animation-delay: 1.2s; }
        .orb-3 { width: 13px; height: 13px; background: #a855f7; top: 32%; right: 17%; animation-delay: 2.4s; }
        .orb-4 { width: 9px; height: 9px; background: #fff; top: 62%; right: 26%; animation-delay: 0.8s; }

        .contact-hero .hero-content {
            position: relative;
            z-index: 2;
            color: #fff;
            padding: 0 24px;
        }

        .contact-hero .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(168,85,247,0.18);
            border: 1px solid rgba(168,85,247,0.5);
            color: #d8b4fe;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 7px 18px;
            border-radius: 30px;
            margin-bottom: 20px;
            animation: fadeInUp 0.7s ease 0.05s backwards;
        }

        .contact-hero h1 {
            font-size: 40px;
            margin-bottom: 14px;
            animation: fadeInUp 0.7s ease 0.15s backwards;
        }

        .contact-hero p {
            color: #cfc0e6;
            font-size: 15px;
            max-width: 520px;
            margin: 0 auto;
            animation: fadeInUp 0.7s ease 0.25s backwards;
        }

        /* ===== Contact info cards (overlapping hero) ===== */
        .info-cards-section {
            max-width: 1300px;
            margin: -50px auto 0;
            padding: 0 24px;
            position: relative;
            z-index: 3;
        }
        .user-menu {
    position: relative;
}

.user-menu-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #1a0f2e;
    border: 1px solid #2a1a3d;
    padding: 8px 16px 8px 8px;
    border-radius: 30px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: #e8def8;
    transition: 0.2s ease;
}

.user-menu-toggle:hover {
    background: #241636;
}

.user-menu-toggle .user-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #9333ea, #6b21a8);
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
    color: #9584b0;
    transition: transform 0.2s ease;
}

.user-menu.open .user-menu-toggle i.fa-chevron-down {
    transform: rotate(180deg);
}

.user-menu-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    background: #12081f;
    border-radius: 12px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.65);
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
    color: #d4c5e8;
    font-weight: 500;
    transition: 0.15s ease;
}

.user-menu-dropdown a:hover {
    background: #241636;
    color: #c084fc;
}

.user-menu-dropdown a i {
    width: 16px;
    color: #9584b0;
}

.user-menu-dropdown a:hover i {
    color: #a855f7;
}

.user-menu-dropdown .dropdown-divider {
    height: 1px;
    background: #2a1a3d;
    margin: 6px 4px;
}

        .info-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 22px;
        }

        .info-card {
            background: #12081f;
            border: 1px solid #2a1a3d;
            border-radius: 16px;
            padding: 26px 22px;
            text-align: center;
            box-shadow: 0 16px 34px rgba(0,0,0,0.5);
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .info-card:nth-child(1) { animation-delay: 0.05s; }
        .info-card:nth-child(2) { animation-delay: 0.15s; }
        .info-card:nth-child(3) { animation-delay: 0.25s; }
        .info-card:nth-child(4) { animation-delay: 0.35s; }

        .info-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 22px 44px rgba(168,85,247,0.28);
        }

        .info-card .info-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: linear-gradient(135deg, #a855f7, #6b21a8);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin: 0 auto 16px;
        }

        .info-card h3 {
            font-size: 15px;
            color: #f3e8ff;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .info-card p, .info-card a {
            font-size: 13px;
            color: #b8a8d4;
            display: block;
        }

        .info-card a:hover {
            color: #c084fc;
        }

        /* ===== Contact main section ===== */
        .contact-main {
            max-width: 1300px;
            margin: 0 auto;
            padding: 70px 24px;
            display: flex;
            gap: 50px;
            align-items: flex-start;
        }

        .contact-form-wrap {
            flex: 1.3;
            background: #12081f;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            border: 1px solid #2a1a3d;
        }

        .contact-form-wrap .eyebrow {
            font-size: 13px;
            font-weight: 700;
            color: #c084fc;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .contact-form-wrap .accent-bar {
            width: 5px;
            height: 20px;
            background: linear-gradient(180deg, #a855f7, #6b21a8);
            border-radius: 4px;
        }

        .contact-form-wrap h2 {
            font-size: 26px;
            color: #f3e8ff;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .contact-form-wrap > p {
            color: #b8a8d4;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .success-banner {
            background: rgba(52,211,153,0.1);
            border: 1px solid rgba(52,211,153,0.4);
            color: #34d399;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 26px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
            animation: fadeInUp 0.5s ease;
        }

        .success-banner i {
            font-size: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #d4c5e8;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            border: 1px solid #2a1a3d;
            border-radius: 10px;
            padding: 13px 16px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            background: #1a0f2e;
            color: #e8def8;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #7a6a94;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #a855f7;
            box-shadow: 0 0 0 3px rgba(168,85,247,0.18);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 130px;
        }

        .contact-form-wrap button[type="submit"] {
            width: 100%;
            justify-content: center;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ===== Side panel ===== */
        .contact-side {
            flex: 1;
        }

        .side-card {
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }

        /* ===== Real embedded map (Google Maps, koi API key nahi chahiye) ===== */
        .side-card .map-embed {
            position: relative;
            height: 220px;
        }

        .side-card .map-embed iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        .side-card .map-embed .map-open-link {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: #1a0f2e;
            color: #d4c5e8;
            font-size: 11px;
            font-weight: 700;
            padding: 7px 14px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s ease;
        }

        .side-card .map-embed .map-open-link:hover {
            background: #a855f7;
            color: #fff;
        }

        .side-card .map-caption {
            background: #12081f;
            padding: 18px 20px;
        }

        .side-card .map-caption h4 {
            font-size: 14px;
            color: #f3e8ff;
            margin-bottom: 4px;
        }

        .side-card .map-caption p {
            font-size: 12.5px;
            color: #9584b0;
        }

        .hours-card {
            background: #12081f;
            border-radius: 20px;
            padding: 28px;
            border: 1px solid #2a1a3d;
        }

        .hours-card h3 {
            font-size: 16px;
            color: #f3e8ff;
            font-weight: 700;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hours-card h3 i { color: #c084fc; }

        .hours-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #2a1a3d;
            font-size: 13.5px;
        }

        .hours-row:last-child { border-bottom: none; }

        .hours-row .day { color: #d4c5e8; font-weight: 600; }
        .hours-row .time { color: #9584b0; }

        .social-row {
            display: flex;
            gap: 12px;
            margin-top: 22px;
        }

        .social-row a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #1a0f2e;
            color: #9584b0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s ease;
        }

        .social-row a:hover {
            background: #a855f7;
            color: #fff;
            transform: translateY(-3px);
        }

        /* ===== Footer ===== */
        .footer {
            background-color: #030008;
            color: #b8a8d4;
            text-align: center;
            padding: 24px;
            font-size: 14px;
        }

        /* ===== Responsive ===== */
        @media (max-width: 900px) {
            .contact-main {
                flex-direction: column;
                padding: 50px 24px;
            }
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }

        @media (max-width: 768px) {
            .contact-hero { height: auto; padding: 46px 20px; }
            .contact-hero h1 { font-size: 26px; }
            .info-cards-section { margin-top: -34px; }
            .contact-form-wrap { padding: 26px; }
        }
    </style>
</head>
<body>

    <!-- ===== Hero ===== -->
    <section class="contact-hero">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <span class="floating-orb orb-1"></span>
        <span class="floating-orb orb-2"></span>
        <span class="floating-orb orb-3"></span>
        <span class="floating-orb orb-4"></span>

        <div class="hero-content">
            <span class="hero-eyebrow"><i class="fa-solid fa-comment-dots"></i> Get In Touch</span>
            <h1>We'd Love To Hear From You</h1>
            <p>Questions, feedback, or just want to say hi? Our team usually replies within 24 hours.</p>
        </div>
    </section>

    <!-- ===== Quick Info Cards ===== -->
    <section class="info-cards-section">
        <div class="info-cards-grid">
            <div class="info-card">
                <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                <h3>Visit Us</h3>
                <p><?php echo htmlspecialchars($officeLocation); ?></p>
            </div>
            <div class="info-card">
                <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                <h3>Call Us</h3>
                <a href="tel:+922100000000">+92 21 0000 0000</a>
            </div>
            <div class="info-card">
                <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                <h3>Email Us</h3>
                <a href="mailto:support@moviebook.com">support@moviebook.com</a>
            </div>
            <div class="info-card">
                <div class="info-icon"><i class="fa-solid fa-headset"></i></div>
                <h3>Support</h3>
                <p>Available 7 days a week</p>
            </div>
        </div>
    </section>

    <!-- ===== Contact Form + Side Info ===== -->
    <section class="contact-main">
        <div class="contact-form-wrap">
            <div class="eyebrow"><span class="accent-bar"></span>Send A Message</div>
            <h2>Let's Start A Conversation</h2>
            <p>Fill out the form below and our team will get back to you shortly.</p>

            <?php if($formSubmitted){ ?>
                <div class="success-banner">
                    <i class="fa-solid fa-circle-check"></i>
                    Thank you! Your message has been sent — we'll get back to you soon.
                </div>
            <?php } ?>

            <form method="POST" action="contact.php">
                <div class="form-row">
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="name" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="john@example.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="How can we help?">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" placeholder="Write your message here..." required></textarea>
                </div>
                <button type="submit" class="btn btn-filled"><i class="fa-solid fa-paper-plane"></i> Send Message</button>
            </form>
        </div>

        <div class="contact-side">
            <div class="side-card">
                <div class="map-embed">
                    <iframe
                        src="https://www.google.com/maps?q=<?php echo $mapQuery; ?>&output=embed"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $mapQuery; ?>" target="_blank" class="map-open-link">
                        <i class="fa-solid fa-up-right-from-square"></i> Open in Maps
                    </a>
                </div>
                <div class="map-caption">
                    <h4>Our Head Office</h4>
                    <p><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($officeLocation); ?></p>
                </div>
            </div>

            <div class="hours-card">
                <h3><i class="fa-solid fa-clock"></i> Working Hours</h3>
                <div class="hours-row">
                    <span class="day">Monday - Friday</span>
                    <span class="time">9:00 AM - 9:00 PM</span>
                </div>
                <div class="hours-row">
                    <span class="day">Saturday</span>
                    <span class="time">10:00 AM - 8:00 PM</span>
                </div>
                <div class="hours-row">
                    <span class="day">Sunday</span>
                    <span class="time">11:00 AM - 6:00 PM</span>
                </div>
                <div class="social-row">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </section>

 <?php
    include_once "include/footer.php";
    
    ?>


</body>
</html>