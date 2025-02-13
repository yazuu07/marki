<?php
session_start();

// Handle form submission (check user credentials)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $host = "localhost";
    $db_user = "root"; // Use the correct database username
    $db_password = ""; // Use the correct database password
    $db = "users";

    // Get the POST data
    $username = $_POST['user']; // Changed variable name to avoid confusion
    $password_input = $_POST['password'];

    // Establish connection to the database
    $conn = mysqli_connect($host, $db_user, $db_password, $db);

    // Check if the connection was successful
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare the SQL query to fetch the user details based on username/email
    $sql = "SELECT * FROM admin WHERE user = ? OR email = ?";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "ss", $username, $username);

        // Execute the query
        mysqli_stmt_execute($stmt);

        // Get the result
        $result = mysqli_stmt_get_result($stmt);

        // Check if the user exists
        if ($row = mysqli_fetch_assoc($result)) {
            // Verify the password  
            if (password_verify($password_input, $row['password'])) {
                // Start a session and store user info for the dashboard
                $_SESSION['user'] = $row['user'];
                $_SESSION['email'] = $row['email'];
                // Do not store the password in the session for security reasons
                // $_SESSION['password'] = $row['password'];

                // Redirect to the dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Invalid password
                header("Location: login.php?error=invalid_password");
                exit();
            }
        } else {
            // No user found
            header("Location: login.php?error=no_user_found");
            exit();
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error in preparing the query.";
    }

    // Close the connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="d1login.css">
</head>
<body>
    <form action="login.php" method="post">
        <label for="username">Username or Email:</label>
        <input type="text" name="user" id="username" required><br> 
                <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>

        <button type="submit">Login</button>
        <p>No Account? <a href="registration.php">Register Now</a></p>
    </form>

    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;">
            <?php
                if ($_GET['error'] == 'invalid_password') {
                    echo 'Invalid password!';
                } elseif ($_GET['error'] == 'no_user_found') {
                    echo 'No such user found!';
                }
            ?>
        </p>
    <?php endif; ?>
</body>
</html>