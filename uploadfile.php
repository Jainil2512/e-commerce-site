<?php
include 'db_connect.php'; // Include database connection
include './includes/header.php';

// session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'admin') {
    echo "<script>alert('Access Denied! Admins Only.'); window.location='shoppage.php';</script>";
    exit();
}

// $uploadDir = 'documents/'; // Folder to store files
$imageDir = 'images/'; // Folder to store images

// if (!is_dir($uploadDir)) {
//     mkdir($uploadDir, 0777, true); // Create folder if not exists
// }

if (!is_dir($imageDir)) {
    mkdir($imageDir, 0777, true); // Create folder if not exists
}

$message = ""; // Success/Error message
$insertedData = []; // Store only newly inserted data

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {  
    $fileName = basename($_FILES['file']['name']);
    $targetFilePath = $imageDir . $fileName;
    
    if (pathinfo($fileName, PATHINFO_EXTENSION) !== 'csv') {
        echo "Only CSV files are allowed.";
    } else {
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
            $file = fopen($targetFilePath, "r");
            fgetcsv($file); // Skip the first row if it's a header

            while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                $productName = mysqli_real_escape_string($conn, $data[0]);
                $price = mysqli_real_escape_string($conn, $data[1]);
                $stock = mysqli_real_escape_string($conn, $data[2]);
                $description = mysqli_real_escape_string($conn, $data[3]);
                $imageUrl = trim($data[4]); // Get image URL from CSV file
                $status = mysqli_real_escape_string($conn, strtolower($data[5]));
                 
                // Validate status (Ensure only 'approved' or 'pending' is allowed)
                if (!in_array($status, ['approved', 'pending'])) {
                    $status = 'pending'; // Default status
                }

                // Check if image is a URL and needs to be downloaded
                if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    $imageData = file_get_contents($imageUrl);
                    if ($imageData) {
                        $imageFileName = basename(parse_url($imageUrl, PHP_URL_PATH));
                        // echo $imageFileName;
                        $imagePath = $imageDir . $imageFileName;
                        // echo $imagePath;


                    // Ensure the image is saved in the correct directory
                        if (file_put_contents($imagePath, $imageData)) {
                        // Store only the relative path to make it work on all environments
                         $imageUrl = $imagePath;
                        } else {
                          $imageUrl = ''; // Set to empty if download fails
                        }

                    }
                
                }
                      // Check if the product already exists in the database
                      $checkQuery = "SELECT * FROM product WHERE product_name = '$productName'";
                      $checkResult = mysqli_query($conn, $checkQuery);
                      if (mysqli_num_rows($checkResult) > 0) {
                        // Product exists → Update it
                        $updateQuery = "UPDATE product 
                                        SET price = '$price', stock = '$stock', description = '$description', image = '$imageUrl', status = '$status' 
                                        WHERE product_name = '$productName'";
                        if (mysqli_query($conn, $updateQuery)) {
                            $insertedData[] = [
                                'product_name' => $productName,
                                'price' => $price,
                                'stock' => $stock,
                                'description' => $description,
                                'image' => $imageUrl,
                                'status' => $status
                            ];
                        }
                    } else {
                        // Product is new → Insert it
                        $insertQuery = "INSERT INTO product (product_name, price, stock, description, image, status) 
                                        VALUES ('$productName', '$price', '$stock', '$description', '$imageUrl', '$status')";
                        if (mysqli_query($conn, $insertQuery)) {
                            $insertedData[] = [
                                'product_name' => $productName,
                                'price' => $price,
                                'stock' => $stock,
                                'description' => $description,
                                'image' => $imageUrl,
                                'status' => $status

                            ];
                        }
                    }

            }
            fclose($file);
            echo "CSV file uploaded and data inserted successfully!";
        } else {
            echo "File upload failed.";
        }
    }
}
// Fetch data from the database to display in the preview
$result = mysqli_query($conn, "SELECT * FROM product ORDER BY product_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>File Upload</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/fileupload/includes/styles.css"> 
</head>
<body>
<div class="container mt-5">
    <h2>Upload a CSV File</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" class="form-control mb-3" required>
        <button type="submit" class="btn btn-primary">Upload CSV</button>
    </form>
       <br><br>
       <?php if (!empty($insertedData)): ?>
        <h2 class="mt-4">Preview of Inserted Data</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($insertedData as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['price']); ?></td>
                            <td><?php echo htmlspecialchars($row['stock']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" width="50">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>
 <?php include './includes/footer.php'; ?>
</body>
</html>
