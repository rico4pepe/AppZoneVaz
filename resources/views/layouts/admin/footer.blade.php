<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom JS -->
  <script>
    // Toggle sidebar on mobile
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('show');
      document.body.classList.toggle('sidebar-open');
    });
    
    // Add option button functionality
    document.querySelector('.btn-outline-secondary').addEventListener('click', function() {
      const optionsContainer = this.parentElement;
      const newOption = document.createElement('div');
      newOption.className = 'option-item d-flex mb-2';
      newOption.innerHTML = `
        <input type="text" class="form-control me-2" placeholder="New Option">
        <button type="button" class="btn btn-outline-danger btn-sm">
          <i class="fas fa-trash"></i>
        </button>
      `;
      
      // Insert before the "Add Option" button
      optionsContainer.insertBefore(newOption, this);
      
      // Add event listener to the new trash button
      newOption.querySelector('.btn-outline-danger').addEventListener('click', function() {
        this.parentElement.remove();
      });
    });
    
    // Initialize existing delete buttons
    document.querySelectorAll('.option-item .btn-outline-danger').forEach(button => {
      button.addEventListener('click', function() {
        this.parentElement.remove();
      });
    });
  </script>
</body>
</html>
