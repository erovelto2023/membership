<?php 
// --- FORM SUBMISSION LOGIC (MOVED TO TOP) ---
include '../db.php'; // DB connection first

session_start(); // Start session before any output
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../adminlogin.php");
    exit;
}

// Handle form submissions for replying and updating status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $userId = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $isAdmin = $_POST['is_admin'];
    $avatar_url = $_POST['existing_avatar_url']; // Keep existing avatar by default

    // --- AVATAR UPLOAD LOGIC ---
    if (isset($_FILES['avatar_image']) && $_FILES['avatar_image']['error'] == 0) {
        $upload_dir = "../profile-image/"; // This is outside the admin folder
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_extension = strtolower(pathinfo($_FILES["avatar_image"]["name"], PATHINFO_EXTENSION));
        $safe_email_filename = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $email);
        $new_filename = $safe_email_filename . '.' . $file_extension;
        $target_path = $upload_dir . $new_filename;

        // Move the uploaded file
        if (move_uploaded_file($_FILES["avatar_image"]["tmp_name"], $target_path)) {
            // Set the new avatar URL (relative path from the root)
            $avatar_url = "profile-image/" . $new_filename;
        }
    }
    // --- END AVATAR UPLOAD LOGIC ---

    // Update user details in the database
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, is_admin = ?, avatar_url = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $username, $email, $isAdmin, $avatar_url, $userId);
    $stmt->execute();
    
    // In a real app, you would add password update logic here if a new password was provided
    
    header("Location: admin-users.php"); // Redirect AFTER processing
    exit();
}

// --- PAGE CONTENT ---
include 'admin-header.php'; 

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$userId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$selectedUser = null;

// Fetch all users from the database for the list view
$users_result = $conn->query("SELECT id, username, email, created_at, is_admin FROM users");

if ($userId) {
    $stmt = $conn->prepare("SELECT id, username, email, is_admin, avatar_url FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $selectedUser = $result->fetch_assoc();
}
?>

<div>
    <?php if ($action === 'list'): ?>
        <!-- User List View -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">User Management</h1>
            <a href="?action=new" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Add New User</span>
            </a>
        </div>
        <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Name</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Email</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Role</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $users_result->fetch_assoc()): ?>
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="p-4 font-medium text-gray-800 dark:text-white"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="p-4 text-gray-600 dark:text-gray-300"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="p-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $user['is_admin'] ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'; ?>">
                                    <?php echo $user['is_admin'] ? 'Admin' : 'Student'; ?>
                                </span>
                            </td>
                            <td class="p-4 space-x-3">
                                <a href="?action=edit&id=<?php echo $user['id']; ?>" class="text-green-500 hover:underline" title="Edit User"><i class="fa-solid fa-pencil"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php elseif (($action === 'edit' && $selectedUser) || $action === 'new'): ?>
        <!-- Add/Edit User View -->
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">
            <?php echo $action === 'edit' ? 'Edit User' : 'Create New User'; ?>
        </h1>
        <form action="admin-users.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="<?php echo $action === 'edit' ? 'update_user' : 'add_user'; ?>" value="1">
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="user_id" value="<?php echo $selectedUser['id']; ?>">
                <input type="hidden" name="existing_avatar_url" value="<?php echo htmlspecialchars($selectedUser['avatar_url'] ?? ''); ?>">
            <?php endif; ?>

            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($selectedUser['username'] ?? ''); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($selectedUser['email'] ?? ''); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                    </div>
                     <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input type="password" id="password" name="password" placeholder="<?php echo $action === 'edit' ? 'Leave blank to keep unchanged' : ''; ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                    </div>
                    <div>
                        <label for="is_admin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                        <select id="is_admin" name="is_admin" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                            <option value="0" <?php echo !($selectedUser['is_admin'] ?? 0) ? 'selected' : ''; ?>>Student</option>
                            <option value="1" <?php echo ($selectedUser['is_admin'] ?? 0) ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="avatar_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Avatar Image</label>
                    <?php if (!empty($selectedUser['avatar_url'])): ?>
                        <img src="../<?php echo htmlspecialchars($selectedUser['avatar_url']); ?>" alt="Current Avatar" class="w-20 h-20 rounded-full my-2">
                    <?php endif; ?>
                    <input type="file" id="avatar_image" name="avatar_image" class="mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>
            <div class="flex justify-end space-x-4">
                <a href="admin-users.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Save User</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>
