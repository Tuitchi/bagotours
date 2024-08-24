<?php 
include '../include/db_conn.php';
session_start();

session_regenerate_id();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login?action=Invalid");
    exit();
}
$msg = "";
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM tours WHERE user_id ='$user_id' limit 1");

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $tours = [];
    while ($row = $result->fetch_assoc()) {
        $title = $row['title'];
        $description = $row['description'];
        $address = $row['address'];
        $msg = "You already registered your account.";
    }
} else {
    $fail_msg = "An error occurred while retrieving tours. Please try again.";
}

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Homepage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .navbar {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .navbar .nav-links {
            display: flex;
            align-items: center;
        }
        .navbar .nav-links a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-size: 16px;
        }
        .navbar .nav-links a:hover {
            text-decoration: underline;
        }
        .navbar .profile {
            position: relative;
            display: inline-block;
        }
        .navbar .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }
        .navbar .profile .dropdown-menu {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            color: black;
            min-width: 150px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .navbar .profile .dropdown-menu a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }
        .navbar .profile .dropdown-menu a:hover {
            background-color: #ddd;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include '../lib/navbar.php'; ?>
    <div class="content">
        <h1>Become a Attraction Owner</h1>
        <p>Fill up the following:</p>
        <form action="../php/register_owner.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" id="title" name="title" placeholder="Attraction Name" value="<?php echo isset ($title)? $title : '';?>" required>
            </div>
            <div class="form-group">
                <input type="text" id="address" name="address" placeholder="Address" value="<?php echo isset ($address)? $address : '';?>" required>
            </div>
            <div class="form-group">
                <select name="type" id="type">
                    <option value="">Types</option>
                    <option value="falls">falls</option>
                    <option value="pools">pools</option>
                    <option value="campsite">campsite</option>
                    <option value="beach">beach</option>
                    <option value="historical">historical</option>
                </select>
            </div>
            <div class="form-group">
                <textarea id="description" name="description" rows="5" placeholder="Description" required><?php echo isset ($description)? $description : '';?></textarea>
            </div>
                
                <div class="form-group">
                    <input type="submit" value="Submit">
                </div>
            </form>
    </div>
    <?php if (!empty($msg)): ?>
        <script>
            Swal.fire({
                icon: "success",
                title: "Please Wait!",
                text: "<?php echo $msg; ?>"
            });
        </script>
    <?php endif; ?>
    <script src="../assets/js/home.js"></script>
</body>
</html>
