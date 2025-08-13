<?php include 'header.php'; ?>

<?php
// Mock data for challenges and action plans
$challenges = [
    [
        'title' => 'Post Your First TikTok Ad',
        'type' => 'Daily Challenge',
        'status' => 'completed',
        'progress' => 100,
    ],
    [
        'title' => 'Build a High-Converting Lead Magnet Page',
        'type' => 'Weekly Challenge',
        'status' => 'active',
        'progress' => 0,
    ],
    [
        'title' => '30-Day Funnel Building Sprint',
        'type' => 'Action Plan',
        'status' => 'in_progress',
        'progress' => 60,
    ],
    [
        'title' => 'Social Media Growth Challenge',
        'type' => 'Action Plan',
        'status' => 'in_progress',
        'progress' => 25,
    ],
];
?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Challenges & Action Plans</h1>
    <p class="text-gray-500 dark:text-gray-400 mb-6">Put your knowledge into practice with guided challenges.</p>

    <div class="space-y-6">
        <?php foreach ($challenges as $challenge): ?>
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <?php
                            $typeClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                            if ($challenge['type'] === 'Action Plan') {
                                $typeClass = 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';
                            }
                        ?>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full <?php echo $typeClass; ?>">
                            <?php echo htmlspecialchars($challenge['type']); ?>
                        </span>
                        <h3 class="text-lg font-bold mt-2 text-gray-800 dark:text-white"><?php echo htmlspecialchars($challenge['title']); ?></h3>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <?php if ($challenge['status'] === 'in_progress'): ?>
                            <div class="w-48">
                                <div class="flex justify-between items-center text-sm text-gray-500 dark:text-gray-400 mb-1">
                                    <span>Progress</span>
                                    <span><?php echo $challenge['progress']; ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo $challenge['progress']; ?>%"></div>
                                </div>
                            </div>
                        <?php elseif ($challenge['status'] === 'completed'): ?>
                            <button class="font-semibold py-2 px-4 rounded-lg text-sm bg-green-100 text-green-700 dark:bg-green-900/70 dark:text-green-300">
                                <i class="fa-solid fa-check mr-2"></i> Completed
                            </button>
                        <?php else: ?>
                            <button class="font-semibold py-2 px-4 rounded-lg text-sm bg-blue-500 text-white hover:bg-blue-600">
                                Start Challenge
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
