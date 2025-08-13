<?php include 'admin-header.php'; ?>

<?php
// Mock data for challenges
$challenges = [
    [
        'id' => 1,
        'title' => 'Post Your First TikTok Ad',
        'type' => 'Daily Challenge',
        'tasks' => 1,
    ],
    [
        'id' => 2,
        'title' => 'Build a High-Converting Lead Magnet Page',
        'type' => 'Weekly Challenge',
        'tasks' => 5,
    ],
    [
        'id' => 3,
        'title' => '30-Day Funnel Building Sprint',
        'type' => 'Action Plan',
        'tasks' => 30,
    ],
];

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$challengeId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$selectedChallenge = null;

if ($challengeId) {
    foreach ($challenges as $challenge) {
        if ($challenge['id'] === $challengeId) {
            $selectedChallenge = $challenge;
            break;
        }
    }
}
?>

<div>
    <?php if ($action === 'list'): ?>
        <!-- Challenge List View -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Challenge Management</h1>
            <a href="?action=new" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition-colors flex items-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Add New Challenge</span>
            </a>
        </div>
        <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Title</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Type</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Tasks</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($challenges as $challenge): ?>
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="p-4 font-medium text-gray-800 dark:text-white"><?php echo htmlspecialchars($challenge['title']); ?></td>
                            <td class="p-4">
                                <?php
                                    $typeClass = 'bg-yellow-100 text-yellow-800';
                                    if ($challenge['type'] === 'Action Plan') {
                                        $typeClass = 'bg-purple-100 text-purple-800';
                                    } elseif ($challenge['type'] === 'Weekly Challenge') {
                                        $typeClass = 'bg-blue-100 text-blue-800';
                                    }
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $typeClass; ?>">
                                    <?php echo htmlspecialchars($challenge['type']); ?>
                                </span>
                            </td>
                            <td class="p-4 text-gray-600 dark:text-gray-300"><?php echo $challenge['tasks']; ?></td>
                            <td class="p-4">
                                <a href="?action=edit&id=<?php echo $challenge['id']; ?>" class="text-blue-500 hover:underline">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($action === 'edit' || $action === 'new'): ?>
        <!-- Add/Edit Challenge View -->
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">
            <?php echo $action === 'edit' ? 'Edit Challenge' : 'Create New Challenge'; ?>
        </h1>
        <form action="#" method="POST" class="space-y-6">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="challenge-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Challenge Title</label>
                        <input type="text" id="challenge-title" name="challenge-title" value="<?php echo htmlspecialchars($selectedChallenge['title'] ?? ''); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg">
                    </div>
                    <div>
                        <label for="challenge-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                        <select id="challenge-type" name="challenge-type" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <option <?php echo ($selectedChallenge['type'] ?? '') === 'Daily Challenge' ? 'selected' : ''; ?>>Daily Challenge</option>
                            <option <?php echo ($selectedChallenge['type'] ?? '') === 'Weekly Challenge' ? 'selected' : ''; ?>>Weekly Challenge</option>
                            <option <?php echo ($selectedChallenge['type'] ?? '') === 'Action Plan' ? 'selected' : ''; ?>>Action Plan</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tasks -->
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Challenge Tasks/Steps</h2>
                <div id="tasks-container" class="space-y-3">
                    <!-- JS will populate this area -->
                </div>
                <button type="button" id="add-task-btn" class="mt-4 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg">
                    <i class="fa-solid fa-plus mr-2"></i>Add Task
                </button>
            </div>
            
            <div class="flex justify-end space-x-4">
                <a href="admin-challenges.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Save Challenge</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tasksContainer = document.getElementById('tasks-container');
    const addTaskBtn = document.getElementById('add-task-btn');
    
    if (addTaskBtn) {
        addTaskBtn.addEventListener('click', () => {
            addTask();
        });
    }

    function addTask(title = '', description = '') {
        const taskIndex = tasksContainer.children.length;
        const taskElement = document.createElement('div');
        taskElement.className = 'p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700';
        
        taskElement.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <input type="text" name="tasks[${taskIndex}][title]" placeholder="Task Title" class="font-semibold bg-transparent w-full focus:outline-none dark:text-white" value="${escapeHTML(title)}">
                <button type="button" class="text-gray-400 hover:text-red-500 remove-task-btn">&times;</button>
            </div>
            <textarea name="tasks[${taskIndex}][description]" placeholder="Task description..." class="w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm" rows="2">${escapeHTML(description)}</textarea>
        `;
        
        tasksContainer.appendChild(taskElement);

        taskElement.querySelector('.remove-task-btn').addEventListener('click', () => {
            taskElement.remove();
        });
    }

    function escapeHTML(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // If editing, you would load existing tasks here. For demonstration:
    if ('<?php echo $action; ?>' === 'edit' && <?php echo json_encode($selectedChallenge); ?>) {
        // In a real app, you would fetch and loop through the tasks for the selected challenge.
        // For now, let's add a placeholder task.
        addTask('Example Task 1', 'This is the description for the first task.');
        addTask('Example Task 2', 'This is the description for the second task.');
    } else if ('<?php echo $action; ?>' === 'new') {
        addTask(); // Start with one empty task
    }
});
</script>

<?php include 'footer.php'; ?>
