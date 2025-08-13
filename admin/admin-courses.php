<?php 
// Include DB and header
include '../db.php'; 
include 'admin-header.php'; 

// Fetch quizzes for the dropdown
$quizzes_result = $conn->query("SELECT id, title FROM quizzes ORDER BY title ASC");
$allQuizzes = $quizzes_result->fetch_all(MYSQLI_ASSOC);
?>

<!-- Add/Edit Course Form -->
<form action="admin-courses.php" method="POST" class="space-y-6">
    <!-- ... other form elements ... -->
    <div id="modules-container" class="space-y-4">
        <!-- JS will populate this area -->
    </div>
    <button type="button" id="add-module-btn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg">
        <i class="fa-solid fa-plus mr-2"></i>Add Module
    </button>
    <!-- ... save/cancel buttons ... -->
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ... existing JS for modules and lessons ...

    function addContentBlock(container, modIndex, lessonIndex, type) {
        let contentHTML = '';
        const fieldName = `modules[${modIndex}][lessons][${lessonIndex}][content][]`;
        
        if (type === 'quiz') {
            let quizOptions = '<option value="">Select a Quiz...</option>';
            <?php foreach($allQuizzes as $quiz) { ?>
                quizOptions += `<option value="<?php echo $quiz['id']; ?>"><?php echo htmlspecialchars($quiz['title']); ?></option>`;
            <?php } ?>
            contentHTML = `<label class="block text-xs font-medium">Attach Quiz</label><input type="hidden" name="${fieldName}[type]" value="quiz"><select name="${fieldName}[value]" class="mt-1 w-full p-2 bg-gray-100 border rounded-lg text-sm">${quizOptions}</select>`;
        } else {
            // ... handle other content block types ...
        }

        const blockElement = document.createElement('div');
        blockElement.className = 'relative p-3 border-l-4 border-gray-300';
        blockElement.innerHTML = `${contentHTML}<button type="button" class="absolute top-0 right-0 text-gray-400 hover:text-red-500 remove-block-btn">&times;</button>`;
        container.appendChild(blockElement);
        blockElement.querySelector('.remove-block-btn').addEventListener('click', () => blockElement.remove());
    }
    
    // ... rest of the JS ...
});
</script>

<?php include '../footer.php'; ?>
