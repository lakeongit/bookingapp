<?php
session_start(); // Start session for storing error messages

// Include necessary configuration files and libraries

// Establish database connection
$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Extract and sanitize form inputs
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = $_POST['password'];
  // Additional form inputs for service provider registration

  // Perform input validation
  $errors = array();

  // Validate username
  if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $errors[] = "Invalid username format. Only alphanumeric characters and underscores are allowed.";
  }

  // Validate email
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address.";
  }

  // Validate password strength
  if (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
  }

  // Check for existing username or email
  $existingUserQuery = "SELECT * FROM users WHERE username='$username' OR email='$email'";
  $existingUserResult = mysqli_query($conn, $existingUserQuery);
  if (mysqli_num_rows($existingUserResult) > 0) {
    $errors[] = "Username or email already registered.";
  }

  // Proceed with registration if no errors
  if (empty($errors)) {
    // Hash the password using a secure algorithm (e.g., bcrypt)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user into the database
    $insertUserQuery = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";
    mysqli_query($conn, $insertUserQuery);

    // Send verification email to the user

    // Provide appropriate feedback to the user
    $_SESSION['success_msg'] = "Registration successful!";
    header("Location: registration_success.php"); // Redirect to a success page
    exit();
  } else {
    $_SESSION['errors'] = $errors;
    header("Location: registration_form.php"); // Redirect back to the registration form to display errors
    exit();
  }
}

// Close database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Registration</title>
</head>
<body>
  <h1>User Registration</h1>
  <?php
  if (isset($_SESSION['errors'])) {
    foreach ($_SESSION['errors'] as $error) {
      echo "<p>Error: " . $error . "</p>";
    }
    unset($_SESSION['errors']);
  }
  ?>
  <form method="post" action="">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>

    <!-- Additional form inputs for service provider registration -->

    <input type="submit" value="Register">
  </form>
</body>
</html>
