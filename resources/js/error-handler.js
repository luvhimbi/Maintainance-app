document.addEventListener('DOMContentLoaded', function() {
    // Handle retry buttons
    document.querySelectorAll('[data-retry]').forEach(button => {
        button.addEventListener('click', function() {
            const url = this.dataset.retry || window.location.href;
            showLoadingState(this);

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        throw new Error('Retry failed');
                    }
                })
                .catch(error => {
                    showErrorState(this, 'Failed to retry. Please try again later.');
                });
        });
    });

    function showLoadingState(button) {
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Loading...
        `;
        button.dataset.originalText = originalText;
    }

    function showErrorState(button, message) {
        button.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i> ${message}`;
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText;
        }, 3000);
    }
});
