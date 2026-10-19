<section class="botton-navigation-bar-container md:hidden">
    <nav class="bottom-navigation-bar">
        <a href="{{ route('index') }}" class="nav-item">
            <img src="../../custom/assets/icon/navigation/home.svg" alt="Home" class="active">
            <span>Home</span>
        </a>
        <a href="{{ route('explore') }}" class="nav-item">
            <img src="../../custom/assets/icon/navigation/category.svg" alt="Category">
            <span>Explore</span>
        </a>
        <a href="{{ route('stores') }}" class="nav-item">
            <img src="../../custom/assets/icon/navigation/store.svg" alt="Store">
            <span>Stores</span>
        </a>
        <a href="{{ route('login') }}" class="nav-item">
            <img src="../../custom/assets/icon/navigation/profile.svg" alt="Profile">
            <span>Profile</span>
        </a>
    </nav>
</section>
<style>
    .botton-navigation-bar-container {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #faf3ed;
        border-top: 1px solid #ddd;
        padding-top: 3px;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }
    .botton-navigation-bar-container .bottom-navigation-bar {
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 10px 16px;
    }
    .botton-navigation-bar-container .bottom-navigation-bar .nav-item {
        width: 63px;
        height: 52px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        color: #333;
        font-size: 12px;
    }
    .botton-navigation-bar-container .bottom-navigation-bar .nav-item img {
        display: block;
        width: 23px;
        height: 23px;
        margin-bottom: 4px;
        filter: grayscale(1);
    }
    .botton-navigation-bar-container .bottom-navigation-bar .nav-item .active {
        filter: grayscale(0);
        color: #6B1A00;
    }
    .botton-navigation-bar-container .bottom-navigation-bar .nav-item:hover {
        color: #6B1A00;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all nav items
        const navItems = document.querySelectorAll('.bottom-navigation-bar .nav-item');

        // Get current path
        const currentPath = window.location.pathname;

        // Remove active class from all items first
        navItems.forEach(item => {
            item.classList.remove('active');
            const img = item.querySelector('img');
            const span = item.querySelector('span');
            if (img) img.classList.remove('active');
            if (span) span.classList.remove('active');

            // Check if item href matches current path
            const itemPath = new URL(item.href).pathname;
            if (currentPath === itemPath) {
                item.classList.add('active');
                const img = item.querySelector('img');
                const span = item.querySelector('span');
                if (img) img.classList.add('active');
                if (span) span.classList.add('active');
            }
        });

        // Optional: Add click handler to update active state when clicked
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                navItems.forEach(i => {
                    i.classList.remove('active');
                    const img = i.querySelector('img');
                    if (img) img.classList.remove('active');
                });
                this.classList.add('active');
                const img = this.querySelector('img');
                if (img) img.classList.add('active');
            });
        });
    });
</script>
