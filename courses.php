<?php 
include 'header.php'; 
include 'db.php';

$currentUserId = $_SESSION['user_id'];

// Fetch all courses for the library view
$courses_result = $conn->query("SELECT * FROM courses WHERE status = 'Published'");
if (!$courses_result) {
    die("Error fetching courses: " . $conn->error);
}
$allCourses = $courses_result->fetch_all(MYSQLI_ASSOC);

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$selectedCourse = null;
$isEnrolled = false;
$courseModules = [];

if ($courseId) {
    // Fetch the selected course details
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();
    $selectedCourse = $result->fetch_assoc();

    if ($selectedCourse) {
        // Check if the user is enrolled in this course
        $enrollStmt = $conn->prepare("SELECT id FROM user_courses WHERE user_id = ? AND course_id = ?");
        $enrollStmt->bind_param("ii", $currentUserId, $courseId);
        $enrollStmt->execute();
        if ($enrollStmt->get_result()->num_rows > 0) {
            $isEnrolled = true;
        }

        // If enrolled, fetch modules and lessons
        if ($isEnrolled) {
            $moduleStmt = $conn->prepare("SELECT * FROM modules WHERE course_id = ? ORDER BY module_order ASC");
            $moduleStmt->bind_param("i", $courseId);
            $moduleStmt->execute();
            $modulesResult = $moduleStmt->get_result();
            while ($module = $modulesResult->fetch_assoc()) {
                $lessonStmt = $conn->prepare("SELECT * FROM lessons WHERE module_id = ? ORDER BY lesson_order ASC");
                $lessonStmt->bind_param("i", $module['id']);
                $lessonStmt->execute();
                $lessonsResult = $lessonStmt->get_result();
                $module['lessons'] = $lessonsResult->fetch_all(MYSQLI_ASSOC);
                $courseModules[] = $module;
            }
        }
    }
}
?>

<div>
    <?php if ($selectedCourse): ?>
        <?php if ($isEnrolled): ?>
            <!-- Course Player View (for enrolled students) -->
            <?php if (!empty($courseModules) && !empty($courseModules[0]['lessons'])): ?>
                <div>
                    <a href="courses.php" class="flex items-center space-x-2 text-blue-600 dark:text-blue-400 font-semibold mb-4 hover:underline">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Back to Courses</span>
                    </a>
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <div class="lg:col-span-8">
                            <div class="aspect-video bg-black rounded-xl overflow-hidden shadow-lg">
                                <iframe class="w-full h-full" src="<?php echo htmlspecialchars($courseModules[0]['lessons'][0]['video_url']); ?>" title="Video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                            <div class="mt-6">
                                <h1 id="lesson-title" class="text-3xl font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($courseModules[0]['lessons'][0]['title']); ?></h1>
                            </div>
                        </div>
                        <div class="lg:col-span-4">
                             <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md sticky top-6">
                                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                    <h2 class="font-bold text-xl text-gray-800 dark:text-white"><?php echo htmlspecialchars($selectedCourse['title']); ?></h2>
                                </div>
                                <div class="p-2 max-h-[calc(100vh-200px)] overflow-y-auto">
                                    <?php foreach($courseModules as $module): ?>
                                        <div>
                                            <h3 class="font-bold text-gray-700 dark:text-gray-300 p-2 mt-2"><?php echo htmlspecialchars($module['title']); ?></h3>
                                            <ul>
                                                <?php foreach($module['lessons'] as $lesson): ?>
                                                    <li>
                                                        <button class="lesson-btn w-full text-left flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50" data-title="<?php echo htmlspecialchars($lesson['title']); ?>">
                                                            <i class="fa-solid fa-play-circle text-gray-400"></i>
                                                            <span class="flex-1 text-gray-800 dark:text-gray-200"><?php echo htmlspecialchars($lesson['title']); ?></span>
                                                        </button>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                 <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Welcome to <?php echo htmlspecialchars($selectedCourse['title']); ?></h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">The instructor is still building this course. Check back soon!</p>
                    <a href="courses.php" class="mt-4 inline-block text-blue-500 hover:underline">&larr; Back to Course Library</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Course Overview Page (for non-enrolled students) -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-lg overflow-hidden">
                    <img src="<?php echo htmlspecialchars($selectedCourse['image_url']); ?>" alt="<?php echo htmlspecialchars($selectedCourse['title']); ?>" class="w-full h-64 object-cover">
                    <div class="p-8">
                        <h1 class="text-4xl font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($selectedCourse['title']); ?></h1>
                        <p class="mt-4 text-lg text-gray-600 dark:text-gray-300"><?php echo htmlspecialchars($selectedCourse['description']); ?></p>
                        <form action="enroll.php" method="POST" class="mt-8">
                            <input type="hidden" name="course_id" value="<?php echo $selectedCourse['id']; ?>">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg text-xl">Enroll Now</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Course Library View -->
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Course Library</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($allCourses as $course): ?>
                <a href="?id=<?php echo $course['id']; ?>" class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md overflow-hidden block hover:-translate-y-1 transition-transform">
                    <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="w-full h-40 object-cover">
                    <div class="p-5">
                        <span class="text-xs font-semibold text-blue-500"><?php echo htmlspecialchars($course['category']); ?></span>
                        <h3 class="text-lg font-bold mt-1 text-gray-800 dark:text-white"><?php echo htmlspecialchars($course['title']); ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>


