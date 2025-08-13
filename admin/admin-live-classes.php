<?php include 'admin-header.php'; ?>

<?php
// Mock data for live classes
$liveClasses = [
    ['id' => 1, 'title' => 'Advanced Copywriting Workshop', 'date' => '2025-09-10', 'status' => 'Scheduled'],
    ['id' => 2, 'title' => 'Live Q&A: SEO Trends', 'date' => '2025-09-17', 'status' => 'Scheduled'],
];

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
?>

<div>
    <?php if ($action === 'list'): ?>
        <!-- Class List View -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Live Class Management</h1>
            <a href="?action=new" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Schedule New Class</span>
            </a>
        </div>
        <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Class Title</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Date</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($liveClasses as $class): ?>
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="p-4 font-medium text-gray-800 dark:text-white"><?php echo htmlspecialchars($class['title']); ?></td>
                            <td class="p-4 text-gray-600 dark:text-gray-300"><?php echo date("M j, Y", strtotime($class['date'])); ?></td>
                            <td class="p-4"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><?php echo htmlspecialchars($class['status']); ?></span></td>
                            <td class="p-4"><a href="?action=edit&id=<?php echo $class['id']; ?>" class="text-blue-500 hover:underline">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($action === 'edit' || $action === 'new'): ?>
        <!-- Add/Edit Class View -->
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6"><?php echo $action === 'edit' ? 'Edit Live Class' : 'Schedule New Live Class'; ?></h1>
        <form class="space-y-6">
            <!-- Scheduling & Access -->
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                 <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Scheduling & Access</h2>
                 <!-- Placeholder for scheduling and access control fields -->
                 <div class="border border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <p class="text-gray-400">Fields for Title, Date, Time, Recurring Options, Access Control (Public/Private), and Participant Limits will go here.</p>
                </div>
            </div>
            <!-- Content & Resources -->
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                 <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Content & Resources</h2>
                 <!-- Placeholder for content upload -->
                 <div class="border border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <p class="text-gray-400">Interface to upload slide decks and downloadable resources (PDFs, templates) will be built here.</p>
                </div>
            </div>
             <!-- Engagement Tools -->
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                 <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Engagement Tools</h2>
                 <!-- Placeholder for engagement tools -->
                 <div class="border border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <p class="text-gray-400">Tools to pre-load polls, quizzes, and configure breakout rooms will be built here.</p>
                </div>
            </div>
            <div class="flex justify-end space-x-4">
                <a href="admin-live-classes.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Save Class</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
