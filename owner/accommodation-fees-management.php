<?php
include '../include/db_conn.php';

if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];
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
    <style>
        td button {
            font-size: 1.2rem;
            border: none;
            background: transparent;
            cursor: pointer;
        }

        td button:hover {
            color: white;
            background-color: green;
        }

        td .cancel {
            font-size: 1.2rem;
        }

        td .cancel:hover {
            color: white;
            background-color: red;
        }

        .table table tbody td input {
            text-align: center;
            font-size: 0.8rem;
        }
    </style>
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

                <div class="order table">
                    <div class="order">
                        <div class="title">
                            <h2>Accommodation and Fees Management</h2>
                            <p>Choose the types of fees and accommodations you want to include in your tour.</p>
                            <select id="fees" onchange="changeForm()">
                                <option value="" selected disabled>Choose a fee or accommodation type to add</option>
                                <option value="transportation">Entrance Fee</option>
                                <option value="food">Parking Fee</option>
                                <option value="accommodation">Accommodation</option>
                            </select>
                        </div>
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
                                    <label for="amount">Amount <span>required</span></label>
                                    <input type="number" id="amount" name="amount" required min="0">
                                </div>
                            </div>
                            <div class="form-group" style="float: left;">
                                <div class="input-group">
                                    <label for="capacity">Capacity <span>required</span></label>
                                    <input type="number" id="capacity" name="capacity" required min="1">
                                </div>
                                <div class="input-group">
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
                            <button type="submit" class="btn-submit">Add Accommodation</button><br>
                            <br><br>
                        </form>
                    </div>
                    <div class="section-header">
                        <hr class="section-divider">
                        <h3 class="section-title">Accommodations List</h3>
                        <hr class="section-divider">
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Capacity</th>
                                <th>Total Units</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("
                           SELECT 
                               id, 
                               name         AS item_name, 
                               capacity, 
                               total_units, 
                               description, 
                               amount
                           FROM accommodations
                           WHERE tour_id = :tour_id
                       ");
                            $stmt->execute([':tour_id' => $tour_id]);
                            $accommodations = $stmt->fetchAll();
                            $edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : null;
                            $counter = 1;
                            if (!empty($accommodations)): ?>
                                <?php foreach ($accommodations as $accommodation): ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td>
                                            <?php if ($edit_id === $accommodation['id']): ?>
                                                <form method="POST" action="../php/accommodation-fees/update-accommodation.php">
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($accommodation['id']) ?>">
                                                    <input type="hidden" name="tour_id" value="<?= $tour_id ?>">
                                                    <input type="hidden" name="type" value="accommodation">
                                                    <input type="text" name="item_name"
                                                        value="<?= htmlspecialchars($accommodation['item_name']) ?>">
                                                <?php else: ?>
                                                    <?= htmlspecialchars($accommodation['item_name']) ?>
                                                <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($edit_id === $accommodation['id']): ?>
                                                <input type="text" name="capacity"
                                                    value="<?= $accommodation['capacity'] !== null ? htmlspecialchars($accommodation['capacity']) : '' ?>">
                                            <?php else: ?>
                                                <?= $accommodation['capacity'] !== null ? htmlspecialchars($accommodation['capacity']) : 'N/A' ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($edit_id === $accommodation['id']): ?>
                                                <input type="text" name="total_units"
                                                    value="<?= $accommodation['total_units'] !== null ? htmlspecialchars($accommodation['total_units']) : '' ?>">
                                            <?php else: ?>
                                                <?= $accommodation['total_units'] !== null ? htmlspecialchars($accommodation['total_units']) : 'N/A' ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($edit_id === $accommodation['id']): ?>
                                                <input type="text" name="amount"
                                                    value="<?= $accommodation['amount'] !== null ? htmlspecialchars($accommodation['amount']) : '' ?>">
                                            <?php else: ?>
                                                ₱<?= $accommodation['amount'] !== null ? htmlspecialchars($accommodation['amount']) : 'N/A' ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($edit_id === $accommodation['id']): ?>
                                                <input type="text" name="description"
                                                    value="<?= htmlspecialchars($accommodation['description']) ?>">
                                            <?php else: ?>
                                                <?= htmlspecialchars($accommodation['description']) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($edit_id === $accommodation['id']): ?>
                                                <button type="submit"><i class="bx bx-check"></i></button>
                                                <a class="cancel" href="accommodation-fees-management?id=<?= $tour_id ?>"><i
                                                        class="bx bx-x"></i></a>
                                                </form>
                                            <?php else: ?>
                                                <a
                                                    href="accommodation-fees-management?id=<?= $tour_id ?>&edit_id=<?= htmlspecialchars($accommodation['id']) ?>"><i
                                                        class="bx bx-edit"></i></a>
                                                <a href="#" class="delete-accommodation" data-id="<?= htmlspecialchars($accommodation['id']) ?>"><i class="bx bx-trash"></i></a>
                                            <?php endif; ?>
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
                    <div class="section-header">
                        <hr class="section-divider">
                        <h3 class="section-title">Fees List</h3>
                        <hr class="section-divider">
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT 
                                        id, 
                                        name as item_name,  
                                        description,
                                        amount
                                    FROM fees WHERE tour_id = :tour_id");
                            $stmt->execute([':tour_id' => $tour_id]);
                            $fees = $stmt->fetchAll();
                            $counter = 1;
                            if (!empty($fees)): ?>
                                <?php foreach ($fees as $fee): ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td>
                                            <?php if ($edit_id === $fee['id']): ?>
                                                <form method="POST" action="../php/accommodation-fees/update-accommodation.php">

                                                    <input type="hidden" name="type" value="fee">
                                                    <input type="hidden" name="tour_id" value="<?= $tour_id ?>">
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($fee['id']) ?>">
                                                    <input type="text" name="item_name"
                                                        value="<?= htmlspecialchars($fee['item_name']) ?>">
                                                <?php else: ?>
                                                    <?= htmlspecialchars($fee['item_name']) ?>
                                                <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($edit_id === $fee['id']): ?>
                                                <input type="text" name="amount"
                                                    value="<?= $fee['amount'] !== null ? htmlspecialchars($fee['amount']) : '' ?>">
                                            <?php else: ?>
                                                ₱<?= $fee['amount'] !== null ? htmlspecialchars($fee['amount']) : 'N/A' ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($edit_id === $fee['id']): ?>
                                                <input type="text" name="description"
                                                    value="<?= htmlspecialchars($fee['description']) ?>">
                                            <?php else: ?>
                                                <?= htmlspecialchars($fee['description']) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($edit_id === $fee['id']): ?>
                                                <button type="submit"><i class="bx bx-check"></i></button>
                                                <a class="cancel" href="accommodation-fees-management?id=<?= $tour_id ?>"><i
                                                        class="bx bx-x"></i></a>
                                                </form>
                                            <?php else: ?>
                                                <a
                                                    href="accommodation-fees-management?id=<?= $tour_id ?>&edit_id=<?= htmlspecialchars($fee['id']) ?>"><i
                                                        class="bx bx-edit"></i></a>
                                                <a href="#" class="delete-fee" data-id="<?= htmlspecialchars($fee['id']) ?>"><i class="bx bx-trash"></i></a>
                                            <?php endif; ?>
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

            <?php if (isset($_GET['success']) && $_['success'] = 1): ?>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Update Success',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });

            <?php endif; ?>
            $('.order.table')
            $(".delete-fee").on("click", function(e) {
                e.preventDefault();

                const feeId = $(this).data("id");

                if (confirm("Are you sure you want to delete this fee?")) {
                    $.ajax({
                        url: "../php/accommodation-fees/delete-fee.php",
                        type: "POST",
                        data: {
                            id: feeId
                        },
                        success: function(response) {
                            alert(response.message);
                            if (response.success) {
                                Swal.fire('Success!', 'The fee has been successfully deleted.', 'success');
                                $('.order.table').load(window.location.href + ' .order.table');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error!', `Something went wrong: ${xhr.responseText}`, 'error');
                        }
                    });
                }
            });
            $(".delete-accommodation").on("click", function(e) {
                e.preventDefault();

                const accomId = $(this).data("id");

                if (confirm("Are you sure you want to delete this fee?")) {
                    $.ajax({
                        url: "../php/accommodation-fees/delete-accommodation.php",
                        type: "POST",
                        data: {
                            id: accomId
                        },
                        success: function(response) {
                            alert(response.message);
                            if (response.success) {
                                Swal.fire('Success!', 'The fee has been successfully deleted.', 'success');
                                $('.order.table').load(window.location.href + ' .order.table');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error!', `Something went wrong: ${xhr.responseText}`, 'error');
                        }
                    });
                }
            });

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
                            $('.order.table').load(window.location.href + ' .order.table');
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