<?php 
// --- FORM SUBMISSION LOGIC ---
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$currentUserId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_thread'])) {
        $title = $_POST['post-title'];
        $content = $_POST['post-content'];
        $categoryId = $_POST['post-category'];
        
        $stmt = $conn->prepare("INSERT INTO forum_threads (category_id, user_id, title, content) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $categoryId, $currentUserId, $title, $content);
        $stmt->execute();
        header("Location: forum.php?category_id=" . $categoryId);
        exit();
    }
    if (isset($_POST['new_reply'])) {
        $content = $_POST['reply-content'];
        $threadId = $_POST['thread_id'];

        $stmt = $conn->prepare("INSERT INTO forum_replies (thread_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $threadId, $currentUserId, $content);
        $stmt->execute();
        header("Location: forum.php?thread_id=" . $threadId);
        exit();
    }
}

// --- PAGE CONTENT ---
include 'header.php';

$categories = $conn->query("SELECT * FROM forum_categories WHERE status = 'Visible'")->fetch_all(MYSQLI_ASSOC);
$activeCategoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : ($categories[0]['id'] ?? 0);
$threadId = isset($_GET['thread_id']) ? (int)$_GET['thread_id'] : null;

if ($threadId) {
    // View single thread
    $stmt = $conn->prepare("SELECT ft.*, u.username as author, u.avatar_url FROM forum_threads ft JOIN users u ON ft.user_id = u.id WHERE ft.id = ? AND ft.status = 'Visible'");
    $stmt->bind_param("i", $threadId);
    $stmt->execute();
    $selectedThread = $stmt->get_result()->fetch_assoc();

    $replies_stmt = $conn->prepare("SELECT fr.*, u.username as author, u.avatar_url FROM forum_replies fr JOIN users u ON fr.user_id = u.id WHERE fr.thread_id = ? ORDER BY fr.created_at ASC");
    $replies_stmt->bind_param("i", $threadId);
    $replies_stmt->execute();
    $replies = $replies_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    // View thread list
    $stmt = $conn->prepare("SELECT ft.*, u.username as author, u.avatar_url, (SELECT COUNT(*) FROM forum_replies WHERE thread_id = ft.id) as reply_count FROM forum_threads ft JOIN users u ON ft.user_id = u.id WHERE ft.category_id = ? AND ft.status = 'Visible' ORDER BY ft.is_pinned DESC, ft.created_at DESC");
    $stmt->bind_param("i", $activeCategoryId);
    $stmt->execute();
    $threads = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<div>
    <?php if ($threadId && $selectedThread): ?>
        <!-- Thread Detail View -->
        <a href="forum.php?category_id=<?php echo $selectedThread['category_id']; ?>" class="text-blue-500 mb-4 inline-block">&larr; Back to Threads</a>
        <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
            <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($selectedThread['title']); ?></h1>
            <p class="text-sm text-gray-500">By <?php echo htmlspecialchars($selectedThread['author']); ?></p>
            <div class="prose dark:prose-invert mt-4"><?php echo nl2br(htmlspecialchars($selectedThread['content'])); ?></div>
        </div>
        <!-- Replies -->
        <div class="space-y-4 mt-6">
            <?php foreach($replies as $reply): ?>
                <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-4">
                    <p class="text-sm font-bold"><?php echo htmlspecialchars($reply['author']); ?></p>
                    <p class="mt-2"><?php echo nl2br(htmlspecialchars($reply['content'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Reply Form -->
         <form method="POST" class="mt-6">
            <input type="hidden" name="new_reply" value="1">
            <input type="hidden" name="thread_id" value="<?php echo $threadId; ?>">
            <textarea name="reply-content" rows="5" class="w-full p-2 border rounded-lg" placeholder="Write a reply..."></textarea>
            <button type="submit" class="mt-2 bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Post Reply</button>
        </form>

    <?php else: ?>
        <!-- Forum List View -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Community Forum</h1>
            <button id="new-post-btn" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">New Post</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-3">
                <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-4 sticky top-6">
                    <h2 class="text-lg font-semibold mb-3">Categories</h2>
                    <ul class="space-y-1">
                        <?php foreach($categories as $cat): ?>
                            <li><a href="?category_id=<?php echo $cat['id']; ?>" class="block p-2 rounded-md <?php echo $activeCategoryId == $cat['id'] ? 'bg-blue-100 font-bold' : 'hover:bg-gray-100'; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="md:col-span-9 space-y-4">
                <?php foreach($threads as $thread): ?>
                    <a href="?thread_id=<?php echo $thread['id']; ?>" class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-5 flex items-start space-x-4 hover:shadow-lg transition-shadow block">
                        <img src="<?php echo htmlspecialchars($thread['avatar_url'] ?? 'https://placehold.co/48x48/E2E8F0/4A5568?text=U'); ?>" class="w-12 h-12 rounded-full">
                        <div class="flex-1">
                            <h3 class="font-semibold"><?php echo htmlspecialchars($thread['title']); ?></h3>
                            <p class="text-sm text-gray-500">By <?php echo htmlspecialchars($thread['author']); ?></p>
                        </div>
                        <div class="text-sm text-gray-500">Replies: <?php echo $thread['reply_count']; ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- New Post Modal -->
<div id="new-post-modal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex justify-center items-center p-4 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-xl font-bold">Create a New Post</h2>
            <button id="close-modal-btn" class="p-2 rounded-full hover:bg-gray-100">&times;</button>
        </div>
        <form method="POST" class="p-6 space-y-4">
            <input type="hidden" name="new_thread" value="1">
            <div>
                <label for="post-title" class="block text-sm font-medium mb-1">Title</label>
                <input type="text" id="post-title" name="post-title" class="w-full p-2 border rounded-lg" required>
            </div>
            <div>
                <label for="post-category" class="block text-sm font-medium mb-1">Category</label>
                <select id="post-category" name="post-category" class="w-full p-2 border rounded-lg" required>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="post-content" class="block text-sm font-medium mb-1">Content</label>
                <textarea id="post-content" name="post-content" class="w-full p-2 border rounded-lg" rows="8" required></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel-post-btn" class="bg-gray-200 font-bold py-2 px-4 rounded-lg">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Post Thread</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPostBtn = document.getElementById('new-post-btn');
    const modal = document.getElementById('new-post-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const cancelPostBtn = document.getElementById('cancel-post-btn');

    function openModal() { modal.classList.remove('hidden'); }
    function closeModal() { modal.classList.add('hidden'); }

    newPostBtn.addEventListener('click', openModal);
    closeModalBtn.addEventListener('click', closeModal);
    cancelPostBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
});
</script>

<?php include 'footer.php'; ?>
