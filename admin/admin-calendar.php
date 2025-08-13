<?php 
// --- FORM SUBMISSION LOGIC ---
include '../db.php'; 
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../adminlogin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $title = $_POST['event-title'];
    $date = $_POST['event-date'];
    $time = $_POST['event-time'];
    $description = $_POST['event-description'];
    $start_datetime = $date . ' ' . $time;

    if (!empty($title) && !empty($date) && !empty($time)) {
        $stmt = $conn->prepare("INSERT INTO events (title, description, start_datetime) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $description, $start_datetime);
        $stmt->execute();
    }
    header("Location: admin-calendar.php");
    exit();
}

if (isset($_GET['delete'])) {
    $idToDelete = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $idToDelete);
    $stmt->execute();
    header("Location: admin-calendar.php");
    exit();
}

// --- PAGE CONTENT ---
include 'admin-header.php'; 
$events_result = $conn->query("SELECT * FROM events ORDER BY start_datetime ASC");
?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Event Calendar Management</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Event Form -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6 sticky top-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Add New Event</h2>
                <form action="admin-calendar.php" method="POST" class="space-y-4">
                    <input type="hidden" name="add_event" value="1">
                    <div>
                        <label for="event-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Title</label>
                        <input type="text" id="event-title" name="event-title" required class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg">
                    </div>
                    <div>
                        <label for="event-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                        <input type="date" id="event-date" name="event-date" required class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg">
                    </div>
                    <div>
                        <label for="event-time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time</label>
                        <input type="time" id="event-time" name="event-time" required class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg">
                    </div>
                    <div>
                        <label for="event-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea id="event-description" name="event-description" rows="3" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Add Event</button>
                </form>
            </div>
        </div>

        <!-- Event List -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Upcoming Events</h2>
                <div class="space-y-4">
                    <?php while($event = $events_result->fetch_assoc()): ?>
                        <div class="bg-gray-50 dark:bg-gray-800/60 p-4 rounded-lg flex justify-between items-center">
                            <div>
                                <p class="font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($event['title']); ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo date("F j, Y \a\\t g:i A", strtotime($event['start_datetime'])); ?></p>
                            </div>
                            <div class="space-x-3">
                                <a href="?delete=<?php echo $event['id']; ?>" onclick="return confirm('Are you sure you want to delete this event?');" class="text-red-500 hover:underline"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

