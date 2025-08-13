<?php include 'header.php'; ?>

<?php
// Mock data for user's notes. In a real app, this would be fetched from the database for the logged-in user.
$userNotes = [
    [
        'course_title' => 'SEO & Blogging Mastery',
        'notes' => "This course is great for understanding the fundamentals.\n\n[Lesson: How Search Engines Work]\n- Remember to focus on user intent.\n- E-A-T (Expertise, Authoritativeness, Trustworthiness) is crucial for ranking.\n\n[Lesson: Keyword Research Deep Dive]\n- Long-tail keywords have less competition and higher conversion rates.\n- Use tools like Ahrefs or SEMrush to find keyword ideas."
    ],
    [
        'course_title' => 'High-Conversion Funnel Building',
        'notes' => "Notes for the funnel building course will go here..."
    ],
];
?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">My Course Notes</h1>

    <div class="space-y-6">
        <?php if (empty($userNotes)): ?>
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-10 text-center">
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200">You haven't taken any notes yet.</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Go to a course lesson to start taking notes.</p>
            </div>
        <?php else: ?>
            <?php foreach ($userNotes as $note): ?>
                <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200"><?php echo htmlspecialchars($note['course_title']); ?></h2>
                    </div>
                    <div class="p-6">
                        <div class="prose dark:prose-invert max-w-none">
                            <pre class="whitespace-pre-wrap font-sans text-base bg-transparent p-0"><?php echo htmlspecialchars($note['notes']); ?></pre>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
