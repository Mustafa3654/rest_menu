<?php 
include "connection.php";
session_start();

if(isset($_POST['submit'])){
    $name = $_POST["username"];
    $pass = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND userpassword = ?");
    $stmt->bind_param("ss", $name, $pass);
    $stmt->execute();
    $result = $stmt->get_result();
      
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION["user_name"] = $user['username'];
        $_SESSION["isAdmin"] = ($user['isAdmin'] == 1);
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Wrong Username or Password. Please try again.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style/login.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Login</h1>
        <?php if(isset($error_message)): ?>
            <div style='text-align:center; margin-bottom:20px; color: #e11d48; font-weight: 600;'>
                <p><?php echo $error_message; ?></p>
                <script>
                    setTimeout(function() {
                        window.location.assign("login.php");
                    }, 3000);
                </script>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter Username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" required>
            <button type="submit" name="submit" value="submit" class="submit">Login</button>
        </form> 
        <br>
        <div class="button-grid"> 
        <a href="index.php" class="back-button backk">Back</a>
        </div>
    </div>
</body>
</html>
