<?php
session_start();



include_once "../connect.php";
include_once "side.php";

$movieCountQuery = "SELECT * FROM movies";
$movieCountResult = mysqli_query($conn, $movieCountQuery);
$totalMovies = mysqli_num_rows($movieCountResult);

$theatreCountQuery = "SELECT * FROM theatres";
$theatreCountResult = mysqli_query($conn, $theatreCountQuery);
$totalTheatres = mysqli_num_rows($theatreCountResult);

$showCountQuery = "SELECT * FROM shows";
$showCountResult = mysqli_query($conn, $showCountQuery);
$totalShows = mysqli_num_rows($showCountResult);

// ===== Contact form se aayi hui saari messages nikal rahe hain =====
$messagesQuery = "SELECT * FROM messages ORDER BY created_at DESC";
$messagesResult = mysqli_query($conn, $messagesQuery);
$totalMessages = $messagesResult ? mysqli_num_rows($messagesResult) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - MovieBook Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* ===== Page heading (icon + gradient title + subtitle), same style as other admin pages ===== */
        .page-heading {
            opacity: 0;
            animation: fadeSlideDown 0.6s ease forwards;
        }

        .page-heading h1 {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 2.1rem;
            font-weight: 800;
            letter-spacing: 0.3px;
            margin: 0;
            background: linear-gradient(90deg, #a855f7, #6b21a8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-heading h1::before {
            content: "\f0e0";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            -webkit-text-fill-color: initial;
            color: #c084fc;
            background: rgba(168, 85, 247, 0.15);
            padding: 10px;
            border-radius: 12px;
            font-size: 1.3rem;
            animation: pulseIcon 2.5s ease-in-out infinite;
        }

        .page-heading .page-subtitle {
            margin: 6px 0 0 62px;
            color: #9584b0;
            font-size: 0.95rem;
            font-weight: 400;
            letter-spacing: 0.2px;
        }

        @keyframes fadeSlideDown {
            from { opacity: 0; transform: translateY(-14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulseIcon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }

        /* ===== Ye styles sirf is page ke messages list ke liye hain ===== */
        .message-card {
            background: #12081f;
            border-radius: 12px;
            padding: 20px 22px;
            margin-bottom: 16px;
            border: 1px solid #2a1a3d;
            box-shadow: 0 4px 14px rgba(0,0,0,0.5);
        }

        .message-card .message-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .message-card .sender-name {
            font-size: 15px;
            font-weight: 700;
            color: #f3e8ff;
        }

        .message-card .sender-email {
            font-size: 13px;
            color: #c084fc;
        }

        .message-card .message-date {
            font-size: 12px;
            color: #9584b0;
            white-space: nowrap;
        }

        .message-card .message-subject {
            font-size: 13.5px;
            font-weight: 600;
            color: #d4c5e8;
            margin-bottom: 8px;
        }

        .message-card .message-body {
            font-size: 13.5px;
            color: #b8a8d4;
            line-height: 1.6;
        }

        .no-messages {
            background: #12081f;
            border-radius: 12px;
            padding: 50px 20px;
            text-align: center;
            border: 1px solid #2a1a3d;
            color: #9584b0;
        }
    </style>
</head>
<body class="dashboard-body">

   
    <!-- ===== Main content ===== -->
    <div class="admin-content">

        <div class="admin-topbar">
            <div class="page-heading">
                <h1>Messages</h1>
                <p class="page-subtitle">View all messages sent through MovieBook</p>
            </div>
        </div>

        <!-- ===== Stats ===== -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon movies"><i class="fa-solid fa-film"></i></div>
                <div>
                    <p class="stat-label">Total Movies</p>
                    <p class="stat-value"><?php echo $totalMovies; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon theatres"><i class="fa-solid fa-building"></i></div>
                <div>
                    <p class="stat-label">Total Theatres</p>
                    <p class="stat-value"><?php echo $totalTheatres; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon shows"><i class="fa-solid fa-ticket"></i></div>
                <div>
                    <p class="stat-label">Total Shows</p>
                    <p class="stat-value"><?php echo $totalShows; ?></p>
                </div>
            </div>
        </section>

        <!-- ===== Messages List ===== -->
        <section class="manage-section">
            <div class="manage-header">
                <h2><i class="fa-solid fa-envelope"></i> Contact Messages (<?php echo $totalMessages; ?>)</h2>
            </div>

            <?php if($totalMessages == 0){ ?>
                <div class="no-messages">
                    <p>No messages have been received yet.</p>
                </div>
            <?php }else{ ?>
                <?php foreach($messagesResult as $msg){ ?>
                    <div class="message-card">
                        <div class="message-top">
                            <div>
                                <div class="sender-name"><?php echo htmlspecialchars($msg["name"]); ?></div>
                                <div class="sender-email"><?php echo htmlspecialchars($msg["email"]); ?></div>
                            </div>
                            <div class="message-date">
                                <?php echo date("d M Y, g:i A", strtotime($msg["created_at"])); ?>
                            </div>
                        </div>
                        <?php if(!empty($msg["subject"])){ ?>
                            <div class="message-subject">Subject: <?php echo htmlspecialchars($msg["subject"]); ?></div>
                        <?php } ?>
                        <div class="message-body"><?php echo nl2br(htmlspecialchars($msg["message"])); ?></div>
                    </div>
                <?php } ?>
            <?php } ?>
        </section>

    </div>

</body>
</html>