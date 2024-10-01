<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
require_once __DIR__ . '/../../func/dashboardFunc.php';
$sidebarHidden = isset($_SESSION['sidebar_hidden']) && $_SESSION['sidebar_hidden'] === "1";
?>

<section id="sidebar" class="<?php echo $sidebarHidden ? 'hide' : ''; ?>">
    <a href="home" class="brand">
        <img src="../assets/icons/websiteIcon.png" alt="" style="width: 60px;">
        <span class="text">BagoTours - Admin</span>
    </a>
    <ul class="side-menu top">
        <li class="<?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
            <a href="dashboard">
                <i class='bx bxs-dashboard'></i>
                <span class="text">Dashboard</span>
            </a>
        </li>
        <li class="<?php echo $current_page == 'tours' ? 'active' : ''; ?>">
            <a href="tours">
                <i class='bx bxs-map-alt'></i>
                <span class="text">Tours</span>
            </a>
        </li>
        <li class="<?php echo $current_page == 'booking' ? 'active' : ''; ?>">
            <a href="booking">
                <i class='bx bxs-calendar-star'></i>
                <span class="text">Booking</span>
                <span class="pending"><?php echo totalBooking($conn)?></span>
            </a>
        </li>
        <li class="<?php echo $current_page == 'inq' ? 'active' : ''; ?>">
            <a href="inq">
                <i class='bx bxs-message-rounded'></i>
                <span class="text">Inquiries</span>
            </a>
        </li>
        <li class="<?php echo $current_page == 'user' ? 'active' : ''; ?>">
            <a href="user">
                <i class='bx bxs-group'></i>
                <span class="text">Users</span>
            </a>
        </li>
        <li class="<?php echo $current_page == 'pending' ? 'active' : ''; ?>">
            <a href="pending">
                <i class='bx bxs-time'></i>
                <span class="text">Pending</span>
                <span class="pending"><?php echo totalPending($conn)?></span>
            </a>
        </li>
        <li class="<?php echo $current_page == 'qr' ? 'active' : ''; ?>">
            <a href="qr">
                <i class='bx bx-qr'></i>
                <span class="text">QR Code</span>
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
