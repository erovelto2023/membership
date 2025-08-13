            </main>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebarTitles = document.querySelectorAll('.sidebar-title');
            const darkModeToggle = document.getElementById('dark-mode-toggle');
            const html = document.documentElement;

            // Sidebar Toggle
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('w-64');
                sidebar.classList.toggle('w-20');
                sidebarTitles.forEach(title => title.classList.toggle('hidden'));
                sidebarToggle.querySelector('i').classList.toggle('fa-chevron-left');
                sidebarToggle.querySelector('i').classList.toggle('fa-chevron-right');
            });

            // Dark Mode Toggle
            darkModeToggle.addEventListener('click', () => {
                html.classList.toggle('dark');
                // You might want to save this preference in localStorage
            });

            // Mobile Menu (basic implementation)
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            mobileMenuButton.addEventListener('click', () => {
                // This would require more complex logic to create an overlay menu
                // For now, it just shows that the button is clickable.
                alert('Mobile menu would open here!');
            });
        });
    </script>
</body>
</html>