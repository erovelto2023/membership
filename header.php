<?php
    session_start();
    // Redirect to login page if the user is not logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    
    include 'db.php'; // Include the database connection
    $currentUserId = $_SESSION['user_id'];

    // Fetch the current user's data to display in the header
    $stmt = $conn->prepare("SELECT username, avatar_url FROM users WHERE id = ?");
    $stmt->bind_param("i", $currentUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Get the current page filename to set the active state in the sidebar
    $currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internet Marketing Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Custom scrollbar styles */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        .dark ::-webkit-scrollbar-track { background: #2d3748; }
        .dark ::-webkit-scrollbar-thumb { background: #718096; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white shadow-xl flex flex-col transition-all duration-300 ease-in-out w-64 hidden lg:flex">
            <div class="flex items-center justify-between p-4 h-16 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-xl font-bold text-blue-600 dark:text-blue-400 sidebar-title">IM Hub</h1>
                <button id="sidebar-toggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
            </div>
            <nav class="flex-1 px-3 py-2">
                <ul>
                    <?php
                        $navItems = [
                            ['id' => 'index.php', 'icon' => 'fa-home', 'label' => 'Dashboard'],
                            ['id' => 'courses.php', 'icon' => 'fa-book-open', 'label' => 'Courses'],
                            ['id' => 'forum.php', 'icon' => 'fa-comments', 'label' => 'Community Forum'],
                            ['id' => 'calendar.php', 'icon' => 'fa-calendar-alt', 'label' => 'Event Calendar'],
                            ['id' => 'members.php', 'icon' => 'fa-users', 'label' => 'Members Directory'],
                            ['id' => 'tools.php', 'icon' => 'fa-wrench', 'label' => 'Tools Library'],
                            ['id' => 'notes.php', 'icon' => 'fa-pencil-alt', 'label' => 'My Notes'],
                            ['id' => 'challenges.php', 'icon' => 'fa-bullseye', 'label' => 'Challenges'],
                            ['id' => 'support.php', 'icon' => 'fa-question-circle', 'label' => 'Support'],
                            ['id' => 'profile.php', 'icon' => 'fa-user', 'label' => 'My Profile'],
                        ];

                        foreach ($navItems as $item) {
                            $isActive = $currentPage == $item['id'];
                            $activeClass = $isActive ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
                            echo "<li><a href='{$item['id']}' class='flex items-center p-3 my-1 rounded-lg transition-colors {$activeClass}'><i class='fa-solid {$item['icon']} w-5 text-center'></i><span class='ml-4 font-medium sidebar-title'>{$item['label']}</span></a></li>";
                        }
                    ?>
                </ul>
            </nav>
        </aside>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white dark:bg-gray-800 shadow-sm flex items-center justify-between px-6">
                <div class="flex items-center">
                    <button id="mobile-menu-button" class="lg:hidden mr-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 hidden sm:block">
                        <?php
                            foreach ($navItems as $item) {
                                if ($currentPage == $item['id']) echo $item['label'];
                            }
                        ?>
                    </h2>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 cursor-pointer">
                        <img src="<?php echo htmlspecialchars($user['avatar_url'] ?? 'https://placehold.co/100x100/E2E8F0/4A5568?text=A'); ?>" alt="User Avatar" class="w-10 h-10 rounded-full object-cover" />
                        <div class="hidden md:block">
                            <p class="font-semibold text-sm text-gray-800 dark:text-white"><?php echo htmlspecialchars($user['username']); ?></p>
                        </div>
                    </div>
                     <a href="logout.php" class="text-sm text-gray-600 dark:text-gray-300 hover:underline" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto p-6">
