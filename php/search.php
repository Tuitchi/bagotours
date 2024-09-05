<?php
include '../include/db_conn.php';

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $stmt = $conn->prepare("SELECT id, img, title, type FROM tours WHERE title LIKE ? LIMIT 10");
    $stmt->bind_param("s", $searchTerm);
    $searchTerm = "%" . $query . "%";
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<a class="dropdown-item" style="font-size:13px;display:flex;align-items:center;gap: 15px;" href="tour?tours=' . $row['id'] . '">
            <img style="width:40px;height:40px;border-radius: 4px;object-fit: cover;" src="../upload/Tour Images/' . $row['img'] . '">
            <div style="display:flex;flex-direction:column;">
                <h3 style="font-size:14px;margin:0;color:#333;">' . $row['title'] . '</h3>
                <h5 style="font-size:10px;margin:0;color:#666;text-align: left;">' . $row['type'] . '</h5>
            </div>
        </a>';
        }
    } else {
        echo '<div class="dropdown-item" style="display:flex;padding:15px;font-size:13px">No results found</div>';
    }
}
