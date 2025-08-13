<?php 
include 'header.php'; 
include 'db.php';

// Fetch all non-admin users from the database
$users_result = $conn->query("SELECT id, username, avatar_url, 'Marketing Niche' as niche FROM users WHERE is_admin = 0 ORDER BY username ASC");
$allUsers = $users_result->fetch_all(MYSQLI_ASSOC);
?>

<div>
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Members Directory</h1>
    <p class="text-gray-500 dark:text-gray-400 mb-6">Find and connect with other members of the community.</p>

    <div class="relative mb-6">
        <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" id="member-search" placeholder="Search for members by name or niche..." class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg pl-12 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div id="members-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php foreach ($allUsers as $user): ?>
            <a href="member-profile.php?id=<?php echo $user['id']; ?>" class="member-card bg-white dark:bg-gray-800/50 rounded-xl shadow-md p-6 text-center hover:-translate-y-1 hover:shadow-lg transition-all duration-300" data-name="<?php echo htmlspecialchars($user['username']); ?>" data-niche="<?php echo htmlspecialchars($user['niche']); ?>">
                <img src="<?php echo htmlspecialchars($user['avatar_url'] ?? 'https://placehold.co/100x100/E2E8F0/4A5568?text=U'); ?>" alt="<?php echo htmlspecialchars($user['username']); ?>" class="w-20 h-20 rounded-full mx-auto mb-4 object-cover">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white"><?php echo htmlspecialchars($user['username']); ?></h3>
                <p class="text-sm text-blue-500"><?php echo htmlspecialchars($user['niche']); ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('member-search');
    const memberCards = document.querySelectorAll('.member-card');

    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        memberCards.forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const niche = card.dataset.niche.toLowerCase();
            if (name.includes(filter) || niche.includes(filter)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>
