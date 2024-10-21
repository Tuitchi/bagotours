<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="navcontainer">
    <nav class="nav">
        <div class="nav-upper-options">
            
            <a href="index" class="nav-option <?php echo $current_page == 'index' ? 'active' : ''; ?>">
                <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210182148/Untitled-design-(29).png"
                    class="nav-img" alt="dashboard">
                <h3> Home</h3>
            </a>

            <a href="list" class="nav-option <?php echo $current_page == 'list' ? 'active' : ''; ?>">
                <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210183320/5.png" class="nav-img"
                    alt="report">
                <h3> Tour List</h3>
            </a>
            <a href="most-popular" class="nav-option <?php echo $current_page == 'most-popular' ? 'active' : ''; ?>">
                <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210183323/10.png" class="nav-img"
                    alt="blog">
                <h3> Most Popular</h3>
            </a>
            <?php if(isset($_SESSION['user_id'])) { ?>
            <a href="map" class="nav-option <?php echo $current_page == 'map' ? 'active' : ''; ?>">
                <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210183323/10.png" class="nav-img"
                    alt="blog">
                <h3> Map</h3>
            </a>
            <?php }?>
        </div>
    </nav>
</div>