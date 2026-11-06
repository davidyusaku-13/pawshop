    </main>
  </div>
</div>

<script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/jquery.dataTables.min.js"></script>
<script>
  // Mobile sidebar toggle
  document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('adminSidebar');
    var toggleBtn = document.getElementById('sidebarToggleBtn');
    var closeBtn = document.getElementById('sidebarCloseBtn');

    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('show');
      });
    }

    if (closeBtn && sidebar) {
      closeBtn.addEventListener('click', function() {
        sidebar.classList.remove('show');
      });
    }
  });
</script>
</body>
</html>
