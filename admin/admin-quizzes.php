<?php 
// --- FORM SUBMISSION LOGIC ---
include '../db.php'; 
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../adminlogin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_quiz'])) {
    $quizTitle = $_POST['quiz-title'];
    $quizId = $_POST['quiz_id'] ?? null;
    $questions = $_POST['questions'] ?? [];

    if ($quizId) { // Update
        $stmt = $conn->prepare("UPDATE quizzes SET title = ? WHERE id = ?");
        $stmt->bind_param("si", $quizTitle, $quizId);
    } else { // Insert
        $stmt = $conn->prepare("INSERT INTO quizzes (title) VALUES (?)");
        $stmt->bind_param("s", $quizTitle);
    }
    $stmt->execute();
    $newQuizId = $quizId ?: $stmt->insert_id;

    // In a real app, you'd also handle deleting questions that were removed.
    
    foreach ($questions as $qIndex => $questionData) {
        $questionId = $questionData['id'] ?? null;
        $questionText = $questionData['text'];
        $questionType = $questionData['type'];

        if ($questionId) {
            $qStmt = $conn->prepare("UPDATE quiz_questions SET question_text = ?, question_type = ?, question_order = ? WHERE id = ?");
            $qStmt->bind_param("ssii", $questionText, $questionType, $qIndex, $questionId);
        } else {
            $qStmt = $conn->prepare("INSERT INTO quiz_questions (quiz_id, question_text, question_type, question_order) VALUES (?, ?, ?, ?)");
            $qStmt->bind_param("issi", $newQuizId, $questionText, $questionType, $qIndex);
        }
        $qStmt->execute();
        $newQuestionId = $questionId ?: $qStmt->insert_id;
        
        // Handle answers for this question
        // In a real app, you'd delete old answers before inserting/updating new ones
    }

    header("Location: admin-quizzes.php");
    exit();
}

// --- PAGE CONTENT ---
include 'admin-header.php'; 

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$quizId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$selectedQuiz = null;

if ($quizId) {
    $stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $selectedQuiz = $stmt->get_result()->fetch_assoc();
}
?>
<style>
    .question-card { transition: all 0.3s ease-in-out; }
    .question-card:hover { border-color: #3b82f6; }
</style>

<div>
    <?php if ($action === 'list'): ?>
        <!-- Quiz List View -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Quiz Management</h1>
            <a href="?action=new" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Create New Quiz</a>
        </div>
        <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md overflow-hidden">
             <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Quiz Title</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Questions</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $quizzes_result = $conn->query("SELECT q.*, COUNT(qq.id) as questions FROM quizzes q LEFT JOIN quiz_questions qq ON q.id = qq.quiz_id GROUP BY q.id");
                    while($quiz = $quizzes_result->fetch_assoc()): ?>
                        <tr class="border-t">
                            <td class="p-4 font-medium"><?php echo htmlspecialchars($quiz['title']); ?></td>
                            <td class="p-4"><?php echo $quiz['questions']; ?></td>
                            <td class="p-4"><a href="?action=edit&id=<?php echo $quiz['id']; ?>" class="text-blue-500 hover:underline">Edit</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($action === 'edit' || $action === 'new'): ?>
        <!-- Add/Edit Quiz View -->
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6"><?php echo $action === 'edit' ? 'Edit Quiz' : 'Create New Quiz'; ?></h1>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="save_quiz" value="1">
            <?php if ($action === 'edit'): ?><input type="hidden" name="quiz_id" value="<?php echo $selectedQuiz['id']; ?>"><?php endif; ?>

            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <label for="quiz-title" class="block text-sm font-medium">Quiz Title</label>
                <input type="text" id="quiz-title" name="quiz-title" value="<?php echo htmlspecialchars($selectedQuiz['title'] ?? ''); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
            </div>

            <div id="questions-container" class="space-y-4"></div>
            
            <button type="button" id="add-question-btn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg">
                <i class="fa-solid fa-plus mr-2"></i>Add Question
            </button>
            
            <div class="flex justify-end space-x-4">
                <a href="admin-quizzes.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Save Quiz</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
// All the JavaScript for the dynamic quiz builder would go here
</script>

<?php include '../footer.php'; ?>
