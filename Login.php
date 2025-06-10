<?php
session_start();
require_once "db.php";

$error = "";  // Variable to hold error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT u.*, r.RoleName
        FROM Users u
        LEFT JOIN Roles r ON u.UserID = r.UserID
        WHERE u.Email = :email";


    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);

    if ($stmt->execute()) {
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the password matches
            if (password_verify($password, $user["Passwords"])) {
                // Store session variables if login is successful
                $_SESSION["UserID"] = $user["UserID"];
                $_SESSION["Firstname"] = $user["Firstname"];
                $_SESSION["Role"] = $user["RoleName"];

                // Redirect to Index.php after successful login
                header("Location: Index.php");
                exit();
            } else {
                $error = "Incorrect email or password.";  // Password mismatch
            }
        } else {
            $error = "Incorrect email or password.";  // User not found or invalid email
        }
    } else {
        $error = "Something went wrong. Please try again later.";  // Database issue
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }
        .login-box {
            background-color: white;
            padding: 30px;
            max-width: 400px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .login-box h2 {
            margin-bottom: 25px;
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-size: 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .register-link {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="main-content">
        <div class="login-box">
            <h2>Login</h2>

            <!-- Display error message if any -->
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="Login.php" method="POST">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>

                <button type="submit">Click to Sign In</button>
            </form>
            <div class="register-link">
                Don't have an account? <a href="Register.php">Register here</a>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
</body>
</html>
