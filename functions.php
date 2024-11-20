<?php
// Function to handle user login
function handleLogin() {
    session_start(); // Start the session to store session variables

    // Initialize variables
    $email = $password = '';
    $emailErr = $passwordErr = '';
    $errorDetails = [];
    $loginError = '';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = trim($_POST['email']);  // Trim leading/trailing spaces
        $password = $_POST['password'];

        // Validate email
        if (empty($email)) {
            $emailErr = 'Email is required.';
            $errorDetails[] = $emailErr;
        } else {
            // Sanitize and validate email format
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = 'Invalid email format.';
                $errorDetails[] = $emailErr;
            }
        }

        // Validate password
        if (empty($password)) {
            $passwordErr = 'Password is required.';
            $errorDetails[] = $passwordErr;
        }

        // If no validation errors, proceed with login
        if (empty($emailErr) && empty($passwordErr)) {
            // Normalize email to lowercase
            $normalizedEmail = strtolower($email);

            // Call the connectDb function to get the database connection
            $conn = connectDb();

            // Prepare SQL query to check if the user exists
            $sql = "SELECT id, email, password FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql); // Prepare the SQL statement
            $stmt->bind_param("s", $normalizedEmail);  // Bind the email parameter to prevent SQL injection
            $stmt->execute(); // Execute the statement
            $result = $stmt->get_result(); // Get the result

            // Check if user exists
            if ($result->num_rows > 0) {
                // Fetch user data
                $user = $result->fetch_assoc();

                // Verify password (assuming passwords are hashed in the database)
                if (password_verify($password, $user['password'])) {
                    // If login is successful, store user info in session
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_id'] = $user['id'];

                    // Redirect to the dashboard
                    header('Location: dashboard.php');
                    exit;  // Stop further execution
                } else {
                    // Incorrect password
                    $errorDetails[] = 'Password is incorrect.';
                }
            } else {
                // Email not found
                $errorDetails[] = 'Email not found.';
            }

            // Close the prepared statement and connection
            $stmt->close();
            $conn->close();
        }

        // If there are errors, set the login error message
        if (!empty($errorDetails)) {
            $loginError = 'System Errors:';
        }
    }

    // Return the login status and errors
    return [
        'email' => $email,
        'password' => $password,
        'emailErr' => $emailErr,
        'passwordErr' => $passwordErr,
        'loginError' => $loginError,
        'errorDetails' => $errorDetails
    ];
}

// Function to establish the database connection
function connectDb() {
    // Database connection parameters
    $servername = "localhost";  // Database server (use your host if it's not localhost)
    $username = "root";         // Database username (adjust if necessary)
    $password = "";             // Database password (adjust if necessary)
    $dbname = "dct-ccs-finals";  // Your database name (replace with your actual database name)

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error); // Handle connection errors
    }

    return $conn;  // Return the connection object
}
?>
