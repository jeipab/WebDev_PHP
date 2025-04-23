<?php
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header("Location: index.php");
        exit;
    }

    // Retrieve user data from session
    $user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($user['fullname']); ?>!</h1>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        
        <p>Profile Picture:</p>
        <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-pic">
    </div>
</body>
</html>