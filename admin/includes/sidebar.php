<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
require_once __DIR__ . '/../../func/dashboardFunc.php';
$sidebarClass = isset($_SESSION['sidebar_hidden']) && $_SESSION['sidebar_hidden'] === 'hide' ? 'hide' : '';
?>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<section id="sidebar" class="<?php echo $sidebarClass ?>">
    <a href="home" class="brand">
        <img src="../assets/icons/logo.png" alt="" style="width: 60px;">
        <span class="text">BagoTours</span>
    </a>
    <ul class="side-menu top">
        <li class="<?php echo $current_page == 'home' ? 'active' : ''; ?>">
            <a href="home">
                <i class='bx bxs-home'></i>
                <span class="text">Home</span>
            </a>
        </li>
        <li class="<?php echo (in_array($current_page, ['dashboard', 'visitor'])) ? 'active' : ''; ?>">
            <a href="dashboard">
                <i class='bx bxs-dashboard'></i>
                <span class="text">Dashboard</span>
            </a>
        </li>
        <li class="<?php echo (in_array($current_page, ['event', 'edit-event', 'add-event'])) ? 'active' : ''; ?>">
            <a href="event">
                <i class='bx bxs-party'></i>
                <span class="text">Events</span>
            </a>
        </li>
        <li class="<?php echo (in_array($current_page, ['tours', 'edit-tour', 'add-tour', 'accommodation-fees-management'])) ? 'active' : ''; ?>">
            <a href="tours">
                <i class='bx bxs-map-alt'></i>
                <span class="text">Tours</span>
            </a>
        </li>
        <li class="<?php echo $current_page == 'user' ? 'active' : ''; ?>">
            <a href="user">
                <i class='bx bxs-user'></i>
                <span class="text">Users</span>
            </a>
        </li>

        <li class="<?php echo $current_page == 'booking' ? 'active' : ''; ?>">
            <a href="booking">
                <i class='bx bxs-calendar-star'></i>
                <span class="text">Booking</span>
                <?php
                $bookingCount = totalBooking($conn, $user_id);
                if ($bookingCount > 0) {
                    echo "<span class='notifCount'>" . $bookingCount . "</span>";
                }
                ?>
            </a>
        </li>
        <li class="<?php echo $current_page == 'pending' ? 'active' : ''; ?>">
            <a href="pending">
                <i class='bx bxs-time'></i>
                <span class="text">Pending Tour</span>
                <?php
                $pendingCount = totalPending($conn);
                if ($pendingCount > 0) {
                    echo "<span class='notifCount'>" . $pendingCount . "</span>";
                }
                ?>
            </a>
        </li>
        <li class="<?php echo $current_page == 'qr' ? 'active' : ''; ?>">
            <a href="qr">
                <i class='bx bx-qr'></i>
                <span class="text">QR Code</span>
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
            <a href="#" class="logout" onclick="return confirmLogout()">
                <i class='bx bxs-log-out-circle'></i>
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Log out',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../php/logout.php';
            }
        });
    }
</script>