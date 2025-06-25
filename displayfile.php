<?php
include 'db_connect.php'; // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Uploaded Files</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Uploaded Files</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>File Name</th>
                <th>Upload Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM files ORDER BY upload_date DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td><a href='documents/{$row['file_name']}' target='_blank'>{$row['file_name']}</a></td>
                        <td>{$row['upload_date']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='2' class='text-center'>No files uploaded yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
