<?php include 'admin-header.php'; ?>

<?php
// Mock data for the admin dashboard
$stats = [
    'total_users' => 1250,
    'active_courses' => 15,
    'monthly_revenue' => 42500,
    'support_tickets' => 8,
];
?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Admin Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800/50 p-6 rounded-xl shadow-md flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Users</p>
                <p class="text-3xl font-bold text-gray-800 dark:text-white"><?php echo number_format($stats['total_users']); ?></p>
            </div>
            <i class="fa-solid fa-users text-blue-500 text-3xl"></i>
        </div>
        <div class="bg-white dark:bg-gray-800/50 p-6 rounded-xl shadow-md flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Active Courses</p>
                <p class="text-3xl font-bold text-gray-800 dark:text-white"><?php echo number_format($stats['active_courses']); ?></p>
            </div>
            <i class="fa-solid fa-book-open text-green-500 text-3xl"></i>
        </div>
        <div class="bg-white dark:bg-gray-800/50 p-6 rounded-xl shadow-md flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Monthly Revenue</p>
                <p class="text-3xl font-bold text-gray-800 dark:text-white">$<?php echo number_format($stats['monthly_revenue']); ?></p>
            </div>
            <i class="fa-solid fa-dollar-sign text-yellow-500 text-3xl"></i>
        </div>
        <div class="bg-white dark:bg-gray-800/50 p-6 rounded-xl shadow-md flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Support Tickets</p>
                <p class="text-3xl font-bold text-gray-800 dark:text-white"><?php echo number_format($stats['support_tickets']); ?></p>
            </div>
            <i class="fa-solid fa-headset text-red-500 text-3xl"></i>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="#" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg text-center transition-colors">Manage Users</a>
            <a href="#" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg text-center transition-colors">Add New Course</a>
            <a href="#" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-4 rounded-lg text-center transition-colors">View Reports</a>
            <a href="#" class="bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg text-center transition-colors">Post Announcement</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
