<?php
// Include your database connection
include 'db.php';

// --- Capture Incoming Data ---
$webhook_content = file_get_contents("php://input");
$webhook_data = json_decode($webhook_content, true);

// --- Log for Debugging ---
// This is crucial for testing. It will create a log file with the raw data from GrooveSell.
file_put_contents('webhook_log.txt', $webhook_content . "\n\n", FILE_APPEND);

// --- Process the Webhook ---
if ($webhook_data && isset($webhook_data['event']) && $webhook_data['event'] === 'SALE') {
    
    // Extract customer information from the GrooveSell payload
    $email = $webhook_data['data']['customer']['email'];
    $first_name = $webhook_data['data']['customer']['first_name'];
    $last_name = $webhook_data['data']['customer']['last_name'];
    $product_name = $webhook_data['data']['product']['name'];
    $transaction_id = $webhook_data['data']['transaction_id'];

    // Check if a user with this email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_user = $result->fetch_assoc();

    if ($existing_user) {
        // User already exists, so we just need to grant them access to the course
        $user_id = $existing_user['id'];
        // In a real app, you would have a table to link users to specific courses
        // For now, we'll just log that they bought a product
        file_put_contents('successful_purchases.txt', "Existing user {$email} purchased {$product_name}\n", FILE_APPEND);

    } else {
        // User does not exist, so create a new account
        $username = $first_name . ' ' . $last_name;
        $random_password = bin2hex(random_bytes(8)); // Generate a random 16-character password
        $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        $stmt->execute();
        $user_id = $stmt->insert_id;

        // --- Send Welcome Email ---
        $to = $email;
        $subject = "Welcome to the Internet Marketing Hub!";
        $message = "
        <html>
        <body>
            <h2>Welcome, {$username}!</h2>
            <p>Your account has been created successfully after your purchase of {$product_name}.</p>
            <p>You can now log in to your dashboard using the following credentials:</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Password:</strong> {$random_password}</p>
            <p><a href='https://yourwebsite.com/login.php'>Click here to login</a></p>
        </body>
        </html>
        ";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: <you@yourwebsite.com>' . "\r\n";
        
        mail($to, $subject, $message, $headers);
    }
    
    // Respond to GrooveSell to acknowledge receipt
    http_response_code(200);
    echo "Webhook processed successfully.";

} else {
    // If it's not a sale event or the data is invalid, ignore it
    http_response_code(400);
    echo "Invalid event or data.";
}
?>
