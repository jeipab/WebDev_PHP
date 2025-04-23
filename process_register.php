<?php
    session_start();

    // Initialize an array to hold any error messages
    $errors = [];

    // Check if the request method is POST
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
        // Retrieve and validate the full name
        $fullname = isset($_POST['fullname']) ? htmlspecialchars(trim($_POST['fullname'])) : '';
        if (empty($fullname)) {
            $errors[] = "Full name is required.";
        }

        // Retrieve and validate the email address
        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email is required.";
        }

        // Retrieve and validate the password
        $password = isset($_POST['password']) ? htmlspecialchars(trim($_POST['password'])) : '';
        if (empty($password) || strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }

        // Retrieve and validate the confirm password
        $confirm_password = isset($_POST['confirm_password']) ? htmlspecialchars(trim($_POST['confirm_password'])) : '';
        if ($confirm_password !== $password) {
            $errors[] = "Passwords do not match.";
        }

        // Validate the profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
            $fileName = $_FILES['profile_picture']['name'];
            $fileType = $_FILES['profile_picture']['type'];
            $fileSize = $_FILES['profile_picture']['size'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png'];

            // Check file extension
            if (!in_array($fileExtension, $allowedExtensions)) {
                $errors[] = "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
            }

            // Check file size (2MB limit)
            if ($fileSize > 2 * 1024 * 1024) {
                $errors[] = "File size exceeds the limit of 2MB.";
            }
        } else {
            $errors[] = "Profile picture upload failed.";
        }

        // Retrieve and validate the gender
        $gender = isset($_POST['gender']) ? htmlspecialchars(trim($_POST['gender'])) : '';
        if (empty($gender)) {
            $errors[] = "Gender is required.";
        }

        // Check if terms and conditions are agreed
        $terms = isset($_POST['terms']) ? htmlspecialchars(trim($_POST['terms'])) : '';
        if ($terms !== 'agree') {
            $errors[] = "You must agree to the terms and conditions.";
        }

        // If validation passes, proceed with registration
        if (empty($errors)) {
            $uploadDir = 'uploads/';

            // Ensure the upload directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate a unique name for the uploaded file
            $newFileName = uniqid('img_', true) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            // Move the uploaded file to the destination directory
            if (!move_uploaded_file($fileTmpPath, $destPath)) {
                $errors[] = "Failed to move uploaded file.";
            }

            // Hash the password for secure storage
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare user data for storage
            $userData = [
                'fullname' => $fullname,
                'email' => $email,
                'password' => $hashPassword,
                'profile_picture' => $newFileName,
                'gender' => $gender,
            ];

            // Load existing users
            $existingUsers = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];

            // Add the new user
            $existingUsers[] = $userData;

            // Save back to file
            file_put_contents('users.json', json_encode($existingUsers, JSON_PRETTY_PRINT));

            // Store user data in the session and redirect to the welcome page
            $_SESSION['user'] = $userData;
            header("Location: welcome.php");
            exit;
        }
    } else {
        // If the request method is not POST, add an error message
        $errors[] = "Invalid request method.";
    }
?>

<?php if (!empty($errors)): ?>
    <script>
        alert(`<?php echo implode("\\n", array_map("addslashes", $errors)); ?>`);
        window.location.href = "register.html";
    </script>
<?php endif; ?>

