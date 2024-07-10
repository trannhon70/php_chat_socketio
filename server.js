const mysql = require('mysql');
const { Server } = require('socket.io');

// Kết nối đến cơ sở dữ liệu MySQL
const dbConnection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '', // Nếu không có mật khẩu, để trống
    database: 'socket_io'

    // host: 'localhost',
    // user: 'dakh_root',
    // password: 'JbmMsDD1JemB6imc', // Nếu không có mật khẩu, để trống
    // database: 'dakh_phongkhamdakhoanhatviet'
});

dbConnection.connect((err) => {
    if (err) {
        console.error('Kết nối cơ sở dữ liệu thất bại:', err.stack);
        return;
    }
    console.log('Đã kết nối đến cơ sở dữ liệu với id', dbConnection.threadId);
});

const io = new Server({
    serveClient: false,
    cors: {
        origin: "*", // Cho phép tất cả các origin truy cập
        methods: ["GET", "POST", "DELETE", "UPDATE"] // Cho phép các phương thức GET và POST
    }
});

io.on('connection', (socket) => {
    console.log('Đã kết nối tới một client');

    socket.on('message', (msg) => {
        try {
            let parsedMsg = msg; // Initialize parsedMsg with msg directly
    
            // Check if msg is a string; if so, parse it as JSON
            if (typeof msg === 'string') {
                parsedMsg = JSON.parse(msg);
            }
    
            let { sender_id, receiver_id, message } = parsedMsg;
    
            // Chuẩn bị câu lệnh SQL để chèn dữ liệu vào bảng messages
            const sql = `INSERT INTO messages (sender_id, receiver_id, message, timestamp) 
                         VALUES (?, ?, ?, NOW())`;
            const values = [sender_id, receiver_id, message];
    
            // Thực thi câu lệnh SQL để lưu tin nhắn vào cơ sở dữ liệu
            dbConnection.query(sql, values, (error, results, fields) => {
                if (error) {
                    console.error('Lỗi khi lưu tin nhắn vào cơ sở dữ liệu:', error);
                } else {
                    // Gửi lại tin nhắn cho tất cả các client đang kết nối
                    io.emit('message', parsedMsg);
                }
            });
        } catch (error) {
            console.error('Dữ liệu JSON không hợp lệ:', error);
        }
    });
    

    socket.on('disconnect', () => {
        console.log('Ngắt kết nối từ một client');
    });
});

const PORT = 1337;
io.listen(PORT);
console.log(`Máy chủ Socket.IO đang chạy trên cổng ${PORT}`);
