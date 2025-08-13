<?php include 'header.php'; ?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Live Class: Advanced Copywriting Workshop</h1>
    <p class="text-gray-500 dark:text-gray-400 mb-6">Welcome! The class will begin shortly.</p>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Main Stage -->
        <div class="lg:col-span-9">
            <div class="aspect-video bg-black rounded-xl shadow-lg flex items-center justify-center text-white">
                <!-- Placeholder for live video stream -->
                <p>Live video stream will appear here.</p>
            </div>
        </div>

        <!-- Interactive Sidebar -->
        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md h-full flex flex-col">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-800 dark:text-white">Chat & Q&A</h2>
                </div>
                <div class="flex-1 p-4 space-y-4 overflow-y-auto">
                    <!-- Placeholder for chat messages -->
                    <div class="text-sm text-gray-500 dark:text-gray-400">Chat messages will appear here...</div>
                </div>
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <!-- Placeholder for message input -->
                    <input type="text" placeholder="Type your message..." class="w-full p-2 border rounded-lg">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Post-Class Resources -->
    <div class="mt-8 bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Post-Class Resources</h2>
        <div class="border border-dashed border-gray-300 rounded-lg p-12 text-center">
            <p class="text-gray-400">After the class, the replay video, transcript, and downloadable files will be available here.</p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
