<?php
include '../include/db_conn.php';

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $stmt = $conn->prepare("SELECT id, img, title, description, address FROM tours WHERE title LIKE ? LIMIT 10");
    $stmt->bind_param("s", $searchTerm);
    $searchTerm = "%".$query."%";
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<a class="dropdown-item" href="tour?tours='.$row['id'].'">'.$row['title'].'</a>';
        }
    } else {
        echo '<div class="dropdown-item">No results found</div>';
    }
}
?>
