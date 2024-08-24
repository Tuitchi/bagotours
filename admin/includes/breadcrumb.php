<?php
$breadcrumbs = [
    'home' => [
        ['title' => 'Home', 'url' => 'home']
    ],
    'booking' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'Booking', 'url' => 'booking']
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
    'user' => [
        ['title' => 'Home', 'url' => 'home'],
        ['title' => 'Users', 'url' => 'user']
    ],
];
$page = basename($_SERVER['PHP_SELF'], '.php');
$currentBreadcrumbs = $breadcrumbs[$page] ?? $breadcrumbs['home'];
?>

<ul class="breadcrumb">
    <?php foreach ($currentBreadcrumbs as $key => $breadcrumb): ?>
        <li>
            <a href="<?php echo $breadcrumb['url']; ?>" <?php if ($key === array_key_last($currentBreadcrumbs)) echo 'class="active"'; ?>>
                <?php echo $breadcrumb['title']; ?>
            </a>
        </li>
        <?php if ($key !== array_key_last($currentBreadcrumbs)): ?>
            <li><i class='bx bx-chevron-right'></i></li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>