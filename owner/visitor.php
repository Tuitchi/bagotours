<?php
include '../include/db_conn.php';
session_start();
$user_id = $_SESSION['user_id'];

require_once __DIR__ . '/../func/dashboardFunc.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$tour_id = isset($_GET['tour']) ? $_GET['tour'] : '';
$visitor_type = isset($_GET['visitorType']) ? $_GET['visitorType'] : '';
$specific_date = isset($_GET['date']) ? $_GET['date'] : '';
$monthInput = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$results_per_page = 10;
$counter = ($page - 1) * $results_per_page + 1;
$start_from = ($page - 1) * $results_per_page;
// Helper function to dynamically construct queries
function buildQuery($baseSql, $filters, &$params)
{
    foreach ($filters as $key => $value) {
        if (!empty($value)) {
            switch ($key) {
                case 'tour_id':
                    $baseSql .= " AND vr.tour_id = :tour_id";
                    $params[':tour_id'] = $value;
                    break;
                case 'search':
                    $baseSql .= " AND (uc.name LIKE :search OR uc.username LIKE :search OR uc.email LIKE :search)";
                    $params[':search'] = '%' . $value . '%';
                    break;
                case 'visitor_type':
                    if ($value === 'bago') {
                        $baseSql .= " AND vr.city_residence = 'Bago City'";
                    } elseif ($value === 'nonbago') {
                        $baseSql .= " AND vr.city_residence != 'Bago City'";
                    }
                    break;
                case 'specific_date':
                    $baseSql .= " AND DATE(vr.visit_time) = :specific_date";
                    $params[':specific_date'] = $value;
                    break;
                case 'month_year':
                    list($year, $month) = explode('-', $value);
                    $baseSql .= " AND MONTH(vr.visit_time) = :month AND YEAR(vr.visit_time) = :year";
                    $params[':month'] = $month;
                    $params[':year'] = $year;
                    break;
                case 'year':
                    $baseSql .= " AND YEAR(vr.visit_time) = :year";
                    $params[':year'] = $value;
                    break;
            }
        }
    }
    return $baseSql;
}

// Filters array
$filters = [
    'tour_id' => $tour_id,
    'search' => $search,
    'visitor_type' => $visitor_type,
    'specific_date' => $specific_date,
    'month_year' => $monthInput,
    'year' => $year,
];

// Count total records for pagination
$countSql = "SELECT COUNT(*) FROM visit_records vr 
             JOIN tours t ON vr.tour_id = t.id 
             JOIN users u ON u.id = t.user_id 
             JOIN users uc ON uc.id = vr.user_id  
             WHERE u.id = :user_id";
$countParams = [':user_id' => $user_id];
$countSql = buildQuery($countSql, $filters, $countParams);

$countStmt = $conn->prepare($countSql);
$countStmt->execute($countParams);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $results_per_page);

// Prepare the main query for fetching visitor data
$sql = "SELECT vr.id as id, vr.user_id as client, t.title as tour_name,CONCAT(u.firstname, '', u.lastname) as admin, 
               vr.visit_time as datetime, vr.city_residence as city, 
               CONCAT(uc.firstname, '', uc.lastname) as client_name, uc.email as client_email
        FROM visit_records vr 
        JOIN tours t ON vr.tour_id = t.id 
        JOIN users u ON u.id = t.user_id 
        JOIN users uc ON uc.id = vr.user_id  
        WHERE u.id = :user_id";
$params = [':user_id' => $user_id];
$sql = buildQuery($sql, $filters, $params);
$sql .= " ORDER BY vr.visit_time DESC LIMIT $start_from, $results_per_page";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

$visitRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/visitor.css">
    <script src="https://www.gstatic.com/charts/loader.js"></script>

    <title>BaGoTours || Visitor Records</title>
</head>

<body>

    <?php include 'includes/sidebar.php'; ?>
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
        <main>
            <div class="head-title">
                <div class="left">
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
            </div>

            <ul class="box-info">

                <li>
                    <i class='bx bxs-city'></i>
                    <span class="text">
                        <h3><?php echo Bago($conn, $user_id); ?></h3>
                        <p>Bago City Visitors</p>
                    </span>
                </li>
                <li>
                    <i class='bx bx-globe'></i>
                    <span class="text">
                        <h3><?php echo nonBago($conn, $user_id); ?></h3>
                        <p>Non-Bago City Visitors</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-user'></i>
                    <span class="text">
                        <h3><?php echo totalVisitors($conn, $user_id); ?></h3>
                        <p>Total Visitors</p>
                    </span>
                </li>
            </ul>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Visitors</h3>
                        <div class="search-container">
                            <i class='bx bx-search' id="search-icon"></i>
                            <input type="text" id="search-input" placeholder="Search...">
                        </div>
                        <div class="filter" id="openFilter">
                            <i class='bx bx-filter'></i>

                            <!-- Hidden Dropdown -->
                            <div id="filterDropdown" class="dropdown-content">
                                <div class="section-header">
                                    <hr class="section-divider">
                                    <p class="section-title">Tourist Spot</p>
                                    <hr class="section-divider">
                                </div>
                                <select name="tour" id="tour">
                                    <option value="" selected>All</option>
                                    <?php require_once '../func/func.php';
                                    $tours = getTouristSpots($conn, $user_id);
                                    foreach ($tours as $tour) { ?>
                                        <option value="<?php echo $tour['id'] ?>"><?php echo $tour['title'] ?></option>
                                    <?php } ?>
                                </select>

                                <div class="section-header">
                                    <hr class="section-divider">
                                    <p class="section-title">Visitor</p>
                                    <hr class="section-divider">
                                </div>
                                <div class="form-group">
                                    <div class="radio-group">
                                        <input type="radio" class="visitor" id="allVisitor" value="" name="visitor"
                                            checked>
                                        <label for="allVisitor">All</label>
                                    </div>
                                    <div class="radio-group">
                                        <input type="radio" class="visitor" id="bagoVisitor" value="bago"
                                            name="visitor">
                                        <label for="bagoVisitor">Bago</label>
                                    </div>
                                    <div class="radio-group">
                                        <input type="radio" class="visitor" id="nonBagoVisitor" value="nonbago"
                                            name="visitor">
                                        <label for="nonBagoVisitor">Non-Bago</label>
                                    </div>
                                </div>

                                <!-- Date Filter -->
                                <div class="section-header">
                                    <hr class="section-divider">
                                    <p class="section-title">Date</p>
                                    <hr class="section-divider">
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <label for="specificDate">Date:</label>
                                        <input type="date" id="specificDate"><br>
                                    </div>
                                    <div class="input-group">
                                        <label for="month">Month:</label>
                                        <input type="month" id="month"><br>
                                    </div>
                                    <div class="input-group">
                                        <label for="year">Year:</label>
                                        <input type="number" id="year" placeholder="YYYY" min="2024" max="2100"><br>
                                    </div>
                                </div>
                                <div class="button-group">
                                    <button type="button" id="delete">Clear all</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="visitorTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Tour</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($visitRecords as $index => $record) {
                                echo "<tr>";
                                echo "<td>" . $counter++ . "</td>"; // Row number with offset
                                echo "<td>" . htmlspecialchars($record['client_name']) . "</td>"; // Client Name
                                echo "<td>" . htmlspecialchars($record['client_email']) . "</td>"; // Email
                                echo "<td>" . htmlspecialchars($record['city']) . "</td>"; // Address (City Residence)
                                echo "<td>" . htmlspecialchars($record['tour_name']) . "</td>"; // Tour Name
                            
                                $date = new DateTime($record['datetime']);
                                echo "<td>" . htmlspecialchars($date->format('M. d, Y')) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="pagination">
                        <?php
                        // Ensure $page is an integer
                        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

                        $start_page = max(1, $page - 2);
                        $end_page = min($totalPages, $page + 2);

                        if ($page > 1): ?>
                            <a class="btn" href="?page=<?php echo $page - 1; ?>">&laquo; Prev</a>
                        <?php endif; ?>

                        <?php if ($start_page > 1): ?>
                            <a href="?page=1">1</a>
                            <?php if ($start_page > 2): ?>
                                <span>...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($end_page < $totalPages): ?>
                            <?php if ($end_page < $totalPages - 1): ?>
                                <span>...</span>
                            <?php endif; ?>
                            <a href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
                        <?php endif; ?>

                        <?php if ($page < $totalPages): ?>
                            <a class="btn" href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
                        <?php endif; ?>
                    </div>
                    <div class="loader" style="display: none;"></div>
                </div>
            </div>
        </main>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        $(document).ready(function () {
            const $dropdownButton = $('#openFilter');
            const $filterContainer = $('.filter');
            const $dropdownContent = $filterContainer.find('.dropdown-content');
            const $searchInput = $('#search-input');
            const $tourFilter = $('#tour');
            const $visitorFilter = $('input[name="visitor"]');
            const $monthFilter = $('#month');
            const $yearFilter = $('#year');
            const $specificDateFilter = $('#specificDate');
            const $visitorBodyTable = $('#visitorTable tbody');
            const $paginationContainer = $('.pagination');

            // Toggle the filter dropdown

            // Function to fetch filtered data from the server
            function fetchFilteredData() {
                const search = $searchInput.val().trim();
                const tour = $tourFilter.val();
                const visitor = $visitorFilter.filter(':checked').val();
                const month = $monthFilter.val();
                const year = $yearFilter.val();
                const specificDate = $specificDateFilter.val();
                const page = new URLSearchParams(window.location.search).get('page') || 1;

                const params = new URLSearchParams({
                    search,
                    tour,
                    visitorType: visitor, // Match server-side parameter name
                    month,
                    year,
                    date: specificDate, // Match server-side parameter name
                    page
                });
                console.log('Fetching data with params:', params.toString());
                $.ajax({
                    url: `?${params.toString()}`, // Server URL with query parameters
                    type: 'GET',
                    success: function (response) {
                        console.log('AJAX response:', response);
                        const $doc = $(response);

                        // Update table body
                        const $newTableBody = $doc.find('#visitorTable tbody');
                        console.log('New Table Body:', $newTableBody.html()); // Ensure correct table content
                        $visitorBodyTable.html($newTableBody.html());

                        // Update pagination
                        const $newPagination = $doc.find('.pagination');
                        console.log('New Pagination:', $newPagination.html()); // Ensure correct pagination content
                        $paginationContainer.html($newPagination.html());
                    },
                    error: function (error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }
            function resetFilters() {
                $searchInput.val('');
                $tourFilter.val('');
                $visitorFilter.prop('checked', false); // Reset radio buttons
                $('#allVisitor').prop('checked', true);
                $monthFilter.val('');
                $yearFilter.val('');
                $specificDateFilter.val('');
                fetchFilteredData();
            }
            // Add event listeners for search and filters
            // Add event listeners for filters
            $searchInput.on('input', fetchFilteredData);
            $tourFilter.on('change', fetchFilteredData);
            $visitorFilter.on('change', fetchFilteredData);
            $monthFilter.on('change', fetchFilteredData);
            $yearFilter.on('change', fetchFilteredData);
            $specificDateFilter.on('change', fetchFilteredData);

            // Pagination links
            $paginationContainer.on('click', 'a', function (e) {
                e.preventDefault();
                const url = new URL(window.location.href); // Use the current page's URL as base
                const page = $(this).attr('href').split('=')[1]; // Extract the page number from href

                // Set the 'page' parameter in the URL
                url.searchParams.set('page', page);

                // Update the URL in the address bar without reloading
                history.pushState(null, '', url);

                fetchFilteredData();
            });

            $dropdownButton.on('click', function (event) {
                event.stopPropagation();
                $filterContainer.toggleClass('active');
            });

            // Close the dropdown when clicking outside of it
            $(window).on('click', function () {
                if ($filterContainer.hasClass('active')) {
                    $filterContainer.removeClass('active');
                }
            });

            // Prevent closing the dropdown when clicking inside
            $dropdownContent.on('click', function (event) {
                event.stopPropagation();
            });
            // Reset filters function


            // Reset filters button
            $('#delete').on('click', resetFilters);

            // Handle back/forward navigation
            $(window).on('popstate', fetchFilteredData);
        });
    </script>



</body>

</html>