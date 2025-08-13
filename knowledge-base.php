<?php 
include 'header.php'; 
include 'db.php';

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : null;
$selectedArticleId = isset($_GET['article_id']) ? (int)$_GET['article_id'] : null;

$selectedArticle = null;
$relatedArticles = [];
$searchResults = [];
$categories = [];

if ($searchQuery) {
    // Perform a search
    $searchTerm = "%{$searchQuery}%";
    $stmt = $conn->prepare("SELECT id, title, content FROM kb_articles WHERE (title LIKE ? OR content LIKE ?) AND status = 'Published'");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $searchResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} elseif ($selectedArticleId) {
    // View a single article
    $stmt = $conn->prepare("SELECT * FROM kb_articles WHERE id = ? AND status = 'Published'");
    $stmt->bind_param("i", $selectedArticleId);
    $stmt->execute();
    $selectedArticle = $stmt->get_result()->fetch_assoc();

    // Fetch related articles based on shared tags
    if ($selectedArticle) {
        $rel_stmt = $conn->prepare("
            SELECT DISTINCT a.id, a.title 
            FROM kb_articles a
            JOIN kb_article_tags at1 ON a.id = at1.article_id
            JOIN kb_article_tags at2 ON at1.tag_id = at2.tag_id
            WHERE at2.article_id = ? AND a.id != ? AND a.status = 'Published'
            LIMIT 5
        ");
        $rel_stmt->bind_param("ii", $selectedArticleId, $selectedArticleId);
        $rel_stmt->execute();
        $relatedArticles = $rel_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
} else {
    // Default view: fetch categories
    $categories_result = $conn->query("SELECT * FROM kb_categories WHERE status = 'Visible' ORDER BY name ASC");
    $categories = $categories_result->fetch_all(MYSQLI_ASSOC);
}
?>

<div>
    <?php if ($searchQuery): ?>
        <!-- Search Results View -->
        <div class="max-w-4xl mx-auto">
            <a href="knowledge-base.php" class="text-blue-500 dark:text-blue-400 hover:underline mb-4 inline-block">&larr; Back to Knowledge Base</a>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h1>
            <div class="space-y-4">
                <?php if (empty($searchResults)): ?>
                    <p class="text-gray-500 dark:text-gray-400">No articles found matching your search term.</p>
                <?php else: ?>
                    <?php foreach($searchResults as $article): ?>
                        <a href="?article_id=<?php echo $article['id']; ?>" class="block bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
                            <h3 class="text-xl font-bold text-blue-500"><?php echo htmlspecialchars($article['title']); ?></h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                <?php 
                                    // Show a snippet of the content
                                    echo htmlspecialchars(substr(strip_tags($article['content']), 0, 150)) . '...'; 
                                ?>
                            </p>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif ($selectedArticle): ?>
        <!-- Single Article View -->
        <div class="max-w-4xl mx-auto">
            <a href="knowledge-base.php" class="text-blue-500 dark:text-blue-400 hover:underline mb-4 inline-block">&larr; Back to Knowledge Base</a>
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-8">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white"><?php echo htmlspecialchars($selectedArticle['title']); ?></h1>
                <div class="prose dark:prose-invert mt-6 max-w-none">
                    <?php echo $selectedArticle['content']; // This content is trusted from the admin editor ?>
                </div>
            </div>

            <?php if (!empty($relatedArticles)): ?>
            <div class="mt-8 bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Related Articles</h3>
                <ul class="space-y-2">
                    <?php foreach($relatedArticles as $article): ?>
                        <li>
                            <a href="?article_id=<?php echo $article['id']; ?>" class="text-blue-500 hover:underline flex items-center space-x-2">
                                <i class="fa-solid fa-file-alt text-gray-400"></i>
                                <span><?php echo htmlspecialchars($article['title']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Main Knowledge Base View -->
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Knowledge Base</h1>
        <!-- Search Form -->
        <form action="knowledge-base.php" method="GET" class="mb-8">
            <div class="relative">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Search for help articles..." class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg pl-12 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($categories as $category): ?>
                <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-3"><?php echo htmlspecialchars($category['name']); ?></h2>
                    <ul class="space-y-2">
                        <?php 
                        $article_stmt = $conn->prepare("SELECT id, title FROM kb_articles WHERE category_id = ? AND status = 'Published' ORDER BY title ASC LIMIT 5");
                        $article_stmt->bind_param("i", $category['id']);
                        $article_stmt->execute();
                        $articles = $article_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        foreach($articles as $article):
                        ?>
                            <li>
                                <a href="?article_id=<?php echo $article['id']; ?>" class="text-blue-500 hover:underline flex items-center space-x-2">
                                    <i class="fa-solid fa-file-alt text-gray-400"></i>
                                    <span><?php echo htmlspecialchars($article['title']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
