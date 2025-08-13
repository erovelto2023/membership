<?php include 'admin-header.php'; ?>

<?php
// Mock data for niches - in a real app, this would come from a database
$niches = [
    [
        'id' => 1,
        'title' => 'Home Gardening',
        'description' => 'A niche focused on helping beginners start their own vegetable and flower gardens at home.',
        'keywords' => ['home gardening', 'vegetable garden', 'beginner gardening', 'container gardening'],
        'affiliate_products' => ['Miracle-Gro Potting Mix', 'Fiskars Garden Tools Set'],
        'competition_sites' => ['gardeners.com', 'thespruce.com/gardening'],
    ],
    [
        'id' => 2,
        'title' => 'Drone Photography',
        'description' => 'Teaching amateur photographers how to take stunning aerial shots and videos using drones.',
        'keywords' => ['drone photography', 'DJI drones', 'aerial photography tips', 'drone video'],
        'affiliate_products' => ['DJI Mavic 3 Pro', 'LowePro DroneGuard Backpack'],
        'competition_sites' => ['dronelife.com', 'fromwhereidrone.com'],
    ],
];

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$nicheId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$selectedNiche = null;

if ($nicheId) {
    foreach ($niches as $niche) {
        if ($niche['id'] === $nicheId) {
            $selectedNiche = $niche;
            break;
        }
    }
}
?>

<div>
    <?php if ($action === 'list'): ?>
        <!-- Niche List View -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Businesses & Niches</h1>
            <a href="?action=new" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition-colors flex items-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Add New Niche</span>
            </a>
        </div>
        <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/60">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Niche Title</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Description</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Keywords</th>
                        <th class="p-4 font-semibold text-gray-600 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($niches as $niche): ?>
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="p-4 font-medium text-gray-800 dark:text-white"><?php echo htmlspecialchars($niche['title']); ?></td>
                            <td class="p-4 text-gray-600 dark:text-gray-300 max-w-sm truncate"><?php echo htmlspecialchars($niche['description']); ?></td>
                            <td class="p-4 text-gray-600 dark:text-gray-300"><?php echo count($niche['keywords']); ?></td>
                            <td class="p-4">
                                <a href="?action=edit&id=<?php echo $niche['id']; ?>" class="text-blue-500 hover:underline">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($action === 'edit' || $action === 'new'): ?>
        <!-- Add/Edit Niche View -->
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">
            <?php echo $action === 'edit' ? 'Edit Niche' : 'Create New Niche'; ?>
        </h1>
        <form action="#" method="POST" class="space-y-6">
            <!-- Basic Info -->
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Basic Information</h2>
                <div>
                    <label for="niche-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Niche Title</label>
                    <input type="text" id="niche-title" name="niche-title" value="<?php echo htmlspecialchars($selectedNiche['title'] ?? ''); ?>" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg">
                </div>
                <div class="mt-4">
                    <label for="niche-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea id="niche-description" name="niche-description" rows="4" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg"><?php echo htmlspecialchars($selectedNiche['description'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Keywords -->
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Keywords</h2>
                <label for="keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Enter keywords, separated by commas</label>
                <textarea id="keywords" name="keywords" rows="3" class="mt-1 w-full p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg"><?php echo isset($selectedNiche['keywords']) ? htmlspecialchars(implode(', ', $selectedNiche['keywords'])) : ''; ?></textarea>
            </div>

            <!-- Dynamic Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Affiliate Products -->
                <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Affiliate Products</h2>
                    <div id="affiliate-products-container" class="space-y-2"></div>
                    <button type="button" id="add-product-btn" class="mt-4 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1 px-3 rounded-lg"><i class="fa-solid fa-plus mr-1"></i>Add Product</button>
                </div>
                <!-- Competition Sites -->
                <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Competition Sites</h2>
                    <div id="competition-sites-container" class="space-y-2"></div>
                    <button type="button" id="add-site-btn" class="mt-4 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1 px-3 rounded-lg"><i class="fa-solid fa-plus mr-1"></i>Add Site</button>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4">
                <a href="admin-niches.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Save Niche</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to add dynamic input fields
    function addDynamicField(containerId, buttonId, inputName, placeholder) {
        const container = document.getElementById(containerId);
        const addBtn = document.getElementById(buttonId);

        if (addBtn) {
            addBtn.addEventListener('click', () => {
                const inputGroup = document.createElement('div');
                inputGroup.className = 'flex items-center space-x-2';
                
                const input = document.createElement('input');
                input.type = 'text';
                input.name = `${inputName}[]`;
                input.className = 'flex-1 p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm';
                input.placeholder = placeholder;

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'text-gray-400 hover:text-red-500';
                removeBtn.innerHTML = '&times;';
                removeBtn.onclick = () => {
                    inputGroup.remove();
                };

                inputGroup.appendChild(input);
                inputGroup.appendChild(removeBtn);
                container.appendChild(inputGroup);
            });
        }
    }

    addDynamicField('affiliate-products-container', 'add-product-btn', 'affiliate_products', 'e.g., Product Name');
    addDynamicField('competition-sites-container', 'add-site-btn', 'competition_sites', 'e.g., example.com');

    // If editing, pre-populate the dynamic fields
    <?php if ($action === 'edit' && $selectedNiche): ?>
        const productsContainer = document.getElementById('affiliate-products-container');
        <?php foreach ($selectedNiche['affiliate_products'] as $product): ?>
            // Similar logic to addDynamicField but with a value
            (function() {
                const inputGroup = document.createElement('div');
                inputGroup.className = 'flex items-center space-x-2';
                inputGroup.innerHTML = `<input type="text" name="affiliate_products[]" class="flex-1 p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm" value="<?php echo htmlspecialchars($product); ?>"><button type="button" class="text-gray-400 hover:text-red-500 remove-btn">&times;</button>`;
                productsContainer.appendChild(inputGroup);
                inputGroup.querySelector('.remove-btn').onclick = () => inputGroup.remove();
            })();
        <?php endforeach; ?>

        const sitesContainer = document.getElementById('competition-sites-container');
        <?php foreach ($selectedNiche['competition_sites'] as $site): ?>
            (function() {
                const inputGroup = document.createElement('div');
                inputGroup.className = 'flex items-center space-x-2';
                inputGroup.innerHTML = `<input type="text" name="competition_sites[]" class="flex-1 p-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm" value="<?php echo htmlspecialchars($site); ?>"><button type="button" class="text-gray-400 hover:text-red-500 remove-btn">&times;</button>`;
                sitesContainer.appendChild(inputGroup);
                inputGroup.querySelector('.remove-btn').onclick = () => inputGroup.remove();
            })();
        <?php endforeach; ?>
    <?php endif; ?>
});
</script>

<?php include 'footer.php'; ?>
