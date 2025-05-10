/**
 * Category Management JavaScript
 * Handles AJAX operations for categories in the Document module
 */
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effect to category cards
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Add hover effect to add-category card
    const addCategoryCard = document.querySelector('.add-category-card');
    if (addCategoryCard) {
        addCategoryCard.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'all 0.3s ease';
            this.style.cursor = 'pointer';
        });
        
        addCategoryCard.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    }
    
    // Save new category
    const saveCategory = document.getElementById('saveCategory');
    if (saveCategory) {
        saveCategory.addEventListener('click', function() {
            const nameAr = document.getElementById('name_ar').value;
            const nameEn = document.getElementById('name_en').value;
            const errorDiv = document.getElementById('addCategoryError');
            
            if (!nameAr || !nameEn) {
                errorDiv.textContent = LANG_MESSAGES.fill_required_fields;
                errorDiv.classList.remove('d-none');
                return;
            }
            
            errorDiv.classList.add('d-none');
            
            // Show loading state
            const saveBtn = this;
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + LANG_MESSAGES.saving;
            saveBtn.disabled = true;
            
            fetch(ROUTES.category_store, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    name_ar: nameAr,
                    name_en: nameEn,
                    type: 'supporting'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset form
                    document.getElementById('addCategoryForm').reset();
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
                    modal.hide();
                    
                    // Refresh the categories list without page reload
                    refreshCategories();
                    
                    // Show success notification
                    toastrs('success', data.message, 'success');
                } else {
                    errorDiv.textContent = data.message;
                    errorDiv.classList.remove('d-none');
                    toastrs('error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.textContent = LANG_MESSAGES.error_occurred;
                errorDiv.classList.remove('d-none');
                toastrs('error', LANG_MESSAGES.error_occurred, 'error');
            })
            .finally(() => {
                // Reset button state
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            });
        });
    }
    
    // Open edit category modal
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-category')) {
            const link = e.target.closest('.edit-category');
            const categoryId = link.dataset.id;
            const titleAr = link.dataset.titleAr;
            const titleEn = link.dataset.titleEn;
            
            document.getElementById('edit_category_id').value = categoryId;
            document.getElementById('edit_name_ar').value = titleAr;
            document.getElementById('edit_name_en').value = titleEn;
            
            const editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            editModal.show();
        }
    });
    
    // Update category
    const updateCategory = document.getElementById('updateCategory');
    if (updateCategory) {
        updateCategory.addEventListener('click', function() {
            const categoryId = document.getElementById('edit_category_id').value;
            const nameAr = document.getElementById('edit_name_ar').value;
            const nameEn = document.getElementById('edit_name_en').value;
            const errorDiv = document.getElementById('editCategoryError');
            
            if (!nameAr || !nameEn) {
                errorDiv.textContent = LANG_MESSAGES.fill_required_fields;
                errorDiv.classList.remove('d-none');
                return;
            }
            
            errorDiv.classList.add('d-none');
            
            // Show loading state
            const updateBtn = this;
            const originalText = updateBtn.innerHTML;
            updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + LANG_MESSAGES.updating;
            updateBtn.disabled = true;
            
            fetch(`${ROUTES.category_update}/${categoryId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    name_ar: nameAr,
                    name_en: nameEn
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset form
                    document.getElementById('editCategoryForm').reset();
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
                    modal.hide();
                    
                    // Refresh the categories list without page reload
                    refreshCategories();
                    
                    // Show success notification
                    toastrs('success', data.message, 'success');
                } else {
                    errorDiv.textContent = data.message;
                    errorDiv.classList.remove('d-none');
                    toastrs('error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.textContent = LANG_MESSAGES.error_occurred;
                errorDiv.classList.remove('d-none');
                toastrs('error', LANG_MESSAGES.error_occurred, 'error');
            })
            .finally(() => {
                // Reset button state
                updateBtn.innerHTML = originalText;
                updateBtn.disabled = false;
            });
        });
    }
});

/**
 * Function to refresh categories without page reload
 */
function refreshCategories() {
    fetch(ROUTES.categories_index, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Update the categories container with the new HTML
        document.getElementById('categories-container').innerHTML = html;
        
        // Reapply hover effects to new elements
        const newCategoryCards = document.querySelectorAll('.category-card');
        newCategoryCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'all 0.3s ease';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        const newAddCategoryCard = document.querySelector('.add-category-card');
        if (newAddCategoryCard) {
            newAddCategoryCard.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'all 0.3s ease';
                this.style.cursor = 'pointer';
            });
            
            newAddCategoryCard.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        }
    })
    .catch(error => {
        console.error('Error refreshing categories:', error);
    });
}
