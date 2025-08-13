<?php 
include 'header.php'; 
include 'db.php';

// The $user variable is already available from header.php
$userId = $_SESSION['user_id'];

// --- Fetch Dynamic Data for Widgets ---

// 1. Latest Announcement
$latest_announcement_result = $conn->query("SELECT message FROM announcements ORDER BY created_at DESC LIMIT 1");
$latest_announcement = $latest_announcement_result->fetch_assoc();

// 2. New Forum Posts (Updated to fetch 5 posts and the thread ID)
$forum_posts_sql = "
    SELECT ft.id as thread_id, ft.title, u.username as author, fc.name as category 
    FROM forum_threads ft 
    JOIN users u ON ft.user_id = u.id 
    JOIN forum_categories fc ON ft.category_id = fc.id 
    WHERE ft.status = 'Visible' AND fc.status = 'Visible'
    ORDER BY ft.created_at DESC 
    LIMIT 5";
$forum_posts_result = $conn->query($forum_posts_sql);
$forum_posts = $forum_posts_result->fetch_all(MYSQLI_ASSOC);

// 3. Progress Summary (Live Data)
$courses_completed_sql = "SELECT COUNT(*) as count FROM user_courses WHERE user_id = $userId";
$courses_completed_result = $conn->query($courses_completed_sql);
$courses_completed = $courses_completed_result->fetch_assoc()['count'];

$classes_attended = 0; // Placeholder until event attendance is tracked
?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">"The secret of getting ahead is getting started." - Mark Twain</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <!-- Left Column -->
        <div class="md:col-span-2 space-y-6">
            <!-- Global Announcement -->
            <?php if ($latest_announcement): ?>
            <div class="bg-blue-100 dark:bg-blue-900/50 border-l-4 border-blue-500 text-blue-800 dark:text-blue-200 p-4 rounded-r-lg shadow-md">
                <div class="flex">
                    <div class="py-1"><i class="fa-solid fa-bullhorn mr-4"></i></div>
                    <div>
                        <p class="font-bold">Message from the Admin</p>
                        <p class="text-sm"><?php echo nl2br(htmlspecialchars($latest_announcement['message'])); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Recent Forum Posts -->
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Recent Forum Posts</h2>
                <div class="space-y-2">
                    <?php if (!empty($forum_posts)): ?>
                        <?php foreach($forum_posts as $post): ?>
                        <a href="forum.php?thread_id=<?php echo $post['thread_id']; ?>" class="block p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex items-start space-x-4">
                                <i class="fa-solid fa-comments text-gray-400 mt-1"></i>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($post['title']); ?></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">by <?php echo htmlspecialchars($post['author']); ?> in <span class="font-medium text-blue-500"><?php echo htmlspecialchars($post['category']); ?></span></p>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 dark:text-gray-400">No forum posts yet. Be the first to start a conversation!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Progress Summary -->
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6 text-center">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200">Progress Summary</h2>
                <div class="mt-4 space-y-4">
                    <div><p class="text-3xl font-bold text-blue-600 dark:text-blue-400"><?php echo $courses_completed; ?></p><p class="text-gray-500 dark:text-gray-400">Courses Enrolled</p></div>
                    <div><p class="text-3xl font-bold text-green-500"><?php echo $classes_attended; ?></p><p class="text-gray-500 dark:text-gray-400">Classes Attended</p></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
