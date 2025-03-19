<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
    i {
        font-size: 1.5rem;
        margin-right: 10px;
        transition: color 0.3s ease;
    }
</style>
<div class="navcontainer">
    <nav class="nav">
        <div class="nav-upper-options">

            <a href="home" class="nav-option <?php echo $current_page == 'home' ? 'active' : ''; ?>">
                <i class='bx bxs-dashboard'></i>
                <h3> Home</h3>
            </a>

            <a href="event" class="nav-option <?php echo (in_array($current_page, ['event', 'view-event', 'history-event']))  ? 'active' : ''; ?>">
                <i class='bx bxs-party'></i>
                <h3> Event</h3>
            </a>
            <a href="list" class="nav-option <?php echo (in_array($current_page, ['list', 'tour']))  ? 'active' : ''; ?>">
                <i class='bx bxs-map-alt'></i>
                <h3> Tourist List</h3>
            </a>
            <a href="most-popular" class="nav-option <?php echo $current_page == 'most-popular' ? 'active' : ''; ?>"><i
                    class='bx bxs-hot'></i>
                <h3> Most Popular</h3>
            </a>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <a href="map" class="nav-option <?php echo $current_page == 'map' ? 'active' : ''; ?>"><i
                        class='bx bxs-map'></i>
                    <h3> Tourist Map</h3>
                </a>
            <?php } ?>
        </div>
        <div class="nav-lower-options">
            <div class="below">
                <a href="term-of-service"><span>Terms of Service</span></a>
            </div>
        </div>
    </nav>
</div>