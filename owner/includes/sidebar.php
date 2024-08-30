<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<section id="sidebar">
    <a href="home" class="brand">
        <i class='bx bxs-smile'></i>
        <span class="text">BaGoTours</span>
    </a>
    <ul class="side-menu top">
        <li class="<?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
            <a href="dashboard">
                <i class='bx bxs-dashboard'></i>
                <span class="text">Dashboard</span>
            </a>
        </li>
        <li class="<?php echo $current_page == 'tour' ? 'active' : ''; ?>">
            <a href="tours">
                <i class='bx bxs-map-alt'></i>
                <span class="text">Tour</span>
            </a>
        </li>
        <li class="<?php echo $current_page == 'booking' ? 'active' : ''; ?>">
            <a href="booking">
                <i class='bx bxs-calendar-star' ></i>
                <span class="text">Booking</span>
            </a>
        </li>
        <li class="<?php echo $current_page == 'inq' ? 'active' : ''; ?>">
            <a href="inq">
                <i class='bx bxs-message-rounded'></i>
                <span class="text">Inquiries</span>
            </a>
        </li>
    </ul>
    <ul class="side-menu">
        <li class="<?php echo $current_page == 'setting' ? 'active' : ''; ?>">
            <a href="setting">
                <i class='bx bxs-cog'></i>
                <span class="text">Settings</span>
            </a>
        </li>
        <li>
            <a href="../php/logout.php" class="logout">
                <i class='bx bxs-log-out-circle'></i>
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</section>
