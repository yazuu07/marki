<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$db = "users";

// Check if 'user', 'password', and 'email' are set via POST
if (isset($_POST['user']) && isset($_POST['password']) && isset($_POST['email'])) {
    // Establish connection to the database
    $conn = mysqli_connect($host, $user, $password, $db);

    // Check if the connection was successful
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get the POST data
    $user = $_POST['user'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password before storing it (use password_hash)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL query using prepared statements to prevent SQL injection
    $sql = "INSERT INTO `admin` (`user`, `email`, `password`) VALUES (?, ?, ?)";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "sss", $user, $email, $hashed_password);

        // Execute the query
        $query = mysqli_stmt_execute($stmt);

        // Check if the query was successful
        if (!$query) {
            echo "Error: " . mysqli_error($conn);  // Show actual database error
        } else {
            echo "Registration Successful";  // Reflect correct message for registration
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error in preparing the query.";
    }

    // Close the connection
    mysqli_close($conn);
} else {
    echo "Required fields are missing!";
}
?>
