<?php 
// --- FORM SUBMISSION LOGIC ---
include '../db.php'; 
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../adminlogin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_announcement'])) {
        $message = $_POST['message'];
        if (!empty($message)) {
            $stmt = $conn->prepare("INSERT INTO announcements (message) VALUES (?)");
            $stmt->bind_param("s", $message);
            $stmt->execute();
        }
    }
    header("Location: admin-announcements.php");
    exit();
}

if (isset($_GET['delete'])) {
    $idToDelete = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $idToDelete);
    $stmt->execute();
    header("Location: admin-announcements.php");
    exit();
}

// --- PAGE CONTENT ---
include 'admin-header.php'; 
$announcements_result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Global Announcements</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Announcement Form -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6 sticky top-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">New Announcement</h2>
                <form action="admin-announcements.php" method="POST" class="space-y-4">
                    <input type="hidden" name="add_announcement" value="1">
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
                        <textarea id="message" name="message" rows="5" required class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Post Announcement</button>
                </form>
            </div>
        </div>

        <!-- Announcement List -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Posted Announcements</h2>
                <div class="space-y-4">
                    <?php while($announcement = $announcements_result->fetch_assoc()): ?>
                        <div class="bg-gray-50 dark:bg-gray-800/60 p-4 rounded-lg flex justify-between items-center">
                            <div>
                                <p class="text-gray-800 dark:text-white"><?php echo nl2br(htmlspecialchars($announcement['message'])); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Posted on <?php echo date("M j, Y", strtotime($announcement['created_at'])); ?></p>
                            </div>
                            <a href="?delete=<?php echo $announcement['id']; ?>" onclick="return confirm('Are you sure you want to delete this announcement?');" class="text-red-500 hover:underline" title="Delete"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
