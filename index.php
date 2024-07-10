<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Form with Socket.IO</title>
    <!-- Include Socket.IO library -->
    <script src="https://cdn.socket.io/4.1.3/socket.io.min.js"></script>
</head>
<body>
    <div id="messages"></div>
    <form id="chatForm">
        <input type="text" id="messageInput" placeholder="Nhập tin nhắn của bạn" required>
        <button type="submit">Gửi</button>
    </form>

    <script>
        // Thay đổi URL thành địa chỉ IP hoặc tên miền của máy chủ Socket.IO
        const socket = io('http://localhost:1337');

        socket.on('connect', function() {
            console.log('Đã kết nối tới máy chủ Socket.IO');
        });

        socket.on('message', function(message) {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML += `<p><strong>${message.sender_id}:</strong> ${message.message}</p>`;
        });

        socket.on('disconnect', function() {
            console.log('Đã đóng kết nối tới máy chủ Socket.IO');
        });

        const form = document.getElementById('chatForm');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const messageInput = document.getElementById('messageInput').value;

            const messageObj = {
                sender_id: 1, // Thay đổi thành ID của người gửi
                receiver_id: 2, // Thay đổi thành ID của người nhận
                message: messageInput
            };

            socket.emit('message', messageObj); // Gửi tin nhắn tới máy chủ

            form.reset(); // Xóa nội dung trong input sau khi gửi
        });
    </script>
</body>
</html>
