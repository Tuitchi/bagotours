<?php
function getAllToursforAdmin($conn, $query = null)
{
    $sql = "SELECT * FROM tours";

    // Prepare the SQL statement
    if ($query) {
        // Use prepared statements to prevent SQL injection
        $sql .= " WHERE title LIKE :search OR address LIKE :search"; // Adjust fields as necessary
        $stmt = $conn->prepare($sql);
        $searchTerm = "%" . $query . "%";
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    } else {
        $stmt = $conn->prepare($sql);
    }

    // Execute the prepared statement
    $stmt->execute();

    // Fetch all results
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function getAllTours($conn)
{
    // Updated SQL query to include average rating and review count
    $sql = "
        SELECT t.*, 
               IFNULL(AVG(r.rating), 0) AS average_rating, 
               IFNULL(COUNT(r.id), 0) AS review_count 
        FROM tours t 
        LEFT JOIN review_rating r ON t.id = r.tour_id 
        WHERE t.status = 1 
        GROUP BY t.id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getTourById($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getTourImageById($conn, $id)
{
    $stmt = $conn->prepare("
        SELECT 
            t.img AS combined_image,
            t.title
        FROM 
            tours t
        WHERE 
            t.id = :id

        UNION ALL

        SELECT 
            img.img AS combined_image,
            t.title
        FROM 
            tours_image img 
        JOIN 
            tours t ON t.id = img.tour_id
        WHERE 
            t.id = :id
    ");

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        echo "Error executing query: ";
        print_r($stmt->errorInfo());
        return false; // Handle as needed
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as an array

    if (!$result) {
        echo "No results found for ID: $id";
    }

    return $result; // Return all results
}


function getUserById($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAverageRating($conn, $tour_id)
{
    $stmt = $conn->prepare("
        SELECT COALESCE(AVG(r.rating), 0) AS average_rating
        FROM review_rating r
        WHERE r.tour_id = :tour_id");
    $stmt->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() ?: 0;
}

function getTourImages($conn, $tourId)
{
    $stmt = $conn->prepare("SELECT * FROM tours_image WHERE tour_id = :tour_id");
    $stmt->bindParam(":tour_id", $tourId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllPopular($conn)
{
    $stmt = $conn->prepare("SELECT t.id, t.title, t.address, t.type, t.img, COUNT(rr.id) AS rating_count 
        FROM tours t 
        INNER JOIN review_rating rr ON t.id = rr.tour_id 
        GROUP BY t.id 
        ORDER BY rating_count DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Register Owner

function registerExpiry($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT expiry, id FROM tours WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $date = date('Y-m-d');

    foreach ($tours as $tour) {
        if ($tour['expiry'] == $date) {
            $deleteStmt = $conn->prepare("DELETE FROM tours WHERE user_id = :user_id AND id = :tour_id");
            $deleteStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $deleteStmt->bindParam(':tour_id', $tour['id'], PDO::PARAM_INT);

            if ($deleteStmt->execute()) {
                require_once 'func.php';
                createNotification($conn, $user_id, $tour['id'], "You can register as an owner again.", "form.php", "Upgrade cancelled");
            }
        }
    }
}

function registerStatus($user_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT status FROM tours WHERE user_id = ? LIMIT 1");
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}


function emailAlreadyUsed($conn, $email)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

function usernameAlreadyUsed($conn, $username)
{
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

function getNotificationsCount($conn)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM notification WHERE user_id = :user_id AND unread = 1");
    $stmt->bindParam(":user_id", $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() ?: 0;
}


// booking
function isAlreadyBooked($conn, $user_id, $tour_id)
{
    // Prepare the SQL statement with proper parentheses to ensure correct logic
    $stmt = $conn->prepare(
        "SELECT * FROM booking 
         WHERE user_id = :user_id 
         AND tour_id = :tour_id 
         AND (status = 1 OR status = 3 OR status = 0)"
    );

    // Bind parameters
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);

    // Execute the statement
    $stmt->execute();

    // Return true if there are any matching rows
    return $stmt->rowCount() > 0;
}


function isBookable($conn, $tour_id)
{
    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = :tour_id AND bookable = 1");
    $stmt->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

function bookApproval($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT date_sched FROM booking WHERE user_id = :user_id AND status = 1");
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $sched = $stmt->fetchColumn();

    if ($sched) {
        $schedDate = new DateTime($sched);
        $schedDate->modify('-2 day');

        $today = new DateTime();

        if ($schedDate->format('Y-m-d') === $today->format('Y-m-d')) {
            return true;
        }
    }
    return false;
}

function userValidation($pageRole)
{
    if (isset($_SESSION['user_id'])) {
        if (!empty($_SESSION['role'])) {
            if ($_SESSION['role'] !== $pageRole) {
                session_unset();
                session_destroy();
                header("Location: ../index.php");
                exit;
            }
        } else {
            session_unset();
            session_destroy();
            header("Location: ../index.php");
            exit;
        }
    } else {
        header("Location: ../index.php");
        exit;
    }
}
