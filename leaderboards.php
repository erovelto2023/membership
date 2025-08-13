<?php include 'header.php'; ?>

<?php
// Mock data for the leaderboard and user stats
$leaderboardData = [
    ['rank' => 1, 'name' => 'John Smith', 'points' => 2100, 'avatar' => 'https://placehold.co/40x40/34D399/FFFFFF?text=J'],
    ['rank' => 2, 'name' => 'Jane Doe', 'points' => 1850, 'avatar' => 'https://placehold.co/40x40/FBBF24/FFFFFF?text=J'],
    ['rank' => 3, 'name' => 'Alex Morgan', 'points' => 1250, 'avatar' => 'https://placehold.co/40x40/60A5FA/FFFFFF?text=A'],
    ['rank' => 4, 'name' => 'Sam Wilson', 'points' => 980, 'avatar' => 'https://placehold.co/40x40/F472B6/FFFFFF?text=S'],
    ['rank' => 5, 'name' => 'Emily White', 'points' => 850, 'avatar' => 'https://placehold.co/40x40/A78BFA/FFFFFF?text=E'],
];

$currentUser = [
    'name' => 'Alex Morgan',
    'avatar' => 'https://placehold.co/100x100/E2E8F0/4A5568?text=A',
    'rank' => 3,
    'points' => 1250,
    'level' => 'Pro Marketer',
    'nextLevelPoints' => 2500,
];

$progressPercentage = ($currentUser['points'] / $currentUser['nextLevelPoints']) * 100;
?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Leaderboards & Gamification</h1>
    <p class="text-gray-500 dark:text-gray-400 mb-6">See who's at the top of their game this week!</p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Leaderboard Table -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200">Weekly Top Contributors</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-gray-800/60">
                            <tr>
                                <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Rank</th>
                                <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">User</th>
                                <th class="p-4 font-semibold text-gray-600 dark:text-gray-300 text-right">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaderboardData as $user): ?>
                                <tr class="border-t border-gray-200 dark:border-gray-700 <?php if($user['name'] === $currentUser['name']) echo 'bg-blue-50 dark:bg-blue-900/20'; ?>">
                                    <td class="p-4 font-bold text-lg text-gray-700 dark:text-gray-200 text-center w-16"><?php echo $user['rank']; ?></td>
                                    <td class="p-4">
                                        <div class="flex items-center space-x-3">
                                            <img src="<?php echo $user['avatar']; ?>" alt="<?php echo htmlspecialchars($user['name']); ?>" class="w-10 h-10 rounded-full" />
                                            <span class="font-medium text-gray-800 dark:text-white"><?php echo htmlspecialchars($user['name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="p-4 font-semibold text-blue-600 dark:text-blue-400 text-right"><?php echo number_format($user['points']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- User Stats Sidebar -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Your Rank</h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <img src="<?php echo $currentUser['avatar']; ?>" alt="<?php echo htmlspecialchars($currentUser['name']); ?>" class="w-16 h-16 rounded-full" />
                        <span class="absolute -bottom-1 -right-1 bg-yellow-400 text-white p-1 rounded-full flex items-center justify-center w-6 h-6">
                            <i class="fa-solid fa-trophy text-xs"></i>
                        </span>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-gray-800 dark:text-white">#<?php echo $currentUser['rank']; ?></p>
                        <p class="text-gray-500 dark:text-gray-400">This Week</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Your Level</h2>
                <p class="text-gray-600 dark:text-gray-300">You are a <span class="font-bold text-blue-500"><?php echo htmlspecialchars($currentUser['level']); ?></span></p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                    Next Level: <span class="font-semibold">Master</span> at <?php echo number_format($currentUser['nextLevelPoints']); ?> points.
                </p>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mt-2">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo $progressPercentage; ?>%"></div>
                </div>
                 <p class="text-xs text-gray-400 dark:text-gray-500 text-right mt-1"><?php echo number_format($currentUser['points']); ?> / <?php echo number_format($currentUser['nextLevelPoints']); ?></p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
