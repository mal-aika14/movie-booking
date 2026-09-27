<?php

if(!isset($_SESSION["admin_id"])){
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CineAura</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
<div class="mobile-topbar">
    <div class="logo"><i class="fa-solid fa-clapperboard"></i> Cine<span>Aura</span></div>
    <button class="sidebar-toggle" type="button" onclick="toggleSidebar()" aria-label="Toggle menu">
        <i class="fa-solid fa-bars"></i>
    </button>
</div>

<!-- ===== Sidebar ===== -->
<aside class="sidebar" id="sidebar">
    <div class="logo"><i class="fa-solid fa-clapperboard"></i> Cine<span>Aura</span></div>
    <nav class="side-links">
        <a href="admin.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="view_movies.php"><i class="fa-solid fa-film"></i> View Movies</a>
        <a href="view_theatres.php"><i class="fa-solid fa-building"></i> View Theatre</a>
        <a href="view_shows.php"><i class="fa-solid fa-ticket"></i> View Shows</a>
        <a href="view_seatclasses.php"><i class="fa-solid fa-chair"></i> Seat Classes</a>
        <a href="view_bookings.php"><i class="fa-solid fa-clipboard-list"></i> View Bookings</a>
        <a href="view_reviews.php"><i class="fa-solid fa-star"></i> Reviews</a>
        <a href="view_users.php"><i class="fa-solid fa-users"></i> View Users</a>
        <a href="view_messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>

        <a href="../logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
</aside>

<script>
    // Current page ke naam se milta link dhoond ke usay "active" bana do
    (function highlightActiveLink() {
        const currentPage = window.location.pathname.split("/").pop();
        document.querySelectorAll(".side-links a").forEach(function (link) {
            if (link.getAttribute("href") === currentPage) {
                link.classList.add("active");
            }
        });
    })();
    // ===== Sidebar toggle (mobile) — same pattern as index.php's hamburger =====
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
}

document.querySelectorAll(".side-links a").forEach(function(link){
    link.addEventListener("click", function(){
        document.getElementById("sidebar").classList.remove("active");
    });
});

document.addEventListener("click", function(e){
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.querySelector(".sidebar-toggle");
    if (sidebar && sidebar.classList.contains("active") &&
        !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
        sidebar.classList.remove("active");
    }
});
</script>