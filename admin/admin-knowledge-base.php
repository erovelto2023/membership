<?php 
// --- FORM SUBMISSION LOGIC ---
include '../db.php'; 
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../adminlogin.php");
    exit;
}

// Handle Category and Article Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_category'])) {
        $name = $_POST['category-name'];
        $description = $_POST['category-description'];
        $status = $_POST['category-status'];
        $id = $_POST['category_id'] ?? null;

        if ($id) { // Update
            $stmt = $conn->prepare("UPDATE kb_categories SET name = ?, description = ?, status = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $description, $status, $id);
        } else { // Insert
            $stmt = $conn->prepare("INSERT INTO kb_categories (name, description, status) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $description, $status);
        }
        $stmt->execute();
        header("Location: admin-knowledge-base.php");
        exit();
    }
    if (isset($_POST['save_article'])) {
        $title = $_POST['article-title'];
        $content = $_POST['article-content'];
        $categoryId = $_POST['article-category'];
        $status = $_POST['article-status'];
        $tags_string = $_POST['tags'] ?? '';
        $id = $_POST['article_id'] ?? null;

        if ($id) { // Update
            $stmt = $conn->prepare("UPDATE kb_articles SET title = ?, content = ?, category_id = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssisi", $title, $content, $categoryId, $status, $id);
            $articleId = $id;
        } else { // Insert
            $stmt = $conn->prepare("INSERT INTO kb_articles (title, content, category_id, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $title, $content, $categoryId, $status);
            $stmt->execute();
            $articleId = $stmt->insert_id;
        }
        $stmt->execute();

        // Handle tags
        $conn->query("DELETE FROM kb_article_tags WHERE article_id = $articleId");
        if (!empty($tags_string)) {
            $tags_array = array_map('trim', explode(',', $tags_string));
            foreach ($tags_array as $tag_name) {
                if (empty($tag_name)) continue;
                // Check if tag exists, if not create it
                $tag_stmt = $conn->prepare("SELECT id FROM kb_tags WHERE name = ?");
                $tag_stmt->bind_param("s", $tag_name);
                $tag_stmt->execute();
                $tag_result = $tag_stmt->get_result();
                if ($tag_result->num_rows > 0) {
                    $tag_id = $tag_result->fetch_assoc()['id'];
                } else {
                    $insert_tag_stmt = $conn->prepare("INSERT INTO kb_tags (name) VALUES (?)");
                    $insert_tag_stmt->bind_param("s", $tag_name);
                    $insert_tag_stmt->execute();
                    $tag_id = $insert_tag_stmt->insert_id;
                }
                // Link article and tag
                $conn->query("INSERT INTO kb_article_tags (article_id, tag_id) VALUES ($articleId, $tag_id)");
            }
        }

        header("Location: admin-knowledge-base.php?action=view_category&category_id=" . $categoryId);
        exit();
    }
}

// --- PAGE CONTENT ---
include 'admin-header.php'; 

$action = isset($_GET['action']) ? $_GET['action'] : 'list_categories';
$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$articleId = isset($_GET['article_id']) ? (int)$_GET['article_id'] : null;

$selectedCategory = null;
$selectedArticle = null;
$relatedArticleIds = [];

if ($categoryId) {
    $stmt = $conn->prepare("SELECT * FROM kb_categories WHERE id = ?");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $selectedCategory = $stmt->get_result()->fetch_assoc();
}
if ($articleId) {
    $stmt = $conn->prepare("SELECT * FROM kb_articles WHERE id = ?");
    $stmt->bind_param("i", $articleId);
    $stmt->execute();
    $selectedArticle = $stmt->get_result()->fetch_assoc();

    // Get current tags as a string
    $tag_result = $conn->query("SELECT t.name FROM kb_tags t JOIN kb_article_tags at ON t.id = at.tag_id WHERE at.article_id = $articleId");
    $tags_array = [];
    while($row = $tag_result->fetch_assoc()) {
        $tags_array[] = $row['name'];
    }
    $currentTags = implode(', ', $tags_array);
}
?>
<style>
    .rich-text-editor { border: 1px solid #d1d5db; border-radius: 0 0 0.5rem 0.5rem; min-height: 250px; }
    .rich-text-editor:focus { outline: none; box-shadow: 0 0 0 2px #3b82f6; }
    .toolbar button { padding: 0.25rem 0.5rem; border-radius: 0.25rem; }
    .toolbar button:hover { background-color: #e5e7eb; }
    .dark .toolbar button:hover { background-color: #4b5563; }
</style>

<div>
    <?php if ($action === 'list_categories'): ?>
        <!-- Category List View -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Knowledge Base Management</h1>
            <a href="?action=new_category" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">New Category</a>
        </div>
        <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Category Name</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Articles</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $categories_result = $conn->query("SELECT c.*, COUNT(a.id) as articles FROM kb_categories c LEFT JOIN kb_articles a ON c.id = a.category_id GROUP BY c.id");
                    while($category = $categories_result->fetch_assoc()): ?>
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="p-4 font-medium text-gray-800 dark:text-white"><?php echo htmlspecialchars($category['name']); ?></td>
                            <td class="p-4 text-gray-600 dark:text-gray-300"><?php echo $category['articles']; ?></td>
                            <td class="p-4"><span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $category['status'] === 'Visible' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800'; ?>"><?php echo $category['status']; ?></span></td>
                            <td class="p-4 space-x-3">
                                <a href="?action=view_category&category_id=<?php echo $category['id']; ?>" class="text-blue-500 hover:underline" title="View Articles"><i class="fa fa-eye"></i></a>
                                <a href="?action=edit_category&category_id=<?php echo $category['id']; ?>" class="text-green-500 hover:underline" title="Edit"><i class="fa fa-pencil"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($action === 'view_category' && $selectedCategory): ?>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Articles in "<?php echo htmlspecialchars($selectedCategory['name']); ?>"</h1>
            <a href="?action=new_article&category_id=<?php echo $selectedCategory['id']; ?>" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">New Article</a>
        </div>
        <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md overflow-hidden">
             <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Article Title</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $article_stmt = $conn->prepare("SELECT id, title, status FROM kb_articles WHERE category_id = ?");
                    $article_stmt->bind_param("i", $selectedCategory['id']);
                    $article_stmt->execute();
                    $articles_result = $article_stmt->get_result();
                    while($article = $articles_result->fetch_assoc()): ?>
                        <tr class="border-t">
                            <td class="p-4 font-medium"><?php echo htmlspecialchars($article['title']); ?></td>
                            <td class="p-4"><span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $article['status'] === 'Published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>"><?php echo $article['status']; ?></span></td>
                            <td class="p-4"><a href="?action=edit_article&article_id=<?php echo $article['id']; ?>" class="text-blue-500 hover:underline">Edit</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($action === 'new_category' || ($action === 'edit_category' && $selectedCategory)): ?>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6"><?php echo $action === 'edit_category' ? 'Edit Category' : 'Create New Category'; ?></h1>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="save_category" value="1">
            <?php if ($action === 'edit_category'): ?>
                <input type="hidden" name="category_id" value="<?php echo $selectedCategory['id']; ?>">
            <?php endif; ?>
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <div class="mt-4">
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
                <a href="admin-knowledge-base.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Save Category</button>
            </div>
        </form>

    <?php elseif ($action === 'edit_article' || $action === 'new_article'): ?>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6"><?php echo $action === 'edit_article' ? 'Edit Article' : 'Create New Article'; ?></h1>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="save_article" value="1">
            <?php if ($action === 'edit_article'): ?>
                <input type="hidden" name="article_id" value="<?php echo $selectedArticle['id']; ?>">
            <?php endif; ?>
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <label for="article-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Article Title</label>
                        <input type="text" id="article-title" name="article-title" value="<?php echo htmlspecialchars($selectedArticle['title'] ?? ''); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                    </div>
                    <div>
                        <label for="article-category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                        <select id="article-category" name="article-category" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                            <?php 
                            $all_categories = $conn->query("SELECT id, name FROM kb_categories")->fetch_all(MYSQLI_ASSOC);
                            foreach($all_categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($selectedArticle['category_id'] ?? $categoryId) == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Article Content</label>
                    <div class="mt-1">
                        <div class="toolbar space-x-2 mb-1 p-1 bg-gray-100 dark:bg-gray-700 rounded-t-lg border-b border-gray-300 dark:border-gray-600">
                            <button type="button" data-command="bold"><i class="fa-solid fa-bold"></i></button>
                            <button type="button" data-command="italic"><i class="fa-solid fa-italic"></i></button>
                            <button type="button" data-command="underline"><i class="fa-solid fa-underline"></i></button>
                            <button type="button" data-command="insertUnorderedList"><i class="fa-solid fa-list-ul"></i></button>
                        </div>
                        <div id="editor" contenteditable="true" class="rich-text-editor p-2 bg-gray-100 dark:bg-gray-700"><?php echo $selectedArticle['content'] ?? ''; ?></div>
                        <textarea name="article-content" class="hidden"></textarea>
                    </div>
                </div>
                 <div class="mt-4">
                    <label for="article-status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="article-status" name="article-status" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg">
                        <option <?php echo ($selectedArticle['status'] ?? '') === 'Published' ? 'selected' : ''; ?>>Published</option>
                        <option <?php echo ($selectedArticle['status'] ?? '') === 'Draft' ? 'selected' : ''; ?>>Draft</option>
                    </select>
                </div>
                <div class="mt-4">
                    <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Related Terms / Tags</label>
                    <input type="text" id="tags" name="tags" value="<?php echo htmlspecialchars($currentTags); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border rounded-lg" placeholder="e.g., SEO, Billing, Funnels">
                    <p class="text-xs text-gray-500 mt-1">Separate tags with a comma.</p>
                </div>
            </div>
            <div class="flex justify-end space-x-4">
                <a href="admin-knowledge-base.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Save Article</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('editor');
    if (editor) {
        const hiddenTextarea = editor.nextElementSibling;
        hiddenTextarea.value = editor.innerHTML; // Initial sync
        const toolbar = editor.previousElementSibling;

        toolbar.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                document.execCommand(e.currentTarget.dataset.command, false, null);
                editor.focus();
            });
        });

        editor.addEventListener('input', () => {
            hiddenTextarea.value = editor.innerHTML;
        });
    }
});
</script>

<?php include '../footer.php'; ?>
