<?php
require_once 'connection.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $conn->real_escape_string($_POST['user']);
    $pass = $_POST['pass'];

    $sql = "SELECT * FROM giocatori WHERE username = '$user' AND password = '$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Username o password errati!"]);
    }
    exit;
}
?>