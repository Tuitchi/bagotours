<style>
    .breadcrumb a {
        text-decoration: none;
        color: #007bff;
    }

    .breadcrumb .breadcrumb-active {
        color: #5891e0;
        font-weight: 600;
        cursor: default;
    }

    .breadcrumb .breadcrumb-active::after {
        content: "";
    }
</style>
<?php
$breadcrumbs = [
    'home' => [
        ['title' => 'Home', 'url' => 'home']
    ],
    'booking' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'Booking', 'url' => 'booking']
    ],
    'view_booking' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'Booking', 'url' => 'booking'],
        ['title' => 'View', 'url' => 'view_booking.php']
    ],
    'dashboard' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'dashboard', 'url' => 'dashboard']
    ],
    'inq' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'Inquiries', 'url' => 'inq']
    ],
    'pending' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'Pending', 'url' => 'pending']
    ],
    'setting' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'Settings', 'url' => 'setting']
    ],
    'tours' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'Tours', 'url' => 'tours']
    ],
    'view_tour' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'Tours', 'url' => 'tours'],
        ['title' => 'View', 'url' => 'view_tour']
    ],
    'edit_tour' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'Tours', 'url' => 'tours'],
        ['title' => 'View', 'url' => 'view_tour'],
        ['title' => 'Edit', 'url' => 'edit_tour']
    ],
    'user' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'Users', 'url' => 'user']
    ],
    'qr' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'QR Code', 'url' => 'qr']
    ],
];
$page = basename($_SERVER['PHP_SELF'], '.php');
$currentBreadcrumbs = $breadcrumbs[$page] ?? $breadcrumbs['home'];
?>

<ul class="breadcrumb">
    <?php foreach ($currentBreadcrumbs as $key => $breadcrumb): ?>
        <li>
            <?php if ($key === array_key_last($currentBreadcrumbs)): ?>
                <span class="breadcrumb-active">
                    <?php echo $breadcrumb['title']; ?>
                </span>
            <?php else: ?>
                <a href="<?php echo $breadcrumb['url']; ?>">
                    <?php echo $breadcrumb['title']; ?>
                </a>
            <?php endif; ?>
        </li>
        <?php if ($key !== array_key_last($currentBreadcrumbs)): ?>
            <li><i class='bx bx-chevron-right'></i></li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
<div id="rotate-message">
    <div class="message">
        <p>Please rotate your device to landscape mode for the best experience.</p>
        <img src="../assets/rotate.gif" alt="Rotate Icon">
    </div>
</div>