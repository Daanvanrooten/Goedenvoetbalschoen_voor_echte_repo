document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const memberCheckboxes = document.querySelectorAll('input[name="member[]"]');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            memberCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    }

    // Update select all checkbox when individual checkboxes change
    memberCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(memberCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(memberCheckboxes).some(cb => cb.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        });
    });

    // Delete button functionality
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Weet je zeker dat je dit lid wilt verwijderen?')) {
                const row = this.closest('tr');
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            }
        });
    });

    // More options button functionality
    const moreButtons = document.querySelectorAll('.more-btn');
    const modal = document.getElementById('adminModal');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const confirmBtn = document.getElementById('confirmBtn');
    let currentMemberId = null;
    let currentMemberName = null;

    moreButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            currentMemberId = this.getAttribute('data-member-id');
            currentMemberName = this.getAttribute('data-member-name');
            
            // Update modal text with member name if needed
            const modalText = modal.querySelector('.modal-text');
            modalText.textContent = 'Wil je deze gebruiker admin maken?';
            
            // Show modal
            modal.classList.add('active');
        });
    });

    // Close modal handlers
    function closeModal() {
        modal.classList.remove('active');
        currentMemberId = null;
        currentMemberName = null;
    }

    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Confirm button - Make user admin
    confirmBtn.addEventListener('click', function() {
        if (currentMemberId) {
            // Simulate making user admin
            console.log(`Making user ${currentMemberId} (${currentMemberName}) an admin`);
            
            // Show success message
            alert(`${currentMemberName} is nu admin!`);
            
            // Close modal
            closeModal();
            
            // In a real application, you would make an AJAX call here to update the user's role
            // Example:
            // fetch('/api/make-admin', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify({ userId: currentMemberId })
            // });
        }
    });

    // Tab switching
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            // Add content switching logic here
        });
    });

    // Pagination
    const pageButtons = document.querySelectorAll('.page-num');
    pageButtons.forEach(button => {
        button.addEventListener('click', function() {
            pageButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            // Add page loading logic here
        });
    });

    // Search functionality
    const searchInputs = document.querySelectorAll('.search-box input');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.members-table tbody tr');
            
            rows.forEach(row => {
                const name = row.querySelector('.member-name').textContent.toLowerCase();
                const role = row.querySelector('.member-role').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || role.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // Previous/Next buttons
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            const currentActive = document.querySelector('.page-num.active');
            const prevPage = currentActive.previousElementSibling;
            
            if (prevPage && prevPage.classList.contains('page-num')) {
                currentActive.classList.remove('active');
                prevPage.classList.add('active');
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            const currentActive = document.querySelector('.page-num.active');
            const nextPage = currentActive.nextElementSibling;
            
            if (nextPage && nextPage.classList.contains('page-num')) {
                currentActive.classList.remove('active');
                nextPage.classList.add('active');
            }
        });
    }
});
