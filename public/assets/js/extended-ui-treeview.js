document.addEventListener('DOMContentLoaded', function () {


  const showTreeBtn = document.getElementById('show-tree');
  const showTableBtn = document.getElementById('show-table');
  const treeContainer = document.getElementById('iso-tree-container');
  const tableContainer = document.getElementById('table-container');

  // Function to update UI based on stored view preference
  function updateView() {
      // Get saved view from localStorage; default to 'tree'
      const viewPreference = localStorage.getItem('viewPreference') || 'tree';
      if (viewPreference === 'tree') {
          treeContainer.style.display = 'block';
          tableContainer.style.display = 'none';
          showTreeBtn.classList.add('btn-outline-primary');
          showTreeBtn.classList.remove('btn-outline-secondary');
          showTableBtn.classList.add('btn-outline-secondary');
          showTableBtn.classList.remove('btn-outline-primary');
      } else {
          treeContainer.style.display = 'none';
          tableContainer.style.display = 'block';
          showTableBtn.classList.add('btn-outline-primary');
          showTableBtn.classList.remove('btn-outline-secondary');
          showTreeBtn.classList.add('btn-outline-secondary');
          showTreeBtn.classList.remove('btn-outline-primary');
      }
  }

  // Save view preference to localStorage
  function saveViewPreference(view) {
      localStorage.setItem('viewPreference', view);
      updateView();
  }

  // Set initial view based on localStorage
  updateView();

  // Add event listeners to toggle buttons
  showTreeBtn.addEventListener('click', function (e) {
      e.preventDefault();
      saveViewPreference('tree');
  });

  showTableBtn.addEventListener('click', function (e) {
      e.preventDefault();
      saveViewPreference('table');
  });

 
});