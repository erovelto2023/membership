<?php 
// --- FORM SUBMISSION LOGIC ---
include '../db.php'; 
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../adminlogin.php");
    exit;
}

// Handle Category Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_category'])) {
        $name = $_POST['category-name'];
        $description = $_POST['category-description'];
        $status = $_POST['category-status'];
        $id = $_POST['category_id'] ?? null;

        if ($id) { // Update
            $stmt = $conn->prepare("UPDATE forum_categories SET name = ?, description = ?, status = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $description, $status, $id);
        } else { // Insert
            $stmt = $conn->prepare("INSERT INTO forum_categories (name, description, status) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $description, $status);
        }
        $stmt->execute();
        header("Location: admin-forum.php");
        exit();
    }
}

// --- PAGE CONTENT ---
include 'admin-header.php'; 

$action = isset($_GET['action']) ? $_GET['action'] : 'list_categories';
$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$selectedCategory = null;

if ($categoryId) {
    $stmt = $conn->prepare("SELECT * FROM forum_categories WHERE id = ?");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $selectedCategory = $stmt->get_result()->fetch_assoc();
}
?>

<div>
    <?php if ($action === 'list_categories'): ?>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Forum Management</h1>
            <a href="?action=new_category" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">New Category</a>
        </div>
        <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md overflow-hidden">
            <table class="w-full text-left">
                <!-- Table headers -->
                <tbody>
                    <?php 
                    $categories_result = $conn->query("SELECT fc.*, COUNT(ft.id) as thread_count FROM forum_categories fc LEFT JOIN forum_threads ft ON fc.id = ft.category_id GROUP BY fc.id");
                    while($category = $categories_result->fetch_assoc()): ?>
                        <tr class="border-t">
                            <td class="p-4 font-medium"><?php echo htmlspecialchars($category['name']); ?></td>
                            <td class="p-4"><?php echo $category['thread_count']; ?></td>
                            <td class="p-4"><span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $category['status'] === 'Visible' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800'; ?>"><?php echo $category['status']; ?></span></td>
                            <td class="p-4 space-x-3">
                                <a href="?action=view_category&category_id=<?php echo $category['id']; ?>" class="text-blue-500" title="View Threads"><i class="fa fa-eye"></i></a>
                                <a href="?action=edit_category&category_id=<?php echo $category['id']; ?>" class="text-green-500" title="Edit"><i class="fa fa-pencil"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($action === 'view_category' && $selectedCategory): ?>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Threads in "<?php echo htmlspecialchars($selectedCategory['name']); ?>"</h1>
        <!-- Thread list for a category would go here -->

    <?php elseif ($action === 'new_category' || ($action === 'edit_category' && $selectedCategory)): ?>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6"><?php echo $action === 'edit_category' ? 'Edit Category' : 'Create New Category'; ?></h1>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="save_category" value="1">
            <?php if ($action === 'edit_category'): ?>
                <input type="hidden" name="category_id" value="<?php echo $selectedCategory['id']; ?>">
            <?php endif; ?>
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <div>
                    <label for="category-name" class="block text-sm font-medium">Category Name</label>
                    <input type="text" id="category-name" name="category-name" value="<?php echo htmlspecialchars($selectedCategory['name'] ?? ''); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                </div>
                <div class="mt-4">
                    <label for="category-description" class="block text-sm font-medium">Description</label>
                    <textarea id="category-description" name="category-description" rows="3" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg"><?php echo htmlspecialchars($selectedCategory['description'] ?? ''); ?></textarea>
                </div>
                <div class="mt-4">
                    <label for="category-status" class="block text-sm font-medium">Status</label>
                    <select id="category-status" name="category-status" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                        <option value="Visible" <?php echo ($selectedCategory['status'] ?? '') === 'Visible' ? 'selected' : ''; ?>>Visible</option>
                        <option value="Hidden" <?php echo ($selectedCategory['status'] ?? '') === 'Hidden' ? 'selected' : ''; ?>>Hidden</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-4">
                <a href="admin-forum.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Save Category</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>
