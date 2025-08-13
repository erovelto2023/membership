<?php 
include 'header.php'; 
include 'db.php';

$profileUserId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$profileUser = null;

if ($profileUserId > 0) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND is_admin = 0");
    $stmt->bind_param("i", $profileUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    $profileUser = $result->fetch_assoc();

    // Fetch user's wall posts
    $posts_stmt = $conn->prepare("SELECT * FROM user_posts WHERE user_id = ? ORDER BY created_at DESC");
    $posts_stmt->bind_param("i", $profileUserId);
    $posts_stmt->execute();
    $userPosts = $posts_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<div>
    <?php if ($profileUser): ?>
        <div class="max-w-5xl mx-auto">
             <!-- Profile Header -->
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md overflow-hidden mb-6">
                <div class="h-48 bg-gray-200 dark:bg-gray-700 bg-cover bg-center" style="background-image: url('<?php echo htmlspecialchars($profileUser['cover_photo_url'] ?? 'https://placehold.co/1200x400/E2E8F0/4A5568?text=Cover+Photo'); ?>')"></div>
                <div class="p-4 flex justify-between items-end -mt-16">
                    <div class="flex items-end">
                        <img src="<?php echo htmlspecialchars($profileUser['avatar_url'] ?? 'https://placehold.co/128x128/3B82F6/FFFFFF?text=A'); ?>" class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 object-cover">
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($profileUser['username']); ?></h1>
                        </div>
                    </div>
                    <a href="messaging.php?start_with_user=<?php echo $profileUser['id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Send Message</a>
                </div>
            </div>
             <!-- User Posts -->
            <div class="space-y-4">
                <?php foreach($userPosts as $post): ?>
                    <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-4">
                        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-700 dark:text-white">User Not Found</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">The requested user could not be found.</p>
            <a href="members.php" class="mt-4 inline-block text-blue-500 hover:underline">Return to Directory</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
