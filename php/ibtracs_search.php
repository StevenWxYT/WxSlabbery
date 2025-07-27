<?php
require_once 'function.php';

$db = new DBFunc($conn);

// Get pagination and filter parameters from AJAX
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

$where = [];
if (!empty($_GET['name'])) $where[] = "name LIKE '%" . $conn->real_escape_string($_GET['name']) . "%'";
if (!empty($_GET['year'])) $where[] = "season = " . intval($_GET['year']);
if (!empty($_GET['wind_min'])) $where[] = "wind_kts >= " . intval($_GET['wind_min']);
if (!empty($_GET['wind_max'])) $where[] = "wind_kts <= " . intval($_GET['wind_max']);
if (!empty($_GET['pressure_max'])) $where[] = "pressure_mb <= " . intval($_GET['pressure_max']);
if (!empty($_GET['sid'])) $where[] = "sid = '" . $conn->real_escape_string($_GET['sid']) . "'";
if (!empty($_GET['basin'])) $where[] = "basin LIKE '%" . $conn->real_escape_string($_GET['basin']) . "%'";

$sql = "SELECT * FROM IBTrACS_Storms";
if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY season DESC, timestamp DESC LIMIT $limit OFFSET $offset";

$data = [];
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Get total number of matching rows for pagination
$countSql = "SELECT COUNT(*) AS total FROM IBTrACS_Storms";
if (!empty($where)) $countSql .= " WHERE " . implode(" AND ", $where);
$countResult = $conn->query($countSql);
$totalRows = ($countResult && $row = $countResult->fetch_assoc()) ? intval($row['total']) : 0;
$totalPages = ceil($totalRows / $limit);

// Return JSON
header('Content-Type: application/json');
echo json_encode([
    'data' => $data,
    'total_pages' => $totalPages,
    'current_page' => $page,
]);
