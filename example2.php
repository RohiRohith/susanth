<?php
session_start();

$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";     // Replace with your database password
$dbname = "users"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'signup':
                handleSignup($conn);
                break;
        }
    }
}

$conn->close();

function handleSignup($conn) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if (isExistingUser($conn, $name, $phone, $address)) {
        echo "User already exists!";
    } else {
        if (validatePassword($password)) {
            if ($password === $confirmPassword) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $sql = "INSERT INTO users (name, phone, address, password) VALUES ('$name', '$phone', '$address', '$hashedPassword')";
                if ($conn->query($sql) === TRUE) {
                    echo '<script>window.location.replace("login.php");</script>';
                    exit();
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Password and Confirm Password do not match.";
            }
        } else {
            echo "Invalid password format. Password must contain at least 2 alphabetical letters, 1 digit, and 1 special character.";
        }
    }
}

function isExistingUser($conn, $name, $phone, $address) {
    $sql = "SELECT * FROM users WHERE name = '$name' AND phone = '$phone' AND address = '$address'";
    $result = $conn->query($sql);

    return $result->num_rows > 0;
}

function validatePassword($password) {
    // Password must contain at least 2 alphabetical letters, 1 digit, and 1 special character
    $alphabeticalCount = preg_match_all('/[a-z]/i', $password);
    $digitCount = preg_match_all('/\d/', $password);
    $specialCharCount = preg_match_all('/[^a-zA-Z\d]/', $password);

    return $alphabeticalCount >= 2 && $digitCount >= 1 && $specialCharCount >= 1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        form {
            max-width: 400px;
            margin: 20px auto;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
        }

        button {
            padding: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div id="signupForm">
        <form id="signup" method="post" action="">
            <h2>Signup</h2>
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="phone" placeholder="Phone" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
            <button type="submit" name="action" value="signup">Signup</button>
        </form>
    </div>

</body>
</html>
