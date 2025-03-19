<?php
function getAllToursforAdmin($conn, $query = null, $page = 1, $limit = 100)
{
    $offset = ($page - 1) * $limit;
    $queryCondition = "";

    // Always filter tours by status (Active, Inactive, or Temporarily Closed)
    $queryCondition = "WHERE status IN ('Active', 'Inactive', 'Temporarily Closed')";

    // Add the search query condition if provided
    if ($query) {
        $query = "%" . $query . "%";
        $queryCondition .= " AND (title LIKE :query OR address LIKE :query)";
    }

    // SQL query with dynamic WHERE conditions
    $sql = "SELECT * FROM tours $queryCondition LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    if ($query) {
        $stmt->bindParam(':query', $query, PDO::PARAM_STR);
    }
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    // Fetch tours data
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count of tours (with the applied filters)
    $totalQuery = $conn->query("SELECT FOUND_ROWS() as total");
    $total = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];

    return ['tours' => $tours, 'total' => $total];
}


function getAllToursforOwners($conn, $user_id, $query = null)
{
    $sql = "SELECT * FROM tours WHERE user_id = $user_id";

    // Prepare the SQL statement
    if ($query) {
        // Use prepared statements to prevent SQL injection
        $sql .= " AND title LIKE :search OR address LIKE :search"; // Adjust fields as necessary
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
        WHERE t.status IN ('Active','Temporarily Closed') 
        GROUP BY t.id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllPopularTours($conn)
{
    // Updated SQL query to include average rating and review count
    $sql = "
WITH booking_visitors AS (
    SELECT t.id, 
           SUM(b.people) AS total_booking_visitors
    FROM tours t
    LEFT JOIN booking b ON t.id = b.tour_id AND b.status = 4
    GROUP BY t.id
),
visit_visitors AS (
    SELECT t.id, 
           COUNT(DISTINCT v.id) AS total_visit_visitors
    FROM tours t
    LEFT JOIN visit_records v ON t.id = v.tour_id
    GROUP BY t.id
)

SELECT t.id, 
       t.title,
       t.img,
       t.type,
       t.status,
       t.bookable,
       COALESCE(bv.total_booking_visitors, 0) + COALESCE(vv.total_visit_visitors, 0) AS total_visitors,
       COUNT(DISTINCT b.id) AS total_completed_bookings,
       IFNULL(AVG(r.rating), 0) AS average_rating,  -- Average rating
       IFNULL(COUNT(r.id), 0) AS review_count       -- Review count
FROM tours t 
LEFT JOIN booking b ON t.id = b.tour_id AND b.status = 4
LEFT JOIN booking_visitors bv ON t.id = bv.id
LEFT JOIN visit_visitors vv ON t.id = vv.id
LEFT JOIN review_rating r ON t.id = r.tour_id  -- Join with review_rating table
WHERE t.status IN ('Active')
GROUP BY t.id, t.title, bv.total_booking_visitors, vv.total_visit_visitors
ORDER BY total_visitors DESC, total_completed_bookings DESC LIMIT 15;
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
function getEventbyCode($conn, $event_code)
{
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_code = :event_code");
    $stmt->bindParam(":event_code", $event_code, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getEventByDate($conn)
{
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_date_start > CURDATE()");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserById($conn, $id)
{
    $stmt = $conn->prepare("SELECT CONCAT(firstname, ' ', lastname) as name, users.* FROM users WHERE id = :id");

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


function registerStatus($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT status, id, reason FROM tours WHERE user_id = :user_id LIMIT 1");
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch both 'status' and 'id' as an associative array
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
function checkBookingStatus($conn, $user_id, $tour_id)
{
    $stmt = $conn->prepare(
        "SELECT status, id FROM booking 
         WHERE user_id = :user_id 
         AND tour_id = :tour_id 
         ORDER BY date_created DESC 
         LIMIT 1"
    );

    // Bind parameters
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch a single record
}




function isBookable($conn, $tour_id)
{
    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = :tour_id AND bookable = 1");
    $stmt->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount() > 0;
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


function checkIfTemporarilyClosed($conn, $tour_id)
{
    $stmt = $conn->prepare("SELECT status FROM tours WHERE id = :tour_id");
    $stmt->bindParam(":tour_id", $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    $status = $stmt->fetchColumn();
    return $status === 'Temporarily Closed';
}


function checkIfTrusted($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT is_trusted FROM users WHERE id = :user_id");
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchColumn() === 1;
}

function fetchProfilePicture($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = :user_id");
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function checkIfPasswordIsNull($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = :user_id");
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() === null;
}