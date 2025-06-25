document.addEventListener('DOMContentLoaded', function () {
    function loadTabContent(tab) {
        fetch(`${tab}.php`)
        .then(response => response.text())
            .then(html => {
                document.getElementById('profileContent').innerHTML = html;
            });
    }

    // Load default tab
    loadTabContent('user_details');

    // Tab switching
    document.querySelectorAll('#profileTabs .nav-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('#profileTabs .nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            const tab = this.getAttribute('data-tab');
            loadTabContent(tab);
        });
    });
});
