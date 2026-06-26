        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('click', function(e) {
    var sidebar = document.querySelector('.admin-sidebar');
    var toggle = document.querySelector('.sidebar-toggle');
    if (window.innerWidth < 992 && sidebar && sidebar.classList.contains('show') && !sidebar.contains(e.target) && toggle && !toggle.contains(e.target)) {
        sidebar.classList.remove('show');
    }
});
</script>
</body>
</html>
