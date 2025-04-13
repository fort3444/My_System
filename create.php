<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($_FILES['image']['name'])) {
        die('Please fill all fields');
    }
    
    // Handle image upload
    $targetDir = "uploads/";
    $fileName = basename($_FILES['image']['name']);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    // Allow certain file formats
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (!in_array($fileType, $allowTypes)) {
        die('Only JPG, JPEG, PNG, GIF files are allowed.');
    }
    
    // Upload file to server
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO users (name, email, image_path) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $fileName]);
        
        header('Location: index.php');
        exit();
    } else {
        die('Error uploading file.');
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New User</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; }
        input, button { margin-bottom: 10px; padding: 8px; width: 100%; }
    </style>
</head>
<body>
    <h1>Add New User</h1>
    <form action="create.php" method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit">Add User</button>
    </form>
    <a href="index.php">Back to User List</a>
</body>
</html>