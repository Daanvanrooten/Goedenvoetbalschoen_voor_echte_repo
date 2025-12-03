// Toggle between week and month view
const toggleBtns = document.querySelectorAll('.toggle-btn');
const monthView = document.querySelector('.month-view');
const weekView = document.querySelector('.week-view');
const mobileTasksSection = document.querySelector('.mobile-tasks-section');

toggleBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        toggleBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const view = this.dataset.view;
        
        if (view === 'week') {
            monthView.style.display = 'none';
            weekView.style.display = 'block';
            weekView.classList.add('active');
            
            // Hide mobile tasks section in week view
            if (mobileTasksSection && window.innerWidth <= 768) {
                mobileTasksSection.style.display = 'none';
            }
        } else {
            monthView.style.display = 'block';
            weekView.style.display = 'none';
            weekView.classList.remove('active');
            
            // Show mobile tasks section in month view
            if (mobileTasksSection && window.innerWidth <= 768) {
                mobileTasksSection.style.display = 'block';
            }
        }
        
        console.log('Switched to ' + view + ' view');
    });
});

// Account button click
const accountBtn = document.querySelector('.account-btn');
const createTaskBtn = document.querySelector('.create-task-btn');
const taakModal = document.getElementById('taakModal');
const taakForm = document.getElementById('taakForm');
const fileUpload = document.querySelector('.file-upload');
const fotoInput = document.getElementById('fotoInput');

// Open modal functie
function openTaakModal() {
    taakModal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Close modal functie
function closeTaakModal() {
    taakModal.classList.remove('active');
    document.body.style.overflow = '';
}

// Open modal bij klik op "Taak aanmaken" knop
if (accountBtn) {
    accountBtn.addEventListener('click', function(e) {
        e.preventDefault();
        openTaakModal();
    });
}

// Open modal bij klik op mobiele "Taak aanmaken" knop
if (createTaskBtn) {
    createTaskBtn.addEventListener('click', function(e) {
        e.preventDefault();
        openTaakModal();
    });
}

// Sluit modal bij klik buiten de modal content
taakModal.addEventListener('click', function(e) {
    if (e.target === taakModal) {
        closeTaakModal();
    }
});

// File upload click handler
if (fileUpload && fotoInput) {
    fileUpload.addEventListener('click', function() {
        fotoInput.click();
    });

    fotoInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const fileName = this.files[0].name;
            const uploadIcon = fileUpload.querySelector('.upload-icon p');
            if (uploadIcon) {
                uploadIcon.textContent = fileName;
            }
        }
    });
}

// Form submit handler
if (taakForm) {
    taakForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verzamel form data
        const formData = new FormData(this);
        const taakData = {
            naam: formData.get('taaknaam'),
            categorie: formData.get('categorie'),
            datum: formData.get('datum'),
            tijd: formData.get('tijd'),
            herhaling: formData.get('herhaling'),
            maxLeden: formData.get('maxleden'),
            beschrijving: formData.get('beschrijving'),
            foto: formData.get('foto')
        };
        
        console.log('Taak aangemaakt:', taakData);
        
        // Toon bevestiging
        alert('Taak succesvol aangemaakt!');
        
        // Reset form en sluit modal
        this.reset();
        closeTaakModal();
        
        // Hier zou je de taak naar de server sturen met AJAX
        // fetch('/api/create-task', {
        //     method: 'POST',
        //     body: formData
        // });
    });
}

// Event click handler
const events = document.querySelectorAll('.event');
events.forEach(event => {
    event.addEventListener('click', function(e) {
        e.stopPropagation();
        const title = this.querySelector('h3').textContent;
        const author = this.querySelector('p').textContent;
        console.log('Event clicked:', title, 'by', author);
        // Hier kun je een modal openen met event details
    });
});

// Day cell click handler
const dayCells = document.querySelectorAll('.day-cell');
dayCells.forEach(cell => {
    cell.addEventListener('click', function() {
        if (!this.querySelector('.event')) {
            console.log('Empty day clicked - add new event');
            // Hier kun je een "add event" dialog tonen
        }
    });
});

// Simulate user login - change this based on actual login status
function updateUserProfile() {
    // Simuleer ingelogde gebruiker
    const loggedInUser = {
        name: 'Pietje Bell',
        initial: 'P' // Dit is de eerste letter van de voornaam
    };
    
    // Update profile circle met initial
    const profileCircle = document.querySelector('.profile-circle');
    if (profileCircle && loggedInUser.initial) {
        profileCircle.textContent = loggedInUser.initial;
    }
    
    // Update calendar title
    const calendarTitle = document.querySelector('.calendar-title');
    if (calendarTitle && loggedInUser.name) {
        calendarTitle.textContent = `${loggedInUser.name}'s schema/Mijn schema`;
    }
}

// Initialize profile on page load
updateUserProfile();

// Profile circle click
const profileCircle = document.querySelector('.profile-circle');
const logoutModal = document.getElementById('logoutModal');
const cancelLogoutBtn = document.querySelector('.cancel-logout-btn');
const confirmLogoutBtn = document.querySelector('.confirm-logout-btn');

if (profileCircle) {
    profileCircle.addEventListener('click', function(e) {
        e.preventDefault();
        // Open logout modal
        logoutModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
}

// Close logout modal functions
function closeLogoutModal() {
    logoutModal.classList.remove('active');
    document.body.style.overflow = '';
}

if (cancelLogoutBtn) {
    cancelLogoutBtn.addEventListener('click', closeLogoutModal);
}

// Close modal when clicking outside
if (logoutModal) {
    logoutModal.addEventListener('click', function(e) {
        if (e.target === logoutModal) {
            closeLogoutModal();
        }
    });
}

// Confirm logout
if (confirmLogoutBtn) {
    confirmLogoutBtn.addEventListener('click', function() {
        // Redirect to login page
        window.location.href = 'login.php';
    });
}

console.log('Agenda pagina geladen!');
