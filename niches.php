<?php include 'header.php'; ?>

<?php
// Mock data for online businesses and niches
$businessNiches = [
    [
        'title' => 'Affiliate Marketing Niche Site',
        'description' => 'Build a content-rich website around a specific topic and earn commissions by promoting products.',
        'tags' => ['Beginner Friendly', 'Content Creation', 'Low Startup Cost'],
        'icon' => 'fa-bullhorn',
    ],
    [
        'title' => 'E-commerce Dropshipping Store',
        'description' => 'Sell physical products online without holding any inventory. Orders are fulfilled directly by the supplier.',
        'tags' => ['Intermediate', 'High Scalability', 'Customer Service'],
        'icon' => 'fa-shipping-fast',
    ],
    [
        'title' => 'Digital Product Creator',
        'description' => 'Create and sell your own digital products like ebooks, templates, or software.',
        'tags' => ['High Profit Margin', 'Creative', 'Scalable'],
        'icon' => 'fa-file-alt',
    ],
    [
        'title' => 'SaaS (Software as a Service)',
        'description' => 'Develop a subscription-based software to solve a specific problem for a target audience.',
        'tags' => ['Advanced', 'Recurring Revenue', 'Technical'],
        'icon' => 'fa-laptop-code',
    ],
    [
        'title' => 'YouTube Content Creator',
        'description' => 'Build an audience by creating valuable or entertaining video content and monetize through ads and sponsorships.',
        'tags' => ['Creative', 'Brand Building', 'Long-Term'],
        'icon' => 'fa-youtube',
    ],
    [
        'title' => 'Online Course Instructor',
        'description' => 'Package your knowledge into an online course and sell it to students worldwide.',
        'tags' => ['Expertise-Based', 'High Profit', 'Community'],
        'icon' => 'fa-chalkboard-teacher',
    ],
];
?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Online Businesses & Niches</h1>
    <p class="text-gray-500 dark:text-gray-400 mb-6">Explore proven business models and find your profitable niche.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($businessNiches as $niche): ?>
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6 flex flex-col">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 dark:bg-blue-900/50 p-3 rounded-full">
                        <i class="fa-solid <?php echo $niche['icon']; ?> text-blue-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($niche['title']); ?></h3>
                </div>
                <p class="text-gray-600 dark:text-gray-300 mt-4 flex-1"><?php echo htmlspecialchars($niche['description']); ?></p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <?php foreach ($niche['tags'] as $tag): ?>
                        <span class="text-xs bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-full"><?php echo htmlspecialchars($tag); ?></span>
                    <?php endforeach; ?>
                </div>
                <a href="#" class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center space-x-2 transition-colors">
                    <span>Learn More</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
