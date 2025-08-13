<?php 
// --- FORM SUBMISSION LOGIC ---
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$currentUserId = $_SESSION['user_id'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle new post submission
    if (isset($_POST['new_post'])) {
        $content = $_POST['post_content'];
        if (!empty($content)) {
            $stmt = $conn->prepare("INSERT INTO user_posts (user_id, content) VALUES (?, ?)");
            $stmt->bind_param("is", $currentUserId, $content);
            $stmt->execute();
        }
        header("Location: profile.php");
        exit();
    }
    // Handle profile update
    if (isset($_POST['update_profile'])) {
        $username = $_POST['username'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $bio = $_POST['bio'];
        $website_url = $_POST['website_url'];
        $linkedin_url = $_POST['linkedin_url'];
        $twitter_url = $_POST['twitter_url'];
        $avatar_url = $_POST['existing_avatar_url'];
        $cover_photo_url = $_POST['existing_cover_photo_url'];

        // Function to handle file upload
        function handleUpload($fileInputName, $identifier, $uploadPath, $namingConvention = 'email') {
            if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == 0) {
                $upload_dir = $uploadPath . "/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $file_extension = strtolower(pathinfo($_FILES[$fileInputName]["name"], PATHINFO_EXTENSION));
                
                $new_filename = '';
                if ($namingConvention === 'username_random') {
                    $safe_username = preg_replace('/[^a-zA-Z0-9-_]/', '', $identifier);
                    $random_numbers = rand(1000, 9999);
                    $new_filename = $safe_username . '-' . $random_numbers . '.' . $file_extension;
                } else { // Default to email
                    $safe_email_filename = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $identifier);
                    $new_filename = $safe_email_filename . '.' . $file_extension;
                }

                $target_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $target_path)) {
                    return $target_path;
                }
            }
            return null;
        }

        // Avatar uses email for filename, saved to 'profile-image' folder
        $new_avatar = handleUpload('avatar_image', $_POST['email'], 'profile-image', 'email');
        if ($new_avatar) $avatar_url = $new_avatar;

        // Cover photo uses username + random numbers, saved to 'cover-photo' folder
        $new_cover = handleUpload('cover_photo', $_POST['username'], 'cover-photo', 'username_random');
        if ($new_cover) $cover_photo_url = $new_cover;
        
        $stmt = $conn->prepare("UPDATE users SET username = ?, first_name = ?, last_name = ?, bio = ?, website_url = ?, linkedin_url = ?, twitter_url = ?, avatar_url = ?, cover_photo_url = ? WHERE id = ?");
        $stmt->bind_param("sssssssssi", $username, $first_name, $last_name, $bio, $website_url, $linkedin_url, $twitter_url, $avatar_url, $cover_photo_url, $currentUserId);
        $stmt->execute();

        header("Location: profile.php?tab=edit");
        exit();
    }
}

// --- PAGE CONTENT & DATA FETCHING ---
include 'header.php'; 

// Fetch user data for the profile
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$userProfile = $stmt->get_result()->fetch_assoc();

// Fetch user's wall posts
$posts_stmt = $conn->prepare("SELECT up.*, u.username, u.avatar_url FROM user_posts up JOIN users u ON up.user_id = u.id WHERE up.user_id = ? ORDER BY up.created_at DESC");
$posts_stmt->bind_param("i", $currentUserId);
$posts_stmt->execute();
$userPosts = $posts_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$activeTab = $_GET['tab'] ?? 'wall';
?>

<div class="max-w-5xl mx-auto">
    <!-- Profile Header -->
    <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md overflow-hidden mb-6">
        <div class="h-48 bg-gray-200 dark:bg-gray-700 bg-cover bg-center" style="background-image: url('<?php echo htmlspecialchars($userProfile['cover_photo_url'] ?? 'https://placehold.co/1200x400/E2E8F0/4A5568?text=Cover+Photo'); ?>')"></div>
        <div class="p-4 flex items-end -mt-16">
            <img src="<?php echo htmlspecialchars($userProfile['avatar_url'] ?? 'https://placehold.co/128x128/3B82F6/FFFFFF?text=A'); ?>" class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 object-cover">
            <div class="ml-4">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($userProfile['username']); ?></h1>
            </div>
        </div>
        <!-- Profile Tabs -->
        <nav class="flex border-t border-gray-200 dark:border-gray-700">
            <a href="?tab=wall" class="px-6 py-3 font-semibold <?php echo $activeTab === 'wall' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'; ?>">Wall</a>
            <a href="?tab=about" class="px-6 py-3 font-semibold <?php echo $activeTab === 'about' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'; ?>">About</a>
            <a href="?tab=edit" class="px-6 py-3 font-semibold <?php echo $activeTab === 'edit' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'; ?>">Edit Profile</a>
            <a href="?tab=billing" class="px-6 py-3 font-semibold <?php echo $activeTab === 'billing' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500'; ?>">Billing</a>
        </nav>
    </div>

    <!-- Tab Content -->
    <div>
        <?php if ($activeTab === 'wall'): ?>
            <!-- New Post Form -->
            <form method="POST" class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-4 mb-6">
                <input type="hidden" name="new_post" value="1">
                <textarea name="post_content" class="w-full p-2 border rounded-lg" placeholder="What's on your mind, <?php echo htmlspecialchars($userProfile['username']); ?>?"></textarea>
                <div class="text-right mt-2">
                    <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Post</button>
                </div>
            </form>
            <!-- User Posts -->
            <div class="space-y-4">
                <?php foreach($userPosts as $post): ?>
                    <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-4">
                        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($activeTab === 'about'): ?>
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">About <?php echo htmlspecialchars($userProfile['username']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($userProfile['bio'] ?? 'No bio provided.')); ?></p>
            </div>
        <?php elseif ($activeTab === 'edit'): ?>
             <form method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6 space-y-6">
                <input type="hidden" name="update_profile" value="1">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($userProfile['email']); ?>">
                <input type="hidden" name="existing_avatar_url" value="<?php echo htmlspecialchars($userProfile['avatar_url'] ?? ''); ?>">
                <input type="hidden" name="existing_cover_photo_url" value="<?php echo htmlspecialchars($userProfile['cover_photo_url'] ?? ''); ?>">

                <!-- Image Uploads -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium">Profile Picture</label>
                        <input type="file" name="avatar_image" class="mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                     <div>
                        <label class="block text-sm font-medium">Cover Photo</label>
                        <input type="file" name="cover_photo" class="mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <!-- Personal Info -->
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="username" class="block text-sm font-medium">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userProfile['username']); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                    </div>
                    <div>
                        <label for="first_name" class="block text-sm font-medium">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($userProfile['first_name'] ?? ''); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                    </div>
                     <div>
                        <label for="last_name" class="block text-sm font-medium">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($userProfile['last_name'] ?? ''); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                    </div>
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium">Bio / About Me</label>
                    <textarea id="bio" name="bio" rows="4" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg"><?php echo htmlspecialchars($userProfile['bio'] ?? ''); ?></textarea>
                </div>

                <!-- Social Links -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="website_url" class="block text-sm font-medium">Website URL</label>
                        <input type="url" id="website_url" name="website_url" value="<?php echo htmlspecialchars($userProfile['website_url'] ?? ''); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                    </div>
                    <div>
                        <label for="linkedin_url" class="block text-sm font-medium">LinkedIn URL</label>
                        <input type="url" id="linkedin_url" name="linkedin_url" value="<?php echo htmlspecialchars($userProfile['linkedin_url'] ?? ''); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                    </div>
                    <div>
                        <label for="twitter_url" class="block text-sm font-medium">Twitter URL</label>
                        <input type="url" id="twitter_url" name="twitter_url" value="<?php echo htmlspecialchars($userProfile['twitter_url'] ?? ''); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Save Changes</button>
                </div>
            </form>
        <?php elseif ($activeTab === 'billing'): ?>
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Billing Information</h2>
                <p class="text-center text-gray-500">Billing details, invoices, and subscription management will be available here.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>



