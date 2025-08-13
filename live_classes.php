<?php include 'header.php'; ?>

<?php
// Mock event data
$mockEvents = [
    [
        'id' => 1, 
        'title' => 'Live Q&A with SEO Expert Neil Patel', 
        'type' => 'Webinar', 
        'date' => '2025-08-15 18:00:00', 
        'rsvpd' => true
    ],
    [
        'id' => 2, 
        'title' => 'Workshop: Building Your First Sales Funnel', 
        'type' => 'Workshop', 
        'date' => '2025-08-22 15:00:00', 
        'rsvpd' => false
    ],
    [
        'id' => 3, 
        'title' => 'Expert Panel: The Future of AI in Marketing', 
        'type' => 'Special Event', 
        'date' => '2025-09-05 19:00:00', 
        'rsvpd' => false
    ],
];

$pastEvents = [
    ['title' => 'Advanced Copywriting Secrets with Ann Handley'],
    ['title' => 'TikTok Ads for E-commerce Masterclass'],
    ['title' => 'Networking Night: Agency Owners Mixer'],
];
?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Live Classes & Events</h1>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content: Schedule -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-gray-200">Upcoming Schedule</h2>
                <div class="space-y-4">
                    <?php foreach ($mockEvents as $event): ?>
                        <div class="bg-gray-50 dark:bg-gray-800/60 p-4 rounded-lg flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="bg-blue-100 dark:bg-blue-900/50 p-3 rounded-full">
                                    <i class="fa-solid fa-calendar-alt text-blue-500 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        <?php 
                                            $date = new DateTime($event['date']);
                                            echo $date->format('F j, Y \a\t g:i A');
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <?php if ($event['rsvpd']): ?>
                                <button class="font-semibold py-2 px-4 rounded-lg text-sm transition-colors bg-green-100 text-green-700 dark:bg-green-900/70 dark:text-green-300 w-full sm:w-auto">
                                    <i class="fa-solid fa-check mr-2"></i> RSVP'd
                                </button>
                            <?php else: ?>
                                <button class="font-semibold py-2 px-4 rounded-lg text-sm transition-colors bg-blue-500 text-white hover:bg-blue-600 w-full sm:w-auto">
                                    RSVP Now
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar: Replays & Special Events -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-gray-200">Event Replays</h2>
                <ul class="space-y-3">
                    <?php foreach ($pastEvents as $replay): ?>
                        <li>
                            <a href="#" class="flex items-center space-x-3 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                <i class="fa-solid fa-video w-5 text-center"></i>
                                <span><?php echo htmlspecialchars($replay['title']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-gray-200">Special Events</h2>
                 <ul class="space-y-3">
                    <li>
                        <a href="#" class="flex items-center space-x-3 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <i class="fa-solid fa-star w-5 text-center text-yellow-500"></i>
                            <span>Monthly Challenge Kickoff</span>
                        </a>
                    </li>
                     <li>
                        <a href="#" class="flex items-center space-x-3 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <i class="fa-solid fa-users w-5 text-center"></i>
                            <span>Community Networking Night</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>