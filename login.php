<?php
include('db_connect.php');
include './includes/header.php';
// session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $email_or_username = $_POST['email_or_username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email_or_username' OR username='$email_or_username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    
    // var_dump($user['password']); 
    // echo"user name and password";
    if ($user && password_verify($password, $user['password'])) {
        $update_sql = "UPDATE users SET last_loggedin_time = NOW() WHERE u_id = {$user['u_id']}";
        mysqli_query($conn, $update_sql);
        
        //session variables
        $_SESSION['user_id'] = $user['u_id'];
        $_SESSION['usertype'] = $user['usertype'];
        echo "<script>alert('Login Successful!'); window.location='shoppage.php';</script>";
    } else {
        echo "<script>alert('Invalid credentials!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- <link rel="stylesheet" href="styles.css"> -->
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
    }

   h2 {
    margin-bottom: 20px;
    }

    input, select, button {
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
   }
   form {
    text-align: left;
  }

 label {
    display: block;
    font-weight: bold;
    /* margin-top: 2px; */
 }
   

   button {
    background:rgb(129, 147, 249);
    color: white;
    font-size: 16px;
    cursor: pointer;
   }

   button:hover {
    background: rgb(70, 98, 253);
   }

   p {
    margin-top: 10px;
   }
 </style>
</head>
<body>
    <div class="register_container">
        <h2>User Login</h2>
        <form method="POST">
            <label for="email_or_username">Email or Username:</label>
            <input type="text" name="email_or_username" placeholder="Email or username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="submit">Login</button>
        </form>
        <p>Not registered yet? <a href="register.php">Sign up</a></p>
    </div>
</body>
</html>
<?php
include './includes/footer.php';
?>