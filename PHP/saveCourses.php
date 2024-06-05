<?php
    header('Access-Control-Allow-Origin: http://localhost:3000');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

$data = file_get_contents("php://input");
$ddd = json_decode($data,true);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "courses";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}   

$cname = $conn->real_escape_string($ddd['cname']);
$isConfirmed = (int)$ddd['isConfirmed'];

$sql = "INSERT INTO tbl (text, confirmed) VALUES ('$cname', $isConfirmed)";

if (mysqli_query($conn, $sql)) {
    $last_id = mysqli_insert_id($conn);
    echo json_encode(["id" => $last_id, "text" => $cname, "confirmed" => (bool)$isConfirmed]);
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
