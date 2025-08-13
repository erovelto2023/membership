<?php 
// --- FORM SUBMISSION LOGIC (MOVED TO TOP) ---
include '../db.php'; // DB connection first

session_start(); // Start session before any output
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../adminlogin.php");
    exit;
}

$currentUserId = $_SESSION['user_id'];

// --- LOGIC TO START A NEW CONVERSATION ---
if (isset($_GET['start_with_user'])) {
    $targetUserId = (int)$_GET['start_with_user'];

    // Find if a conversation already exists between the two users
    $findConvoSql = "SELECT cp1.conversation_id FROM conversation_participants cp1
                     JOIN conversation_participants cp2 ON cp1.conversation_id = cp2.conversation_id
                     WHERE cp1.user_id = ? AND cp2.user_id = ?";
    $stmt = $conn->prepare($findConvoSql);
    $stmt->bind_param("ii", $currentUserId, $targetUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingConvo = $result->fetch_assoc();

    if ($existingConvo) {
        header("Location: admin-messaging.php?conversation_id=" . $existingConvo['conversation_id']);
    } else {
        $conn->query("INSERT INTO conversations () VALUES ()");
        $newConversationId = $conn->insert_id;
        $stmt = $conn->prepare("INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?, ?), (?, ?)");
        $stmt->bind_param("iiii", $newConversationId, $currentUserId, $newConversationId, $targetUserId);
        $stmt->execute();
        header("Location: admin-messaging.php?conversation_id=" . $newConversationId);
    }
    exit();
}

// --- PAGE CONTENT & DATA FETCHING ---
include 'admin-header.php'; 

$activeConversationId = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : null;

// CORRECTED QUERY: Fetch all conversations for the current admin, showing the OTHER participant
$conversationsSql = "
    SELECT
        c.id,
        other_user.username AS name,
        other_user.avatar_url AS avatar
    FROM conversations c
    JOIN conversation_participants cp1 ON c.id = cp1.conversation_id
    JOIN conversation_participants cp2 ON c.id = cp2.conversation_id
    JOIN users other_user ON cp2.user_id = other_user.id
    WHERE
        cp1.user_id = ? 
        AND cp2.user_id != ?
    ORDER BY c.id DESC
";
$stmt = $conn->prepare($conversationsSql);
$stmt->bind_param("ii", $currentUserId, $currentUserId);
$stmt->execute();
$conversationsResult = $stmt->get_result();
$conversations = $conversationsResult->fetch_all(MYSQLI_ASSOC);

if (!$activeConversationId && !empty($conversations)) {
    $activeConversationId = $conversations[0]['id'];
}

$activeConversation = null;
if ($activeConversationId) {
    foreach($conversations as $convo) {
        if ($convo['id'] == $activeConversationId) {
            $activeConversation = $convo;
            break;
        }
    }
}

$allUsers = $conn->query("SELECT id, username, avatar_url FROM users WHERE id != $currentUserId AND is_admin = 0")->fetch_all(MYSQLI_ASSOC); // Only show students
?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Admin Messaging</h1>
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 h-[calc(100vh-220px)]">
        <div class="md:col-span-4 lg:col-span-3">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md h-full flex flex-col">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <button id="new-message-btn" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-lg text-sm">New Message</button>
                </div>
                <div class="flex-1 overflow-y-auto">
                    <?php foreach ($conversations as $convo): ?>
                        <a href="?conversation_id=<?php echo $convo['id']; ?>" class="p-4 flex items-center space-x-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer <?php echo ($activeConversationId == $convo['id']) ? 'border-l-4 border-blue-500 bg-blue-50 dark:bg-blue-900/20' : ''; ?>">
                            <img src="../<?php echo htmlspecialchars($convo['avatar'] ?? 'profile-image/default.png'); ?>" alt="<?php echo htmlspecialchars($convo['name']); ?>" class="w-12 h-12 rounded-full">
                            <div class="flex-1 overflow-hidden">
                                <h3 class="font-semibold text-gray-800 dark:text-white truncate"><?php echo htmlspecialchars($convo['name']); ?></h3>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="md:col-span-8 lg:col-span-9">
            <?php if ($activeConversation): ?>
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md h-full flex flex-col">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($activeConversation['name']); ?></h2>
                </div>
                <div id="message-container" class="flex-1 p-6 overflow-y-auto space-y-4">
                    <!-- Messages will be loaded here by JavaScript -->
                </div>
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60">
                    <form id="message-form" class="relative">
                        <input type="hidden" id="conversation_id" value="<?php echo $activeConversationId; ?>">
                        <input type="text" id="message-input" placeholder="Type a message..." class="w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 border rounded-lg pl-4 pr-12 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete="off">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-blue-600 hover:bg-blue-700 p-2 rounded-full text-white">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php else: ?>
                <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md h-full flex flex-col items-center justify-center text-center">
                    <i class="fa-solid fa-comments text-5xl text-gray-300 dark:text-gray-600"></i>
                    <h2 class="mt-4 text-xl font-semibold text-gray-700 dark:text-gray-200">Select a conversation</h2>
                    <p class="text-gray-500 dark:text-gray-400">Or start a new one to begin chatting.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- New Message Modal -->
<div id="new-message-modal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex justify-center items-center p-4 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">New Message</h2>
            <button id="close-modal-btn" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"><i class="fa-solid fa-times"></i></button>
        </div>
        <div class="p-4">
            <input type="text" id="user-search-input" placeholder="Search for a user..." class="w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
            <div id="user-search-results" class="mt-4 max-h-60 overflow-y-auto"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newMsgBtn = document.getElementById('new-message-btn');
    const newMsgModal = document.getElementById('new-message-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const userSearchInput = document.getElementById('user-search-input');
    const userSearchResults = document.getElementById('user-search-results');
    const allUsers = <?php echo json_encode($allUsers); ?>;
    const messageContainer = document.getElementById('message-container');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const conversationIdInput = document.getElementById('conversation_id');
    const currentUserId = <?php echo $currentUserId; ?>;

    function openModal() { newMsgModal.classList.remove('hidden'); }
    function closeModal() { newMsgModal.classList.add('hidden'); }

    if(newMsgBtn) newMsgBtn.addEventListener('click', openModal);
    if(closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if(newMsgModal) newMsgModal.addEventListener('click', closeModal);

    if(userSearchInput) {
        userSearchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            userSearchResults.innerHTML = '';
            if (searchTerm.length > 0) {
                const filteredUsers = allUsers.filter(user => user.username.toLowerCase().includes(searchTerm));
                filteredUsers.forEach(user => {
                    const userElement = document.createElement('a');
                    userElement.href = `?start_with_user=${user.id}`;
                    userElement.className = 'p-2 flex items-center space-x-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg cursor-pointer';
                    userElement.innerHTML = `<img src="../${user.avatar_url || 'profile-image/default.png'}" class="w-10 h-10 rounded-full"><span>${user.username}</span>`;
                    userSearchResults.appendChild(userElement);
                });
            }
        });
    }

    function renderMessages(messages) {
        if (!messageContainer) return;
        messageContainer.innerHTML = '';
        messages.forEach(msg => {
            const messageWrapper = document.createElement('div');
            messageWrapper.className = `flex ${msg.sender_id == currentUserId ? 'justify-end' : 'justify-start'}`;
            const messageBubble = document.createElement('div');
            messageBubble.className = `${msg.sender_id == currentUserId ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700'} p-3 rounded-lg max-w-md`;
            messageBubble.textContent = msg.message_text;
            messageWrapper.appendChild(messageBubble);
            messageContainer.appendChild(messageWrapper);
        });
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }

    async function fetchMessages() {
        if (!conversationIdInput) return;
        const conversationId = conversationIdInput.value;
        try {
            const response = await fetch(`../api.php?action=fetch_messages&conversation_id=${conversationId}`);
            const messages = await response.json();
            renderMessages(messages);
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    }

    if (messageForm) {
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const messageText = messageInput.value.trim();
            const conversationId = conversationIdInput.value;
            if (messageText === '') return;

            const formData = new FormData();
            formData.append('conversation_id', conversationId);
            formData.append('message_text', messageText);

            try {
                await fetch('../api.php?action=send_message', {
                    method: 'POST',
                    body: formData
                });
                messageInput.value = '';
                fetchMessages(); // Immediately fetch new messages
            } catch (error) {
                console.error('Error sending message:', error);
            }
        });
    }

    if (activeConversation) {
        fetchMessages(); // Initial load
        setInterval(fetchMessages, 3000); // Poll for new messages every 3 seconds
    }
});
</script>

<?php include '../footer.php'; ?>
