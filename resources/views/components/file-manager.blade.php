
<!-- File Manager Modal -->
<div class="modal fade" id="fileManagerModal" tabindex="-1" aria-labelledby="fileManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileManagerModalLabel">{{ __('File Manager') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12" id="fm-main-block">
                    <div id="fm"></div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- @push('script-page') --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileManagerModal = document.getElementById('fileManagerModal');
        const openFileManagerBtn = document.getElementById('open-file-manager');

        // Open file manager modal on button click
        openFileManagerBtn.addEventListener('click', function () {
            fileManagerModal.classList.add('show'); // Show the modal
            fileManagerModal.style.display = 'block'; // Make it visible
            document.body.classList.add('modal-open'); // Add modal-open class to body
            fm.open(); // Open the file manager inside the modal
        });

        // Close modal when clicking the close button or backdrop
        const closeModalButtons = fileManagerModal.querySelectorAll('[data-bs-dismiss="modal"]');
        closeModalButtons.forEach(button => {
            button.addEventListener('click', function () {
                fileManagerModal.classList.remove('show'); // Hide the modal
                fileManagerModal.style.display = 'none'; // Make it invisible
                document.body.classList.remove('modal-open'); // Remove modal-open class from body
            });
        });

        // Set callback for file selection
        fm.$store.commit('fm/setFileCallBack', function (fileUrl) {
            // Set the selected file path in the input field
            document.getElementById('file_path').value = fileUrl;

            // Close the modal after selection
            fileManagerModal.classList.remove('show');
            fileManagerModal.style.display = 'none';
            document.body.classList.remove('modal-open');
        });
    });
</script>