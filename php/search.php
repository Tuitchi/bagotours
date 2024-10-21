<?php
include '../include/db_conn.php';

if (isset($_POST['query'])) {
    $query = htmlspecialchars($_POST['query'], ENT_QUOTES, 'UTF-8');
    $stmt = $conn->prepare("SELECT id, img, title, type FROM tours WHERE title LIKE :searchTerm LIMIT 10");
    $searchTerm = "%" . $query . "%";
    $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        foreach ($result as $row) {
            echo '<a class="dropdown-item" style="font-size:15px;display:flex;align-items:center;gap: 15px;text-decoration:none;margin-bottom:5px" href="tour?id=' .base64_encode($row['id'] . $salt) . '">
                    <img style="width:40px;height:40px;border-radius: 4px;object-fit: cover;" src="upload/Tour Images/' . $row['img'] . '">
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
