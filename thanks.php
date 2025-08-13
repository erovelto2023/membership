<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You!</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Use the Inter font family */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <!-- Main container for centering content -->
    <div class="flex items-center justify-center min-h-screen">
        
        <!-- Card container -->
        <div class="bg-white dark:bg-gray-800 p-8 sm:p-12 rounded-xl shadow-lg max-w-md w-full text-center">
            
            <!-- Checkmark Icon -->
            <div class="mb-6">
                <svg class="w-16 h-16 mx-auto text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            
            <!-- Main Heading -->
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 dark:text-white mb-3">
                Thank You!
            </h1>
            
            <!-- Subtitle/Message -->
            <p class="text-gray-600 dark:text-gray-300 text-lg mb-8">
                <?php
                    // You can add PHP logic here if needed.
                    // For example, to thank a user by name from a form submission:
                    // $name = htmlspecialchars($_GET['name'] ?? 'Guest');
                    // echo "Thank you, " . $name . "! ";
                ?>
                Your message has been received. We appreciate you reaching out.
            </p>
            
            <!-- Back to Home Button -->
            <a href="/" class="inline-block bg-blue-600 text-white font-semibold py-3 px-8 rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition-transform transform hover:scale-105">
                Return to Homepage
            </a>
            
        </div>
        
    </div>
</body>
</html>
