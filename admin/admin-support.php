<?php 
// --- FORM SUBMISSION LOGIC (MOVED TO TOP) ---
include '../db.php'; // DB connection first

session_start(); // Start session before any output
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../adminlogin.php");
    exit;
}

// Handle form submissions for replying and updating status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ticket'])) {
    $ticketId = $_POST['ticket_id'];
    $replyMessage = $_POST['reply-message'];
    $newStatus = $_POST['ticket-status'];
    $adminUserId = $_SESSION['user_id']; // Use the logged-in admin's ID

    // Add reply to database
    if (!empty($replyMessage)) {
        $stmt = $conn->prepare("INSERT INTO ticket_replies (ticket_id, user_id, reply_message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $ticketId, $adminUserId, $replyMessage);
        $stmt->execute();
    }

    // Update ticket status
    $stmt = $conn->prepare("UPDATE support_tickets SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $ticketId);
    $stmt->execute();
    
    header("Location: admin-support.php");
    exit();
}

// --- PAGE CONTENT ---
include 'admin-header.php'; 

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$ticketId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$selectedTicket = null;

// Fetch all tickets
$all_tickets_sql = "SELECT st.*, u.username FROM support_tickets st JOIN users u ON st.user_id = u.id ORDER BY st.last_updated DESC";
$all_tickets_result = $conn->query($all_tickets_sql);

if ($ticketId) {
    // Fetch a single ticket and its replies
    $stmt = $conn->prepare("SELECT st.*, u.username FROM support_tickets st JOIN users u ON st.user_id = u.id WHERE st.id = ?");
    $stmt->bind_param("i", $ticketId);
    $stmt->execute();
    $result = $stmt->get_result();
    $selectedTicket = $result->fetch_assoc();
}
?>

<div>
    <?php if ($action === 'list'): ?>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Support Ticket System</h1>
        <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">User</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Subject</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Date Submitted</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($ticket = $all_tickets_result->fetch_assoc()): ?>
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="p-4 font-medium text-gray-800 dark:text-white"><?php echo htmlspecialchars($ticket['username']); ?></td>
                            <td class="p-4 text-gray-600 dark:text-gray-300"><?php echo htmlspecialchars($ticket['subject']); ?></td>
                            <td class="p-4 text-gray-600 dark:text-gray-300"><?php echo date("M j, Y", strtotime($ticket['created_at'])); ?></td>
                            <td class="p-4">
                                <?php
                                    $statusClass = 'bg-red-100 text-red-800'; // Open
                                    if ($ticket['status'] === 'In Progress') $statusClass = 'bg-yellow-100 text-yellow-800';
                                    if ($ticket['status'] === 'Resolved') $statusClass = 'bg-green-100 text-green-800';
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($ticket['status']); ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <a href="?action=view&id=<?php echo $ticket['id']; ?>" class="text-blue-500 hover:underline">View/Reply</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($action === 'view' && $selectedTicket): ?>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Viewing Ticket #<?php echo $selectedTicket['id']; ?></h1>
        <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
            <div class="mb-4">
                <p><strong>From:</strong> <?php echo htmlspecialchars($selectedTicket['username']); ?></p>
                <p><strong>Subject:</strong> <?php echo htmlspecialchars($selectedTicket['subject']); ?></p>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <p class="text-gray-700 dark:text-gray-300"><?php echo nl2br(htmlspecialchars($selectedTicket['message'])); ?></p>
            </div>

            <form action="admin-support.php" method="POST" class="mt-6">
                <input type="hidden" name="update_ticket" value="1">
                <input type="hidden" name="ticket_id" value="<?php echo $selectedTicket['id']; ?>">
                <div>
                    <label for="reply-message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Reply</label>
                    <textarea id="reply-message" name="reply-message" rows="6" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg"></textarea>
                </div>
                <div class="mt-4 flex justify-between items-center">
                    <div>
                        <label for="ticket-status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Update Status</label>
                        <select id="ticket-status" name="ticket-status" class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                            <option <?php echo $selectedTicket['status'] === 'Open' ? 'selected' : ''; ?>>Open</option>
                            <option <?php echo $selectedTicket['status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option <?php echo $selectedTicket['status'] === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                        </select>
                    </div>
                    <div class="space-x-2">
                        <a href="admin-support.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Back to List</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Send Reply & Update</button>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>

