<?php 
// --- FORM SUBMISSION LOGIC (MOVED TO TOP) ---
session_start(); // Start session before anything else
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Handle new ticket submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ticket'])) {
    $subject = $_POST['ticket-subject'];
    $message = $_POST['ticket-message'];

    $stmt = $conn->prepare("INSERT INTO support_tickets (user_id, subject, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $subject, $message);
    $stmt->execute();
    header("Location: support.php"); // Refresh to show the new ticket
    exit();
}

// --- PAGE CONTENT ---
include 'header.php'; 

// Fetch user's ticket history
$stmt = $conn->prepare("SELECT id, subject, status, last_updated FROM support_tickets WHERE user_id = ? ORDER BY last_updated DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$tickets_result = $stmt->get_result();
$userTickets = $tickets_result->fetch_all(MYSQLI_ASSOC);
?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Support Center</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- New Ticket Form -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6 sticky top-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Create a New Support Ticket</h2>
                <form action="support.php" method="POST" class="space-y-4">
                    <input type="hidden" name="submit_ticket" value="1">
                    <div>
                        <label for="ticket-subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                        <input type="text" id="ticket-subject" name="ticket-subject" required class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg">
                    </div>
                    <div>
                        <label for="ticket-message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Describe your issue</label>
                        <textarea id="ticket-message" name="ticket-message" rows="5" required class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Submit Ticket</button>
                </form>
            </div>
        </div>

        <!-- Ticket History -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Your Ticket History</h2>
                <div class="space-y-4">
                    <?php if (empty($userTickets)): ?>
                        <p class="text-gray-500 dark:text-gray-400">You have no open or past support tickets.</p>
                    <?php else: ?>
                        <?php foreach ($userTickets as $ticket): ?>
                            <div class="bg-gray-50 dark:bg-gray-800/60 p-4 rounded-lg flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($ticket['subject']); ?></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Last Updated: <?php echo date("M j, Y", strtotime($ticket['last_updated'])); ?></p>
                                </div>
                                <?php
                                    $statusClass = 'bg-red-100 text-red-800'; // Open
                                    if ($ticket['status'] === 'In Progress') $statusClass = 'bg-yellow-100 text-yellow-800';
                                    if ($ticket['status'] === 'Resolved') $statusClass = 'bg-green-100 text-green-800';
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($ticket['status']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
