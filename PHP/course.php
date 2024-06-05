<?php

    header('Access-Control-Allow-Origin: http://localhost:3000');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day

echo $_SERVER['HTTP_ORIGIN'];

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "courses";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['action'])) {
        if ($data['action'] == 'edit') {
            $old_text = $conn->real_escape_string($data['old_text']);
            $new_text = $conn->real_escape_string($data['new_text']);

            $sql = "UPDATE tbl SET text='$new_text' WHERE text='$old_text'";
            if ($conn->query($sql) !== TRUE) {
                echo "Error updating course: " . $conn->error;
            } else {
                echo json_encode(["id" => $data['id'], "text" => $new_text, "confirmed" => $data['confirmed']]);
            }
        } elseif ($data['action'] == 'delete') {
            $text = $conn->real_escape_string($data['text']);

            $sql = "DELETE FROM tbl WHERE text='$text'";
            if ($conn->query($sql) !== TRUE) {
                echo "Error deleting course: " . $conn->error;
            } else {
                echo "Course deleted successfully";
            }
        } elseif ($data['action'] == 'confirm') {
            $text = $conn->real_escape_string($data['text']);
            $confirmed = $data['confirmed'] ? 1 : 0;

            $sql = "UPDATE tbl SET confirmed=$confirmed WHERE text='$text'";
            if ($conn->query($sql) !== TRUE) {
                echo "Error updating confirmation status: " . $conn->error;
            } else {
                echo json_encode(["id" => $data['id'], "text" => $text, "confirmed" => true]);
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT * FROM tbl";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $courses = array();
        while ($row = $result->fetch_assoc()) {
            $row['confirmed'] = (bool)$row['confirmed'];
            $courses[] = $row;
        }
        echo json_encode($courses);
    } else {
        echo "No courses found";
    }
}

$conn->close();
?>
