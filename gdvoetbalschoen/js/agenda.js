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
accountBtn.addEventListener('click', function() {
    alert('Account menu openen');
    // Hier kun je een dropdown menu tonen
});

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
if (profileCircle) {
    profileCircle.addEventListener('click', function(e) {
        e.preventDefault();
        alert('Profiel menu openen');
        // Hier kun je een dropdown menu tonen met account opties
    });
}

console.log('Agenda pagina geladen!');
