<?php
include '../include/db_conn.php';
include '../func/func.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../?action=Invalid");
    exit();
}
$user_id = $_SESSION['user_id'];
$id = $_SESSION['tour_id'];
$pp = $_SESSION['profile-pic'];
$title = '';

try {
    $stmt = $conn->prepare("SELECT title FROM tours WHERE id = :tour_id");
    $stmt->bindParam(':tour_id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $title = $stmt->fetchColumn();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if (!validateQR($conn, $id)) {
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tourId = "' . $id . '";
        var title = "' . $title . '";
        
        fetch("../php/generateQR.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                tour: title + "|" + tourId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            console.log("Response data:", data); // Log full response data
            if (data.success) {
                console.log("QR Code generated successfully:", data.message);
            } else {
                console.error("Error generating QR Code:", data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    });
    </script>';
} else {
    $qr = getQR($conn, $id);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="assets/icons/<?php echo $webIcon ?>">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
    <title>BaGoTours - View Tour</title>
    <style>
        .tourQR {
            width: auto;
            height: auto;
            margin: 0 9px;
            display: inline-block;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        #qr-image {
            margin: 5px 5px;
            width: 100%;
            object-fit: contain;
        }

        .btn {
            float: right;
            margin-top: 10px;
            margin-right: 10px;
            color: white;
            width: 90px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #btn-print {
            background-color: #3498db;
        }

        #btn-print:hover {
            background-color: #1c70b1;
        }

        .btn {
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <?php include 'includes/sidebar.php'; ?>
    <section id="content">
        <?php include 'includes/navbar.php'; ?>
        <main id="main">
            <div class="head-title">
                <div class="left">
                    <h1>QR Code</h1>
                    <?php include 'includes/breadcrumb.php'; ?>
                </div>
            </div>
            <div class="table-data">
                <div class="tourQR">
                    <?php if (isset($qr) && !empty($qr)) { ?>
                        <img id="qr-image" src="<?php echo $qr[0]['qr_code_path']; ?>" alt="QR Code">
                        <div class="qr-code-info">
                            <h4><?php echo $qr[0]['title']; ?></h4>
                            <button class="btn" id="btn-print" data-image="<?php echo $qr[0]['qr_code_path']; ?>" data-title="<?php echo $qr[0]['title']; ?>">Print</button>
                        </div>
                    <?php } else { ?>
                        <p>QR Code not generated yet.</p>
                    <?php } ?>
                </div>
            </div>
        </main>
    </section>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            $(document).on('click', '#btn-print', function() {
                const qrImage = $(this).data('image');
                const qrTitle = $(this).data('title');

                const printWindow = window.open('', '_blank');
                printWindow.document.write('<html><head><title>Print QR Code</title>');
                printWindow.document.write('<style>body { text-align: center; font-family: Arial, sans-serif; }</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write('<h4>' + qrTitle + '</h4>');
                printWindow.document.write('<img src="' + qrImage + '" style="width: 100%; max-width: 500px;">');
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
            });
        });
    </script>
</body>

</html>