<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $User = $_POST['username'];
    $Password = $_POST['password'];

    $servername = "localhost";
    $username = "root";
    $db_password = "";
    $dbname = "kyourin";

    $conn = new mysqli($servername, $username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Query preparation error: " . $conn->error);
    }

    $stmt->bind_param("s", $User);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result !== null && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($Password, $row['password'])) {

                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

                echo '<script>
                        alert("Login successful!");
                        window.location.href = "index.php";
                      </script>';
                exit;
            } else {
                $message = "Invalid username or password. Please try again.";
            }
        } else {
            $message = "Invalid username or password. Please try again.";
        }
    } else {
        $message = "Error executing SQL query.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .animated-message {
            text-align: center;
            font-size: 24px;
            opacity: 0;
            animation: fadeIn 1s ease-in-out forwards;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="animated-message">
        <?php echo $message; ?>
    </div>
</body>

</html>