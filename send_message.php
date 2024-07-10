<?php
$servername = "localhost";
$username = "dakh_root";
$password = "JbmMsDD1JemB6imc";
$dbname = "dakh_phongkhamdakhoanhatviet";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối tới cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    $sql = "INSERT INTO messages (sender_id, receiver_id, message, timestamp) 
            VALUES ('$sender_id', '$receiver_id', '$message', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "Đã gửi tin nhắn thành công.";
    } else {
        echo "Lỗi: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
