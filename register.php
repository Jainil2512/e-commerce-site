<?php
include('db_connect.php');
include './includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $usertype = $_POST['usertype'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (first_name, last_name, email, username, phone, usertype, password, registration_date) 
              VALUES ('$first_name', '$last_name', '$email', '$username', '$phone', '$usertype', '$password', NOW())";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Registration Successful!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Error: ".mysqli_error($conn)."');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            text-align: center;
            background-color: rgb(158, 255, 249);
        }

        .register_container {
            width: 450px;
            background: white;
            padding: 20px;
            margin: 50px auto;
            box-shadow: 0px 0px 10px gray;
            border-radius: 8px;
            text-align: left;
            /* background-color: rgb(244, 240, 159); */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background:rgb(12, 222, 208);
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: rgb(9, 166, 156);
        }

        p {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="register_container">
        <h2>User Registration</h2>
        <form method="POST">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" placeholder="Enter First Name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" placeholder="Enter Last Name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter Email" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter Username" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" placeholder="Enter Phone Number" required>

            <label for="usertype">User Type:</label>
            <select id="usertype" name="usertype" required>
                <option value="user">User</option>
                <!-- <option value="admin">Admin</option> -->
            </select>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" required>

            <button type="submit" name="submit">Register</button>
        </form>
        <p>Already registered? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>

<?php
include './includes/footer.php';
?>