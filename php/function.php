<?php
include("db.php"); // Assuming this contains your DBConn class

class DBFunc
{
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // --- REGISTER USER ---
    public function registerUser($username, $email, $password)
    {
        $pwd = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $pwd);

        if ($stmt->execute()) {
            header("Location: register.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // --- LOGIN USER ---
    public function loginUser($email, $pwd)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($pwd, $user["password"])) {
                $_SESSION['username'] = $user['username'];
                header('Location: index.php');
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "User not found.";
        }
    }

    // --- LOGOUT USER ---
    public function logoutUser()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }

    // --- SHOW SINGLE CYCLONE ENTRY ---
    public function showDatabase($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tcdatabase WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } else {
            echo "Query error: " . $stmt->error;
            return null;
        }
    }

    // --- INSERT CYCLONE DATA ---
    public function insertDatabase($storm_id, $name, $basin, $wind_speed, $pressure, $start_date, $end_date, $fatalities, $damages, $ace, $file)
    {
        $imagePath = $this->handleImageUpload($file);
        $stmt = $this->conn->prepare("INSERT INTO tcdatabase (storm_id, name, basin, wind_speed, pressure, start_date, end_date, fatalities, damages, ace, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiisssiss", $storm_id, $name, $basin, $wind_speed, $pressure, $start_date, $end_date, $fatalities, $damages, $ace, $imagePath);

        if ($stmt->execute()) {
            header("Location: tc_admin.php");
            exit();
        } else {
            echo "Insert error: " . $stmt->error;
        }
    }

    // --- UPDATE CYCLONE DATA ---
    public function updateDatabase($storm_id, $name, $basin, $wind_speed, $pressure, $start_date, $end_date, $fatalities, $damages, $ace, $id, $file = null)
    {
        $imagePath = null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->handleImageUpload($file);
        }

        if ($imagePath) {
            $stmt = $this->conn->prepare("UPDATE tcdatabase SET storm_id = ?, name = ?, basin = ?, wind_speed = ?, pressure = ?, start_date = ?, end_date = ?, fatalities = ?, damages = ?, ace = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sssiisssissi", $storm_id, $name, $basin, $wind_speed, $pressure, $start_date, $end_date, $fatalities, $damages, $ace, $imagePath, $id);
        } else {
            $stmt = $this->conn->prepare("UPDATE tcdatabase SET storm_id = ?, name = ?, basin = ?, wind_speed = ?, pressure = ?, start_date = ?, end_date = ?, fatalities = ?, damages = ?, ace = ? WHERE id = ?");
            $stmt->bind_param("sssiisssiii", $storm_id, $name, $basin, $wind_speed, $pressure, $start_date, $end_date, $fatalities, $damages, $ace, $id);
        }

        if ($stmt->execute()) {
            header("Location: tc_admin.php");
            exit();
        } else {
            echo "Update error: " . $stmt->error;
        }
    }

    // --- DELETE CYCLONE DATA ---
    public function deleteDatabase($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tcdatabase WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: tc_admin.php");
            exit();
        } else {
            echo "Delete error: " . $stmt->error;
        }
    }

    // --- GET ALL TORNADO RECORDS ---
    public function getAllTornadoes($search = '')
    {
        $search = $this->conn->real_escape_string($search);
        $where = '';

        if (!empty($search)) {
            $where = "WHERE tor_location LIKE '%$search%' OR fujita_rank LIKE '%$search%'";
        }

        $sql = "SELECT * FROM tornado_db $where ORDER BY date DESC";
        return $this->conn->query($sql);
    }

    // --- INSERT TORNADO RECORD ---
    public function insertTornado($tor_location, $date, $fujita_rank, $wind_speed, $max_width, $distance, $duration, $file)
    {
        $imagePath = $this->handleImageUpload($file);
        $stmt = $this->conn->prepare("INSERT INTO tornado_db (tor_location, date, fujita_rank, wind_speed, max_width, distance, duration, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssddss", $tor_location, $date, $fujita_rank, $wind_speed, $max_width, $distance, $duration, $imagePath);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Insert Error: " . $stmt->error;
            return false;
        }
    }

    // --- UPDATE TORNADO RECORD ---
    public function updateTornado($id, $tor_location, $date, $fujita_rank, $wind_speed, $max_width, $distance, $duration, $file = null)
    {
        $imagePath = null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->handleImageUpload($file);
        }

        if ($imagePath) {
            $stmt = $this->conn->prepare("UPDATE tornado_db SET tor_location = ?, date = ?, fujita_rank = ?, wind_speed = ?, max_width = ?, distance = ?, duration = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssssddssi", $tor_location, $date, $fujita_rank, $wind_speed, $max_width, $distance, $duration, $imagePath, $id);
        } else {
            $stmt = $this->conn->prepare("UPDATE tornado_db SET tor_location = ?, date = ?, fujita_rank = ?, wind_speed = ?, max_width = ?, distance = ?, duration = ? WHERE id = ?");
            $stmt->bind_param("ssssddsi", $tor_location, $date, $fujita_rank, $wind_speed, $max_width, $distance, $duration, $id);
        }

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Update Error: " . $stmt->error;
            return false;
        }
    }

    // --- DELETE TORNADO RECORD ---
    public function deleteTornado($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM tornado_db WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Delete Error: " . $stmt->error;
            return false;
        }
    }

    // --- GET SINGLE TORNADO RECORD BY ID ---
    public function getTornadoById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tornado_db WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // --- HANDLE IMAGE UPLOAD ---
    private function handleImageUpload($file)
    {
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = basename($file['name']);
        $targetFile = $uploadDir . time() . '_' . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $targetFile;
        }

        return null;
    }

    

    public function insertIBTracsStorm($data)
{
    $stmt = $this->conn->prepare("
        INSERT INTO IBTrACS_Storms (
            sid, name, basin, agency, lat, lon, wind_kts, pressure_mb,
            timestamp, storm_type, nature, track_type, track_points,
            start_date, end_date, landfall_count, max_wind_kts,
            min_pressure_mb, comments, storm_num, season_year,
            storm_classification, source_notes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssssddiidsssissiiisiis",
        $data['sid'], $data['name'], $data['basin'], $data['agency'],
        $data['lat'], $data['lon'], $data['wind_kts'], $data['pressure_mb'],
        $data['timestamp'], $data['storm_type'], $data['nature'],
        $data['track_type'], $data['track_points'], $data['start_date'],
        $data['end_date'], $data['landfall_count'], $data['max_wind_kts'],
        $data['min_pressure_mb'], $data['comments'], $data['storm_num'],
        $data['season_year'], $data['storm_classification'], $data['source_notes']
    );

    if ($stmt->execute()) {
        return true;
    } else {
        echo "Insert Error: " . $stmt->error;
        return false;
    }
}

public function updateIBTracsStorm($id, $data) {
    $stmt = $this->conn->prepare("
        UPDATE IBTrACS_Storms SET
            sid = ?, name = ?, basin = ?, agency = ?, lat = ?, lon = ?,
            wind_kts = ?, pressure_mb = ?, timestamp = ?, storm_type = ?,
            nature = ?, track_type = ?, track_points = ?, start_date = ?, end_date = ?
        WHERE id = ?
    ");

    $stmt->bind_param("ssssddiissssiissi",
        $data['sid'],
        $data['name'],
        $data['basin'],
        $data['agency'],
        $data['lat'],
        $data['lon'],
        $data['wind_kts'],
        $data['pressure_mb'],
        $data['timestamp'],
        $data['storm_type'],
        $data['nature'],
        $data['track_type'],
        $data['track_points'],
        $data['start_date'],
        $data['end_date'],
        $id
    );

    $stmt->execute();
    $stmt->close();
}

public function getIBTracsStorms($limit = 100)
{
    $sql = "SELECT * FROM IBTrACS_Storms ORDER BY timestamp DESC LIMIT ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result();
}

public function deleteIBTracsStorm($sid)
{
    $stmt = $this->conn->prepare("DELETE FROM IBTrACS_Storms WHERE sid = ?");
    $stmt->bind_param("s", $sid);
    if ($stmt->execute()) {
        return true;
    } else {
        echo "Delete Error: " . $stmt->error;
        return false;
    }
}

public function getIBTracsStormBySid($sid)
{
    $stmt = $this->conn->prepare("SELECT * FROM IBTrACS_Storms WHERE sid = ?");
    $stmt->bind_param("s", $sid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

public function getTopIntensityByStorm($sid)
{
    $stmt = $this->conn->prepare("
        SELECT * FROM IBTrACS_Storms 
        WHERE sid = ? 
        ORDER BY wind_kts DESC, pressure_mb ASC 
        LIMIT 1
    ");
    $stmt->bind_param("s", $sid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

public function getIBTracsByYear($year)
{
    $stmt = $this->conn->prepare("
        SELECT * FROM IBTrACS_Storms 
        WHERE season_year = ?
        ORDER BY timestamp ASC
    ");
    $stmt->bind_param("i", $year);
    $stmt->execute();
    return $stmt->get_result();
}

public function insertTrackPoint($data)
{
    $stmt = $this->conn->prepare("
        INSERT INTO IBTrACS_Tracks 
        (sid, point_order, lat, lon, wind_kts, pressure_mb, timestamp, storm_type, nature)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "siddiisss",
        $data['sid'], $data['point_order'], $data['lat'], $data['lon'],
        $data['wind_kts'], $data['pressure_mb'], $data['timestamp'],
        $data['storm_type'], $data['nature']
    );
    return $stmt->execute();
}

public function getStormsByBasin($basinCode)
{
    $stmt = $this->conn->prepare("SELECT * FROM IBTrACS_Storms WHERE basin = ?");
    $stmt->bind_param("s", $basinCode);
    $stmt->execute();
    return $stmt->get_result();
}

public function insertLifecycleEvent($data)
{
    $stmt = $this->conn->prepare("
        INSERT INTO Storm_Lifecycle (sid, stage, start_time, end_time, description)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssss",
        $data['sid'], $data['stage'], $data['start_time'],
        $data['end_time'], $data['description']
    );
    return $stmt->execute();
}

// public function insertProxyStorm($data)
// {
//     $stmt = $this->conn->prepare("
//         INSERT INTO Proxy_Storms (site_name, region, proxy_type, storm_date_range, estimated_intensity, notes, source_reference)
//         VALUES (?, ?, ?, ?, ?, ?, ?)
//     ");
//     $stmt->bind_param("sssssss",
//         $data['site_name'], $data['region'], $data['proxy_type'],
//         $data['storm_date_range'], $data['estimated_intensity'],
//         $data['notes'], $data['source_reference']
//     );
//     return $stmt->execute();
// }

public function calculateACEForStorm($sid)
{
    $stmt = $this->conn->prepare("
        SELECT wind_kts FROM IBTrACS_Tracks 
        WHERE sid = ? AND wind_kts >= 35
    ");
    $stmt->bind_param("s", $sid);
    $stmt->execute();
    $result = $stmt->get_result();

    $ace = 0;
    while ($row = $result->fetch_assoc()) {
        $v = $row['wind_kts'];
        $ace += pow($v, 2);
    }

    // ACE divided by 10,000 (6-hourly obs × knots² / 10,000)
    return round($ace / 10000, 2);
}

public function getStormCountByYear()
{
    $sql = "SELECT season_year, COUNT(*) as count FROM IBTrACS_Storms GROUP BY season_year ORDER BY season_year DESC";
    return $this->conn->query($sql);
}

public function getStrongestStorms($limit = 10)
{
    $sql = "SELECT * FROM IBTrACS_Storms ORDER BY max_wind_kts DESC, min_pressure_mb ASC LIMIT ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result();
}

// public function insertPaleoStorm($data)
// {
//     $stmt = $this->conn->prepare("
//         INSERT INTO Proxy_Storms (
//             site, proxy_type, start_year, end_year, frequency_estimate,
//             uncertainty_years, source, lat, lon
//         ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
//     ");

//     $stmt->bind_param(
//         "ssiiidssd",
//         $data['location_found'],
//         $data['source_type'],
//         $data['year'],      // start_year
//         $data['year'],      // end_year (same as start for now)
//         // $dummyFreq,         // use placeholder if not inputted
//         // $dummyUncertainty,  // use placeholder if not inputted
//         $data['description'],
//         // $dummyLat,
//         // $dummyLon
//     );

//     // Fallback dummy values (you can extend the form later)
//     // $dummyFreq = 1.0;
//     // $dummyUncertainty = 0;
//     // $dummyLat = 0.0;
//     // $dummyLon = 0.0;

//     $stmt->execute();
//     $stmt->close();
// }

// public function deletePaleoStorm($id)
// {
//     $stmt = $this->conn->prepare("DELETE FROM Proxy_Storms WHERE id = ?");
//     $stmt->bind_param("i", $id);
//     $stmt->execute();
//     $stmt->close();
// }

// public function showPaleoStorm($id)
// {
//     $stmt = $this->conn->prepare("SELECT * FROM Proxy_Storms WHERE id = ?");
//     $stmt->bind_param("i", $id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $storm = $result->fetch_assoc();
//     $stmt->close();
//     return $storm;
// }

}
