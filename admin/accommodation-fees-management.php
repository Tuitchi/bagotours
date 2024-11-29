<?php
include '../include/db_conn.php';

if (isset($_GET['id'])) {
} else {
    header("Location: tours.php");
    exit();
}

session_start();
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../assets/icons/<?php echo $webIcon ?>">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/add.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>BaGoTours || Tour Management</title>
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
            <div class="table-data">
                <div class="order">
                    <div class="title">
                        <h2>Accommodation and Fees Management</h2>
                        <p>Choose the types of fees and accommodations you want to include in your tour.</p>
                        <select id="fees" onchange="changeForm()">
                            <option value="" selected disabled>Select Fee Type</option>
                            <option value="transportation">Entrance Fee</option>
                            <option value="food">Parking Fee</option>
                            <option value="accommodation">Accommodation</option>
                        </select>
                    </div>
                    <!-- Form for Parking Fee -->
                    <form id="parkingFee" method="POST" enctype="multipart/form-data" style="display: none;">
                        <input type="hidden" name="tour_id" id="tour_id" value="<?php echo $_GET['id']; ?>">
                        <div class="section-header">
                            <hr class="section-divider">
                            <h3 class="section-title">Parking Fee</h3>
                            <hr class="section-divider">
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <label for="description">Description <span>required</span></label>
                                <input type="text" id="description" name="description" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <label for="amount">Amount <span>required</span></label>
                                <input type="number" id="amount" name="amount" required min="0">
                            </div>
                        </div>
                        <button type="submit" class="btn-submit">Add Parking Fee</button>
                    </form>

                    <!-- Form for Entrance Fee -->
                    <form id="entranceFee" method="POST" enctype="multipart/form-data" style="display: none;">
                        <input type="hidden" name="tour_id" id="tour_id" value="<?php echo $_GET['id']; ?>">
                        <div class="section-header" style="padding:0;">
                            <hr class="section-divider">
                            <h3 class="section-title">Entrance Fee</h3>
                            <hr class="section-divider">
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <label for="description">Description <span>required</span></label>
                                <input type="text" id="description" name="description" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <label for="amount">Amount <span>required</span></label>
                                <input type="number" id="amount" name="amount" required min="0">
                            </div>
                        </div>
                        <button type="submit" class="btn-submit">Add Entrance Fee</button>
                    </form>

                    <!-- Form for Accommodation -->
                    <form id="accommodation" method="POST" enctype="multipart/form-data" style="display: none;">
                        <input type="hidden" name="tour_id" id="tour_id" value="<?php echo $_GET['id']; ?>">
                        <div class="section-header">
                            <hr class="section-divider">
                            <h3 class="section-title">Accommodation</h3>
                            <hr class="section-divider">
                        </div>
                        <div class="form-group">
                            <div class="input-group" style="width:65%">
                                <label for="name">Name <span>required</span></label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="input-group" style="width:35%">
                                <label for="type">Type <span>required</span></label>
                                <input type="text" id="type" name="type" required>
                            </div>
                        </div>
                        <div class="form-group" style="float: left;">
                            <div class="input-group" style="width:30%">
                                <label for="capacity">Capacity <span>required</span></label>
                                <input type="number" id="capacity" name="capacity" required min="1">
                            </div>
                            <div class="input-group" style="width:30%">
                                <label for="total_units">Total Units <span>required</span></label>
                                <input type="number" id="total_units" name="total_units" required min="1">
                            </div>
                        </div>
                        <div class="form-group" style="width: 100%;">
                            <div class="input-group">
                                <label for="description">Description <span>required</span></label>
                                <textarea name="description" id="description"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn-submit">Add Accommodation</button>
                    </form>
                </div>
                <div class="order table">
                    <div class="section-header">
                        <hr class="section-divider">
                        <h3 class="section-title">Accommodations and Fees List</h3>
                        <hr class="section-divider">
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Type</th>
                                <th>Capacity</th>
                                <th>Total Units</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // SQL query to combine accommodations and fees
                            $query = "SELECT 
    id, 
    name AS item_name, 
    type, 
    capacity, 
    total_units, 
    description, 
    cost AS amount
FROM accommodations
UNION
SELECT 
    id, 
    fee_type AS item_name, 
    NULL AS type, 
    NULL AS capacity, 
    NULL AS total_units, 
    description, 
    amount
FROM fees
ORDER BY id ASC;
";
                            try {
                                $stmt = $conn->prepare($query);
                                $stmt->execute();

                                // Fetch all rows
                                $rows = $stmt->fetchAll();
                            } catch (PDOException $e) {
                                die("Error: " . $e->getMessage());
                            }
                            $counter = 1;
                            if (!empty($rows)): ?>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?= $counter ?></td>
                                        <td><?= $row['item_name'] ? htmlspecialchars($row['item_name']) : 'N/A' ?></td>
                                        <td><?= $row['type'] ? htmlspecialchars($row['type']) : 'N/A' ?></td>
                                        <td><?= $row['capacity'] !== null ? htmlspecialchars($row['capacity']) : 'N/A' ?></td>
                                        <td><?= $row['total_units'] !== null ? htmlspecialchars($row['total_units']) : 'N/A' ?></td>
                                        <td><?= htmlspecialchars($row['description']) ?></td>
                                        <td><?= $row['amount'] !== null ? htmlspecialchars($row['amount']) : 'N/A' ?></td>
                                        <td>
                                            <a href="edit.php?id=<?= htmlspecialchars($row['id']) ?>"><i class="bx bx-edit"></i></a>
                                            <a href="delete.php?id=<?= htmlspecialchars($row['id']) ?>"><i class="bx bx-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9">No records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </main>
    </section>

    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.order.table')

            function handleFormSubmit(formId, actionUrl, successMessage) {
                $(`#${formId}`).on('submit', function(event) {
                    event.preventDefault();

                    let formData = new FormData(this);

                    $.ajax({
                        url: actionUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire('Success!', successMessage, 'success');
                            $(`#${formId}`)[0].reset();
                            $('.order.table')
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error!', `Something went wrong: ${xhr.responseText}`, 'error');
                        }
                    });
                });
            }

            handleFormSubmit('parkingFee', '../php/accommodation-fees/add-parkingFees.php', 'Parking Fee added successfully!');
            handleFormSubmit('entranceFee', '../php/accommodation-fees/add-entranceFees.php', 'Entrance Fee added successfully!');
            handleFormSubmit('accommodation', '../php/accommodation-fees/add-accommodation.php', 'Accommodation added successfully!');
        });

        function changeForm() {
            var feeType = document.getElementById("fees").value;

            // Hide all forms initially
            document.getElementById("parkingFee").style.display = "none";
            document.getElementById("entranceFee").style.display = "none";
            document.getElementById("accommodation").style.display = "none";

            // Show the relevant form based on the selected fee type
            if (feeType === "transportation") {
                document.getElementById("entranceFee").style.display = "block";
            } else if (feeType === "accommodation") {
                document.getElementById("accommodation").style.display = "block";
            } else if (feeType === "food") {
                document.getElementById("parkingFee").style.display = "block";
            }
        }
    </script>
</body>

</html>