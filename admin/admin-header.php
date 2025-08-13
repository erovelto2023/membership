<?php
    // Basic session start for a logged-in state (in a real app, this would be more secure)
    session_start();
    // For demonstration, let's assume login is successful
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: ../adminlogin.php");
        exit;
    }
    $currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans">
    <div class="flex h-screen">
        <!-- Admin Sidebar -->
        <aside class="bg-gray-800 text-white w-64 flex-shrink-0 flex flex-col">
            <div class="flex items-center justify-center h-16 bg-gray-900">
                <h1 class="text-xl font-bold">Admin Panel</h1>
            </div>
            <nav class="flex-1 px-3 py-4">
                <ul>
                    <?php
                        $adminNavItems = [
                            ['id' => 'admindashboard.php', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
                            ['id' => 'admin-users.php', 'icon' => 'fa-users', 'label' => 'User Management'],
                            ['id' => 'admin-courses.php', 'icon' => 'fa-book-open', 'label' => 'Course Management'],
                            ['id' => 'admin-forum.php', 'icon' => 'fa-comments', 'label' => 'Forum Management'],
                            ['id' => 'admin-support.php', 'icon' => 'fa-headset', 'label' => 'Support Tickets'],
                            ['id' => 'admin-quizzes.php', 'icon' => 'fa-question-circle', 'label' => 'Quiz Management'],
                            ['id' => 'admin-challenges.php', 'icon' => 'fa-bullseye', 'label' => 'Challenge Management'],
                            ['id' => 'admin-niches.php', 'icon' => 'fa-store', 'label' => 'Businesses & Niches'],
                            ['id' => 'admin-calendar.php', 'icon' => 'fa-calendar-alt', 'label' => 'Calendar Management'],
                            ['id' => 'admin-knowledge-base.php', 'icon' => 'fa-book-atlas', 'label' => 'Knowledge Base'],
                            ['id' => 'admin-analytics.php', 'icon' => 'fa-chart-line', 'label' => 'Analytics'],
                            ['id' => 'admin-settings.php', 'icon' => 'fa-cog', 'label' => 'Settings'],
                        ];

                        foreach ($adminNavItems as $item) {
                            $isActive = $currentPage == $item['id'];
                            $activeClass = $isActive ? 'bg-blue-600' : 'hover:bg-gray-700';
                            echo "<li>
                                    <a href='{$item['id']}' class='flex items-center p-3 my-1 rounded-lg transition-colors {$activeClass}'>
                                        <i class='fa-solid {$item['icon']} w-5 text-center'></i>
                                        <span class='ml-4 font-medium'>{$item['label']}</span>
                                    </a>
                                  </li>";
                        }
                    ?>
                </ul>
            </nav>
            <div class="p-4 border-t border-gray-700">
                 <a href="../index.php" class="flex items-center p-3 my-1 rounded-lg transition-colors hover:bg-gray-700">
                    <i class="fa-solid fa-arrow-left w-5 text-center"></i>
                    <span class="ml-4 font-medium">Back to Main Site</span>
                </a>
            </div>
        </aside>
        <div class="flex-1 flex flex-col overflow-hidden">
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
