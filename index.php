<?php
// Include the login function and database connection function
// Use include_once or require_once to prevent multiple inclusions
include_once('functions.php');  // Ensure that functions.php is included only once

// Call the handleLogin function and get the result
$loginResult = handleLogin();

// Extract variables from the login result array
$email = $loginResult['email'];
$password = $loginResult['password'];
$emailErr = $loginResult['emailErr'];
$passwordErr = $loginResult['passwordErr'];
$loginError = $loginResult['loginError'];
$errorDetails = $loginResult['errorDetails'];

// Proceed if there are no login errors
if (empty($errorDetails)) {
    // Call the connectDb() function to establish a database connection
    $conn = connectDb();

    // Example: Safe query using prepared statements to prevent SQL injection
    $sql = "SELECT id, email, name FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);  // Prepare the SQL statement
    $stmt->bind_param("s", $email);  // Bind the email parameter (s means string)
    $stmt->execute();  // Execute the prepared statement

    // Get the result of the query
    $queryResult = $stmt->get_result();

    if ($queryResult->num_rows > 0) {
        // Output data of each row
        while($row = $queryResult->fetch_assoc()) {
            echo "id: " . $row["id"] . " - Email: " . $row["email"] . " - Name: " . $row["name"] . "<br>";
        }
    } else {
        echo "No results found.";
    }

    // Close the prepared statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Display login errors
    echo $loginError . "<br>";
    foreach ($errorDetails as $error) {
        echo $error . "<br>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title></title>
</head>

<body class="bg-secondary-subtle">
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="col-3">
            <!-- Server-Side Validation Messages should be placed here -->
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-4 fw-normal">Login</h1>
                    <form method="post" action="">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="email" name="email" placeholder="user1@example.com">
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            <label for="password">Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>