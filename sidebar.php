<!-- Dark Overlay background when sidebar is open -->
<div id="overlay" class="overlay-bg"></div>

<!-- Floating Toggle Button -->
<button class="toggle-btn" id="sidebarToggle">☰ MENU</button>

<div class="sidebar" id="sidebar">
    <div style="text-align: right;">
        <button id="closeBtn" style="background:none; border:none; color:white; font-size:24px; cursor:pointer;">&times;</button>
    </div>
    
    <h3>Library Admin</h3>
    
    <a href="index.php">🏠 Dashboard</a>
    <a href="admin_books.php">📚 Books Database</a>
    
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('closeBtn');
    const dropBtn = document.getElementById('dropBtn');
    const dropContent = document.getElementById('dropContent');

    // Open Sidebar
    toggleBtn.onclick = () => {
        sidebar.classList.add('open');
        overlay.style.display = 'block';
    }

    // Close Sidebar
    const closeSidebar = () => {
        sidebar.classList.remove('open');
        overlay.style.display = 'none';
    }
    
    closeBtn.onclick = closeSidebar;
    overlay.onclick = closeSidebar;

    // Toggle Dropdown (only if elements exist)
    if(dropBtn && dropContent) {
        dropBtn.onclick = () => {
            const isVisible = dropContent.style.display === "block";
            dropContent.style.display = isVisible ? "none" : "block";
        }
    }
</script>