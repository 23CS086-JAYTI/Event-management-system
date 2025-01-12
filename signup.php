<?php
require 'db.php';

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $errorMessage = 'Passwords do not match!';
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            $errorMessage = 'Email already exists.';
        } else {
            // Insert user into the database
            $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $hashed_password);
            if ($stmt->execute()) {
                $successMessage = 'Registration successful!';
                header('Location: login.html');
                exit;
            } else {
                $errorMessage = 'Error: ' . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap">
    <style>
        /* Background styling */
        body {
            background-image: url('https://t4.ftcdn.net/jpg/08/97/91/67/360_F_897916750_YcsUrgSf21qmMAWnLuBrQD14s0n9M0pz.webp');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Playfair Display', serif;
        }

        /* Form container styling */
        .form-container {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 30px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            color: #ffffff;
        }

        /* Form heading styling */
        .form-container h2 {
            margin-bottom: 20px;
            font-size: 2em;
            font-weight: 700;
        }

        /* Input field styling */
        .form-container input[type="email"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }

        /* Button styling */
        .form-container button {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #3a3042;
            color: #ffffff;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #5c4d6d;
        }

        /* Login link styling */
        .form-container a {
            display: block;
            margin-top: 15px;
            color: #ffffff;
            font-size: 0.9em;
            text-decoration: none;
        }

        .form-container a:hover {
            text-decoration: underline;
        }

        /* Error and success message styling */
        .error-message, .success-message {
            color: #ff4d4d;
            font-size: 0.9em;
            margin-top: 10px;
        }

        .success-message {
            color: #4dff88;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form method="POST" action="">
            <h2>Sign Up</h2>
            
            <?php if ($errorMessage): ?>
                <p class="error-message"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
            <?php if ($successMessage): ?>
                <p class="success-message"><?php echo $successMessage; ?></p>
            <?php endif; ?>

            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="password" placeholder="Create a password" required>
            <input type="password" name="confirm_password" placeholder="Confirm your password" required>
            <button type="submit">Sign Up</button>
            <a href="login.html">Already have an account? Login</a>
        </form>
    </div>
</body>
</html>
