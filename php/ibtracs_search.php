<?php
require_once 'db.php';
require_once 'function.php';

$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

// Get pagination and filter parameters from AJAX
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

// Build WHERE clause from filters
$where = [];
if (!empty($_GET['name']))         $where[] = "name LIKE '%" . $conn->real_escape_string($_GET['name']) . "%'";
if (!empty($_GET['year']))         $where[] = "season = " . intval($_GET['year']);
if (!empty($_GET['wind_min']))     $where[] = "wind_kts >= " . intval($_GET['wind_min']);
if (!empty($_GET['wind_max']))     $where[] = "wind_kts <= " . intval($_GET['wind_max']);
if (!empty($_GET['pressure_max'])) $where[] = "pressure_mb <= " . intval($_GET['pressure_max']);
if (!empty($_GET['sid']))          $where[] = "sid = '" . $conn->real_escape_string($_GET['sid']) . "'";
if (!empty($_GET['basin']))        $where[] = "basin LIKE '%" . $conn->real_escape_string($_GET['basin']) . "%'";

// ✅ Optional filters
if (!empty($_GET['agency']))       $where[] = "agency LIKE '%" . $conn->real_escape_string($_GET['agency']) . "%'";
if (!empty($_GET['storm_type']))   $where[] = "storm_type LIKE '%" . $conn->real_escape_string($_GET['storm_type']) . "%'";
if (!empty($_GET['track_type']))   $where[] = "track_type LIKE '%" . $conn->real_escape_string($_GET['track_type']) . "%'";
if (!empty($_GET['nature']))       $where[] = "nature LIKE '%" . $conn->real_escape_string($_GET['nature']) . "%'";
if (!empty($_GET['lat_min']))      $where[] = "lat >= " . floatval($_GET['lat_min']);
if (!empty($_GET['lat_max']))      $where[] = "lat <= " . floatval($_GET['lat_max']);
if (!empty($_GET['lon_min']))      $where[] = "lon >= " . floatval($_GET['lon_min']);
if (!empty($_GET['lon_max']))      $where[] = "lon <= " . floatval($_GET['lon_max']);

// Main SQL query
$sql = "SELECT * FROM IBTrACS_Storms";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY season DESC, timestamp DESC LIMIT $limit OFFSET $offset";

// Fetch results
$data = [];
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Include view link
        $row['view_link'] = 'ibtracs_view.php?sid=' . urlencode($row['sid']);
        $data[] = $row;
    }
}

// Count total rows for pagination
$countSql = "SELECT COUNT(*) AS total FROM IBTrACS_Storms";
if (!empty($where)) {
    $countSql .= " WHERE " . implode(" AND ", $where);
}
$countResult = $conn->query($countSql);
$totalRows = ($countResult && $row = $countResult->fetch_assoc()) ? intval($row['total']) : 0;
$totalPages = ceil($totalRows / $limit);

// Output as JSON for AJAX
header('Content-Type: application/json');
echo json_encode([
    'data' => $data,
    'total_pages' => $totalPages,
    'current_page' => $page,
]);
?>