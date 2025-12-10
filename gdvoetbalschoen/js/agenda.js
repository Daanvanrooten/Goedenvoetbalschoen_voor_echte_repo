// Toggle between week and month view
const toggleBtns = document.querySelectorAll('.toggle-btn');
const monthView = document.querySelector('.month-view');
const weekView = document.querySelector('.week-view');
const mobileTasksSection = document.querySelector('.mobile-tasks-section');
const monthNavigation = document.querySelector('.month-navigation');
const weekNavigationBtns = document.querySelectorAll('.week-navigation');

toggleBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        toggleBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const view = this.dataset.view;
        
        if (view === 'week') {
            monthView.style.display = 'none';
            weekView.style.display = 'block';
            weekView.classList.add('active');
            
            // Show week navigation buttons
            if (monthNavigation) {
                monthNavigation.style.display = 'none';
            }
            weekNavigationBtns.forEach(nav => {
                nav.style.display = 'flex';
            });
            
            // Hide mobile tasks section in week view
            if (mobileTasksSection && window.innerWidth <= 768) {
                mobileTasksSection.style.display = 'none';
            }
            
            // Initialize week view
            regenerateWeekView();
            updateWeekInfo(); // Initialize selected week display
        } else {
            monthView.style.display = 'block';
            weekView.style.display = 'none';
            weekView.classList.remove('active');
            
            // Show month navigation buttons
            if (monthNavigation) {
                monthNavigation.style.display = 'flex';
            }
            weekNavigationBtns.forEach(nav => {
                nav.style.display = 'none';
            });
            
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

// Month navigation functionality
let currentMonth = new Date().getMonth(); // 0-11
let currentYear = new Date().getFullYear();

const monthNames = ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 
                    'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'];

const prevMonthBtn = document.getElementById('prevMonth');
const nextMonthBtn = document.getElementById('nextMonth');
const calendarTitle = document.querySelector('.calendar-title');
const weekInfoLabel = document.querySelector('.current-week-info .info-label:last-of-type');

// Update calendar title with current month
function updateCalendarTitle() {
    const loggedInUser = {
        name: 'Pietje Bell'
    };
    
    const monthYear = `${monthNames[currentMonth]} ${currentYear}`;
    if (calendarTitle) {
        calendarTitle.textContent = `${loggedInUser.name}'s schema - ${monthYear}`;
    }
    
    // Update week info display
    if (weekInfoLabel) {
        weekInfoLabel.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    }
}

// Navigate to previous month
if (prevMonthBtn) {
    prevMonthBtn.addEventListener('click', function() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        updateCalendarTitle();
        regenerateCalendar();
    });
}

// Navigate to next month
if (nextMonthBtn) {
    nextMonthBtn.addEventListener('click', function() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        updateCalendarTitle();
        regenerateCalendar();
    });
}

// Regenerate calendar grid for new month
function regenerateCalendar() {
    const daysGrid = document.querySelector('.days-grid');
    const weekNumbers = document.querySelector('.week-numbers');
    if (!daysGrid) return;
    
    // Get first day of month and number of days
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();
    
    // Adjust for Monday start (0 = Sunday, make it 6)
    const adjustedFirstDay = firstDay === 0 ? 6 : firstDay - 1;
    
    // Clear existing days
    daysGrid.innerHTML = '';
    
    // Clear and rebuild week numbers
    if (weekNumbers) {
        weekNumbers.innerHTML = '<div class="week-header"></div>';
        
        // Calculate week numbers for this month
        const totalDays = adjustedFirstDay + daysInMonth;
        const weeksToShow = Math.ceil(totalDays / 7);
        
        for (let week = 0; week < weeksToShow; week++) {
            const weekDate = new Date(currentYear, currentMonth, 1 + (week * 7) - adjustedFirstDay);
            const weekNum = getWeekNumber(weekDate);
            const weekDiv = document.createElement('div');
            weekDiv.className = 'week-number';
            
            // Check if this is the current week
            const today = new Date();
            const currentWeekNum = getWeekNumber(today);
            if (weekNum === currentWeekNum) {
                weekDiv.classList.add('current-week');
            }
            
            weekDiv.textContent = weekNum;
            weekNumbers.appendChild(weekDiv);
        }
    }
    
    // Add previous month's days
    for (let i = adjustedFirstDay - 1; i >= 0; i--) {
        const dayCell = document.createElement('div');
        dayCell.className = 'day-cell prev-month';
        dayCell.textContent = daysInPrevMonth - i;
        daysGrid.appendChild(dayCell);
    }
    
    // Add current month's days
    for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('div');
        dayCell.className = 'day-cell current-month';
        
        // Check if it's today
        const today = new Date();
        if (day === today.getDate() && 
            currentMonth === today.getMonth() && 
            currentYear === today.getFullYear()) {
            dayCell.classList.add('today');
        }
        
        // Check if it's weekend (Saturday or Sunday)
        const dayOfWeek = new Date(currentYear, currentMonth, day).getDay();
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            dayCell.classList.add('weekend');
        }
        
        dayCell.textContent = day;
        daysGrid.appendChild(dayCell);
    }
    
    // Add next month's days to fill the grid
    const totalCells = adjustedFirstDay + daysInMonth;
    const remainingCells = (Math.ceil(totalCells / 7) * 7) - totalCells;
    
    for (let day = 1; day <= remainingCells; day++) {
        const dayCell = document.createElement('div');
        dayCell.className = 'day-cell prev-month';
        dayCell.textContent = day;
        daysGrid.appendChild(dayCell);
    }
    
    // Re-add click handlers to new day cells
    const newDayCells = daysGrid.querySelectorAll('.day-cell');
    newDayCells.forEach(cell => {
        cell.addEventListener('click', function() {
            if (!this.querySelector('.event')) {
                console.log('Empty day clicked - add new event');
            }
        });
    });
}

// Helper function to get ISO week number
function getWeekNumber(date) {
    const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
}

// Week navigation
let currentWeekDate = new Date();
const prevWeekBtn = document.getElementById('prevWeek');
const nextWeekBtn = document.getElementById('nextWeek');
const weekBadge = document.querySelector('.week-badge');
const selectedWeekNum = document.getElementById('selectedWeekNum');

// Navigate to previous week
if (prevWeekBtn) {
    prevWeekBtn.addEventListener('click', function() {
        currentWeekDate.setDate(currentWeekDate.getDate() - 7);
        updateWeekInfo();
        regenerateWeekView();
    });
}

// Navigate to next week
if (nextWeekBtn) {
    nextWeekBtn.addEventListener('click', function() {
        currentWeekDate.setDate(currentWeekDate.getDate() + 7);
        updateWeekInfo();
        regenerateWeekView();
    });
}

// Update week info display
function updateWeekInfo() {
    const selectedWeek = getWeekNumber(currentWeekDate);
    
    // Update selected week (the one you're navigating through)
    if (selectedWeekNum) {
        selectedWeekNum.textContent = selectedWeek;
    }
    
    // Keep the actual current week badge unchanged
    // weekBadge stays as the real current week
    
    // Update month and year in week info
    const monthYear = `${monthNames[currentWeekDate.getMonth()]} ${currentWeekDate.getFullYear()}`;
    if (weekInfoLabel) {
        weekInfoLabel.textContent = monthYear;
    }
}

// Regenerate week view for current week
function regenerateWeekView() {
    const weekGrid = document.querySelector('.week-grid');
    const weekDaysContainer = document.querySelector('.week-days-container');
    const mobileWeekView = document.querySelector('.mobile-week-view');
    
    if (!weekGrid) return;
    
    // Calculate start of week (Sunday)
    const weekStart = new Date(currentWeekDate);
    const day = weekStart.getDay();
    const diff = weekStart.getDate() - day;
    weekStart.setDate(diff);
    
    const daysOfWeek = ['SUN', 'MON', 'TUE', 'WED', 'THUR', 'FRI', 'SAT'];
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                        'July', 'August', 'September', 'October', 'November', 'December'];
    
    // Clear and rebuild desktop week headers
    weekGrid.innerHTML = '';
    
    const today = new Date();
    
    for (let i = 0; i < 6; i++) {
        const dayDate = new Date(weekStart);
        dayDate.setDate(weekStart.getDate() + i);
        
        const isToday = dayDate.toDateString() === today.toDateString();
        const dayNumber = dayDate.getDate();
        
        // Show month name for today's date, otherwise just the day number
        const displayDate = isToday ? `${monthNames[dayDate.getMonth()]} ${dayNumber}` : dayNumber;
        
        const dayHeader = document.createElement('div');
        dayHeader.className = 'week-day-header';
        if (isToday) dayHeader.classList.add('active-day');
        
        dayHeader.innerHTML = `
            <div class="day-label">${daysOfWeek[i]}</div>
            <div class="day-number">${displayDate}</div>
        `;
        
        weekGrid.appendChild(dayHeader);
    }
    
    // Clear desktop week days container
    if (weekDaysContainer) {
        weekDaysContainer.innerHTML = '';
        for (let i = 0; i < 6; i++) {
            const column = document.createElement('div');
            column.className = 'week-day-column';
            weekDaysContainer.appendChild(column);
        }
    }
    
    // Clear and rebuild mobile week view
    if (mobileWeekView) {
        mobileWeekView.innerHTML = '';
        
        for (let i = 0; i < 6; i++) {
            const dayDate = new Date(weekStart);
            dayDate.setDate(weekStart.getDate() + i);
            
            const dayNumber = dayDate.getDate();
            
            const mobileDay = document.createElement('div');
            mobileDay.className = 'mobile-week-day';
            mobileDay.innerHTML = `
                <div class="mobile-day-label">${daysOfWeek[i]}</div>
                <div class="mobile-day-content">
                    <div class="mobile-day-number">${dayNumber}</div>
                </div>
            `;
            
            mobileWeekView.appendChild(mobileDay);
        }
    }
}

// Initialize with current month
updateCalendarTitle();

console.log('Agenda pagina geladen!');
