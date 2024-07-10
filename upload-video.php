<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Thay username bằng tên người dùng cơ sở dữ liệu
$password = ""; // Thay password bằng mật khẩu của người dùng cơ sở dữ liệu
$dbname = "socket_io"; // Thay database bằng tên cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}
// Thư mục lưu trữ video
$uploadDir = 'videos/';

// Kiểm tra xem có dữ liệu gửi đến không
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['videoFile'])) {
    $file = $_FILES['videoFile'];

    // Kiểm tra lỗi khi tải lên
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "Lỗi khi tải lên video.";
        exit();
    }

    // Lấy thông tin từ tệp tải lên
    $fileName = basename($file['name']);
    $filePath = $uploadDir . $fileName;

    // Di chuyển tệp tải lên vào thư mục lưu trữ trên server
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Kết nối đến cơ sở dữ liệu
       

        // Chuẩn bị câu lệnh SQL để chèn dữ liệu
        $sql = "INSERT INTO videos (filename, filepath) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $fileName, $filePath);

        // Thực thi câu lệnh chèn dữ liệu
        if ($stmt->execute()) {
            echo "Đã lưu video thành công vào: " . $filePath;
        } else {
            echo "Lỗi khi lưu video vào cơ sở dữ liệu.";
        }

        // Đóng kết nối
        $stmt->close();
        $conn->close();
    } else {
        echo "Lỗi khi lưu video.";
    }
} else {
    echo "Không có dữ liệu video được gửi lên.";
}

$sql = "SELECT filename, filepath FROM videos";
$result = $conn->query($sql);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video</title>
</head>
<body>
<?php if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $videoName = $row['filename'];
        $videoPath = $row['filepath'];
?>

     <div id="videoContainer">
        <video id="myVideo" width="320" height="240" controls>
            <source src='<?php echo $videoPath; ?>' type='video/mp4'>
            Your browser does not support the video tag.
        </video>
        <div id="customControls">
            <button id="playPauseBtn">Play/Pause</button>
            <button id="stopBtn">Stop</button>
            <button id="muteBtn">Mute</button>
        </div>
    </div>
    <?php
    }
} else {
    echo "No videos found.";
} ?>
    <form id="uploadForm" enctype="multipart/form-data" action="upload-video.php" method="POST">
        <input type="file" id="fileInput" name="videoFile" accept="video/*">
        <button type="submit">Upload Video</button>
    </form>
    <script>
        const video = document.getElementById('myVideo');
        const customControls = document.getElementById('customControls');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const stopBtn = document.getElementById('stopBtn');
        const muteBtn = document.getElementById('muteBtn');

        // Hiển thị các điều khiển tùy chỉnh khi video được tải
        video.addEventListener('loadedmetadata', () => {
            customControls.style.display = 'block';
        });

        // Xử lý sự kiện khi nhấn nút Play/Pause
        playPauseBtn.addEventListener('click', () => {
            if (video.paused || video.ended) {
                video.play();
                playPauseBtn.textContent = 'Pause';
            } else {
                video.pause();
                playPauseBtn.textContent = 'Play';
            }
        });

        // Xử lý sự kiện khi nhấn nút Stop
        stopBtn.addEventListener('click', () => {
            video.pause();
            video.currentTime = 0;
            playPauseBtn.textContent = 'Play';
        });

        // Xử lý sự kiện khi nhấn nút Mute
        muteBtn.addEventListener('click', () => {
            if (video.muted) {
                video.muted = false;
                muteBtn.textContent = 'Mute';
            } else {
                video.muted = true;
                muteBtn.textContent = 'Unmute';
            }
        });
    </script>
</body>
</html>

