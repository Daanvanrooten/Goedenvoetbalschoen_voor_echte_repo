// Automatische path detectie - werkt op elke PC
const currentPath = window.location.pathname;
const viewsIndex = currentPath.lastIndexOf("/views/");
const baseUrl = viewsIndex !== -1 ? currentPath.substring(0, viewsIndex) : "";

// Kleur mapping voor week-events
function getEventClass(colorHex) {
  switch (colorHex) {
    case "#cccccc":
      return "green-bg";
    default:
      return "green-bg";
  }
}

// Render week events in een kolom
function renderWeekEvents(dayColumn, events) {
  events.forEach((ev) => {
    const el = document.createElement("div");
    el.classList.add("week-event", getEventClass(ev.color));
    el.innerHTML = `
            <div class="event-time">${ev.start} - ${ev.end}</div>
            <div class="event-title">${ev.title}</div>
        `;
    dayColumn.appendChild(el);
  });
}

// Vul weekview met events
function populateWeekView(tasksByDate) {
  const dayColumns = document.querySelectorAll(".week-day-column");
  dayColumns.forEach((col) => {
    const date = col.dataset.date;
    col.innerHTML = "";
    if (tasksByDate[date]) {
      renderWeekEvents(col, tasksByDate[date]);
      col.classList.add("active-column");
    }
  });
}

// Zet de data-date attribuut op elke kolom
function setWeekDates(startDate) {
  const cols = document.querySelectorAll(".week-day-column");
  cols.forEach((col, i) => {
    const d = new Date(startDate);
    d.setDate(d.getDate() + i);
    const iso = d.toISOString().split("T")[0];
    col.dataset.date = iso;
  });
}

// Enable clicking on week day columns to show tasks modal
function enableWeekDayClick(tasksByDate) {
  document.querySelectorAll(".week-day-column").forEach((col) => {
    col.onclick = () => {
      const date = col.dataset.date;
      const events = tasksByDate[date] || [];
      showTasksModal(date, events);
    };
  });
}
// Modal voor taken tonen
function showTasksModal(date, events) {
  if (!events || events.length === 0) {
    alert("Geen taken op deze dag");
    return;
  }
  let modal = document.getElementById("tasksModal");
  if (!modal) {
    modal = document.createElement("div");
    modal.id = "tasksModal";
    modal.style.position = "fixed";
    modal.style.top = "0";
    modal.style.left = "0";
    modal.style.width = "100vw";
    modal.style.height = "100vh";
    modal.style.background = "rgba(0,0,0,0.3)";
    modal.style.display = "flex";
    modal.style.alignItems = "center";
    modal.style.justifyContent = "center";
    modal.style.zIndex = "9999";
    document.body.appendChild(modal);
  }
  modal.innerHTML = `<div class="task-modal-content" style="background:#fff;border-radius:16px;max-width:400px;width:90vw;padding:0;box-shadow:0 2px 16px rgba(0,0,0,0.15);overflow:hidden;">
        <div style='display:flex;align-items:center;gap:12px;padding:16px 20px 0 20px;'>
            <div style='background:#e5dbfa;color:#6b5b95;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-weight:600;'>A</div>
            <span style='font-weight:600;font-size:18px;'>Taak</span>
            <button id='closeTasksModal' style='margin-left:auto;background:none;border:none;font-size:22px;cursor:pointer;'>&times;</button>
        </div>
        <div style='background:#f4f0fa;display:flex;align-items:center;justify-content:center;height:100px;margin:20px 0;'>
            <svg width='60' height='60' fill='#d1c4e9' viewBox='0 0 24 24'><circle cx='12' cy='12' r='10'/></svg>
        </div>
        <div style='padding:0 20px 20px 20px;'>
            ${events
              .map(
                (ev) => `
                <div style='margin-bottom:18px;'>
                    <div style='font-weight:600;font-size:17px;'>${ev.title}</div>
                    <div style='color:#888;font-size:14px;'>${ev.start} - ${ev.end}</div>
                    <div style='margin:8px 0 0 0;font-size:15px;'>Leden moeten deze taak uitvoeren.</div>
                </div>
            `,
              )
              .join("")}
        </div>
    </div>`;
  modal.onclick = function (e) {
    if (e.target === modal) modal.style.display = "none";
  };
  document.getElementById("closeTasksModal").onclick = function () {
    modal.style.display = "none";
  };
  modal.style.display = "flex";
}
// Kleur naar CSS class
function getEventClass(colorHex) {
  switch (colorHex) {
    case "#cccccc":
      return "green-event";
    default:
      return "green-event";
  }
}

// Render events in een dagcel
function renderEventsForDay(dayCell, events) {
  // Toon alleen een blokje, klikbaar voor alle taken
  if (events.length > 0) {
    const el = document.createElement("div");
    el.classList.add("event", getEventClass(events[0].color));
    el.style.cursor = "pointer";
    el.innerHTML = `
            <h3 style='margin:0;font-size:15px;'>${events.length === 1 ? events[0].title : events.length + " taken"}</h3>
            <p style='margin:0;font-size:13px;color:#888;'>Klik voor details</p>
        `;
    el.onclick = (e) => {
      e.stopPropagation();
      showTasksModal("", events);
    };
    dayCell.appendChild(el);
  }
}
// Toggle between week and month view
const toggleBtns = document.querySelectorAll(".toggle-btn");
const monthView = document.querySelector(".month-view");
const weekView = document.querySelector(".week-view");
const mobileTasksSection = document.querySelector(".mobile-tasks-section");
const monthNavigation = document.querySelector(".month-navigation");
const weekNavigationBtns = document.querySelectorAll(".week-navigation");

toggleBtns.forEach((btn) => {
  btn.addEventListener("click", function () {
    toggleBtns.forEach((b) => b.classList.remove("active"));
    this.classList.add("active");

    const view = this.dataset.view;

    if (view === "week") {
      monthView.style.display = "none";
      weekView.style.display = "block";
      weekView.classList.add("active");
      // Show week navigation
      if (monthNavigation) {
        monthNavigation.style.display = "none";
      }
      weekNavigationBtns.forEach((nav) => {
        nav.style.display = "flex";
      });
      // Toon weeknavigatie altijd in weekview
      const weekNav = document.getElementById("weekNavigation");
      if (weekNav) weekNav.style.display = "flex";
      // Hide mobile tasks section in week view
      if (mobileTasksSection && window.innerWidth <= 768) {
        mobileTasksSection.style.display = "none";
      }
      // Initialize week view
      regenerateWeekView();
      updateWeekInfo();
    } else {
      monthView.style.display = "block";
      weekView.style.display = "none";
      weekView.classList.remove("active");
      // Show month navigation buttons
      if (monthNavigation) {
        monthNavigation.style.display = "flex";
      }
      weekNavigationBtns.forEach((nav) => {
        nav.style.display = "none";
      });
      // Verberg weeknavigatie in maandview
      const weekNav = document.getElementById("weekNavigation");
      if (weekNav) weekNav.style.display = "none";
      // Show mobile tasks section in month view
      if (mobileTasksSection && window.innerWidth <= 768) {
        mobileTasksSection.style.display = "block";
      }
    }

    console.log("Switched to " + view + " view");
  });
});

// Account button click
const accountBtn = document.querySelector(".account-btn");
const createTaskBtn = document.querySelector(".create-task-btn");
const taakModal = document.getElementById("taakModal");
const taakForm = document.getElementById("taakForm");
const fileUpload = document.querySelector(".file-upload");
const fotoInput = document.getElementById("fotoInput");

// Open modal functie
function openTaakModal() {
  taakModal.classList.add("active");
  document.body.style.overflow = "hidden";
}

// Close modal functie
function closeTaakModal() {
  taakModal.classList.remove("active");
  document.body.style.overflow = "";
}

// Open modal bij klik op "Taak aanmaken" knop
if (accountBtn) {
  accountBtn.addEventListener("click", function (e) {
    e.preventDefault();
    openTaakModal();
  });
}

// Open modal bij klik op mobiele "Taak aanmaken" knop
if (createTaskBtn) {
  createTaskBtn.addEventListener("click", function (e) {
    e.preventDefault();
    openTaakModal();
  });
}

// Sluit modal bij klik buiten de modal content (alleen voor admins)
if (taakModal) {
  taakModal.addEventListener("click", function (e) {
    if (e.target === taakModal) {
      closeTaakModal();
    }
  });
}

// File upload click handler
if (fileUpload && fotoInput) {
  fileUpload.addEventListener("click", function () {
    fotoInput.click();
  });

  fotoInput.addEventListener("change", function (e) {
    if (this.files && this.files[0]) {
      const fileName = this.files[0].name;
      const uploadIcon = fileUpload.querySelector(".upload-icon p");
      if (uploadIcon) {
        uploadIcon.textContent = fileName;
      }
    }
  });
}

// Form submit handler
if (taakForm) {
  taakForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector(".submit-btn");
    submitBtn.disabled = true;
    submitBtn.textContent = "Bezig...";
    try {
      const response = await fetch(baseUrl + "/phpcode/create_task.php", {
        method: "POST",
        body: formData,
      });
      const data = await response.json();
      if (data.success) {
        alert(data.message);
        this.reset();
        closeTaakModal();
      } else {
        alert(data.message);
      }
    } catch (err) {
      alert("Er is een fout opgetreden. Probeer het opnieuw.");
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Taak aanmaken";
    }
  });
}

// Event click handler
const events = document.querySelectorAll(".event");
events.forEach((event) => {
  event.addEventListener("click", function (e) {
    e.stopPropagation();
    const title = this.querySelector("h3").textContent;
    const author = this.querySelector("p").textContent;
    console.log("Event clicked:", title, "by", author);
    // Hier kun je een modal openen met event details
  });
});

// Day cell click handler
const dayCells = document.querySelectorAll(".day-cell");
dayCells.forEach((cell) => {
  cell.addEventListener("click", function () {
    if (!this.querySelector(".event")) {
      console.log("Empty day clicked - add new event");
      // Hier kun je een "add event" dialog tonen
    }
  });
});

// Profile circle click
const profileCircle = document.querySelector(".profile-circle");
const logoutModal = document.getElementById("logoutModal");
const cancelLogoutBtn = document.querySelector(".cancel-logout-btn");
const confirmLogoutBtn = document.querySelector(".confirm-logout-btn");

if (profileCircle) {
  profileCircle.addEventListener("click", function (e) {
    e.preventDefault();
    // Open logout modal
    logoutModal.classList.add("active");
    document.body.style.overflow = "hidden";
  });
}

// Close logout modal functions
function closeLogoutModal() {
  logoutModal.classList.remove("active");
  document.body.style.overflow = "";
}

if (cancelLogoutBtn) {
  cancelLogoutBtn.addEventListener("click", closeLogoutModal);
}

// Close modal when clicking outside
if (logoutModal) {
  logoutModal.addEventListener("click", function (e) {
    if (e.target === logoutModal) {
      closeLogoutModal();
    }
  });
}

// Confirm logout
if (confirmLogoutBtn) {
  confirmLogoutBtn.addEventListener("click", function () {
    // Redirect to logout page
    window.location.href = "logout.php";
  });
}

// Month navigation functionality
let currentMonth = new Date().getMonth(); // 0-11
let currentYear = new Date().getFullYear();

const monthNames = [
  "Januari",
  "Februari",
  "Maart",
  "April",
  "Mei",
  "Juni",
  "Juli",
  "Augustus",
  "September",
  "Oktober",
  "November",
  "December",
];

const prevMonthBtn = document.getElementById("prevMonth");
const nextMonthBtn = document.getElementById("nextMonth");
const calendarTitle = document.querySelector(".calendar-title");
const weekInfoLabel = document.querySelector(
  ".current-week-info .info-label:last-of-type",
);

// Update calendar title with current month
function updateCalendarTitle() {
  const monthYear = `${monthNames[currentMonth]} ${currentYear}`;

  if (calendarTitle) {
    calendarTitle.textContent = monthYear;
  }

  if (weekInfoLabel) {
    weekInfoLabel.textContent = monthYear;
  }
}

// Navigate to previous month
if (prevMonthBtn) {
  prevMonthBtn.addEventListener("click", function () {
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
  nextMonthBtn.addEventListener("click", function () {
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
  const daysGrid = document.querySelector(".days-grid");
  const weekNumbers = document.querySelector(".week-numbers");
  if (!daysGrid) return;

  // Get first day of month and number of days
  const firstDay = new Date(currentYear, currentMonth, 1).getDay();
  const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();

  // Adjust for Monday start (0 = Sunday, make it 6)
  const adjustedFirstDay = firstDay === 0 ? 6 : firstDay - 1;

  // Clear existing days
  daysGrid.innerHTML = "";

  // Clear and rebuild week numbers
  if (weekNumbers) {
    weekNumbers.innerHTML = '<div class="week-header"></div>';

    // Calculate week numbers for this month
    const totalDays = adjustedFirstDay + daysInMonth;
    const weeksToShow = Math.ceil(totalDays / 7);

    for (let week = 0; week < weeksToShow; week++) {
      const weekDate = new Date(
        currentYear,
        currentMonth,
        1 + week * 7 - adjustedFirstDay,
      );
      const weekNum = getWeekNumber(weekDate);
      const weekDiv = document.createElement("div");
      weekDiv.className = "week-number";

      // Check if this is the current week
      const today = new Date();
      const currentWeekNum = getWeekNumber(today);
      if (weekNum === currentWeekNum) {
        weekDiv.classList.add("current-week");
      }

      weekDiv.textContent = weekNum;
      weekNumbers.appendChild(weekDiv);
    }
  }

  // Add previous month's days
  for (let i = adjustedFirstDay - 1; i >= 0; i--) {
    const dayCell = document.createElement("div");
    dayCell.className = "day-cell prev-month";
    dayCell.textContent = daysInPrevMonth - i;
    daysGrid.appendChild(dayCell);
  }

  // Haal taken op en render ze in de juiste dag
  const year = currentYear;
  const month = currentMonth;
  const start = `${year}-${String(month + 1).padStart(2, "0")}-01`;
  const end = `${year}-${String(month + 1).padStart(2, "0")}-${String(daysInMonth).padStart(2, "0")}`;
  fetch(
    `${baseUrl}/phpcode/get_calendar_tasks.php?start=${start}&end=${end}`,
  )
    .then((res) => res.json())
    .then((tasksByDate) => {
      for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement("div");
        dayCell.className = "day-cell current-month";

        // Check if it's today
        const today = new Date();
        if (
          day === today.getDate() &&
          currentMonth === today.getMonth() &&
          currentYear === today.getFullYear()
        ) {
          dayCell.classList.add("today");
        }

        // Check if it's weekend (Saturday or Sunday)
        const dayOfWeek = new Date(currentYear, currentMonth, day).getDay();
        if (dayOfWeek === 0 || dayOfWeek === 6) {
          dayCell.classList.add("weekend");
        }

        dayCell.textContent = day;

        // Render events voor deze dag
        const dateKey = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
        if (tasksByDate[dateKey]) {
          renderEventsForDay(dayCell, tasksByDate[dateKey]);
          dayCell.classList.add("green"); // optioneel: hele dag kleuren
        }

        daysGrid.appendChild(dayCell);
      }
    });

  // Add next month's days to fill the grid
  const totalCells = adjustedFirstDay + daysInMonth;
  const remainingCells = Math.ceil(totalCells / 7) * 7 - totalCells;

  for (let day = 1; day <= remainingCells; day++) {
    const dayCell = document.createElement("div");
    dayCell.className = "day-cell prev-month";
    dayCell.textContent = day;
    daysGrid.appendChild(dayCell);
  }

  // Re-add click handlers to new day cells
  const newDayCells = daysGrid.querySelectorAll(".day-cell");
  newDayCells.forEach((cell) => {
    cell.addEventListener("click", function () {
      if (!this.querySelector(".event")) {
        console.log("Empty day clicked - add new event");
      }
    });
  });
}

// Helper function to get ISO week number
function getWeekNumber(date) {
  const d = new Date(
    Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()),
  );
  const dayNum = d.getUTCDay() || 7;
  d.setUTCDate(d.getUTCDate() + 4 - dayNum);
  const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
  return Math.ceil(((d - yearStart) / 86400000 + 1) / 7);
}

// Week navigation
let currentWeekDate = new Date();
const prevWeekBtn = document.getElementById("prevWeek");
const nextWeekBtn = document.getElementById("nextWeek");
const weekBadge = document.querySelector(".week-badge");
const selectedWeekNum = document.getElementById("selectedWeekNum");

// Navigate to previous week
if (prevWeekBtn) {
  prevWeekBtn.addEventListener("click", function () {
    currentWeekDate.setDate(currentWeekDate.getDate() - 7);
    updateWeekInfo();
    regenerateWeekView();
  });
}

// Navigate to next week
if (nextWeekBtn) {
  nextWeekBtn.addEventListener("click", function () {
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
  const weekGrid = document.querySelector(".week-grid");
  const weekDaysContainer = document.querySelector(".week-days-container");
  const mobileWeekView = document.querySelector(".mobile-week-view");

  if (!weekGrid) return;

  // Calculate start of week (Sunday)
  const weekStart = new Date(currentWeekDate);
  const day = weekStart.getDay();
  const diff = weekStart.getDate() - day;
  weekStart.setDate(diff);

  const daysOfWeek = ["SUN", "MON", "TUE", "WED", "THUR", "FRI", "SAT"];
  const monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];

  // Clear and rebuild desktop week headers
  weekGrid.innerHTML = "";
  const today = new Date();
  for (let i = 0; i < 7; i++) {
    const dayDate = new Date(weekStart);
    dayDate.setDate(weekStart.getDate() + i);
    const isToday = dayDate.toDateString() === today.toDateString();
    const dayNumber = dayDate.getDate();
    const displayDate = isToday
      ? `${monthNames[dayDate.getMonth()]} ${dayNumber}`
      : dayNumber;
    const dayHeader = document.createElement("div");
    dayHeader.className = "week-day-header";
    if (isToday) dayHeader.classList.add("active-day");
    dayHeader.innerHTML = `
            <div class="day-label">${daysOfWeek[i]}</div>
            <div class="day-number">${displayDate}</div>
        `;
    weekGrid.appendChild(dayHeader);
  }

  // Clear desktop week days container
  if (weekDaysContainer) {
    weekDaysContainer.innerHTML = "";
    // Maak 7 kolommen aan met data-date
    for (let i = 0; i < 7; i++) {
      const dayDate = new Date(weekStart);
      dayDate.setDate(weekStart.getDate() + i);
      const column = document.createElement("div");
      column.className = "week-day-column";
      weekDaysContainer.appendChild(column);
    }
    setWeekDates(weekStart);
    // Haal taken op voor deze week
    const weekStartStr = `${weekStart.getFullYear()}-${String(weekStart.getMonth() + 1).padStart(2, "0")}-${String(weekStart.getDate()).padStart(2, "0")}`;
    const weekEndDate = new Date(weekStart);
    weekEndDate.setDate(weekStart.getDate() + 6);
    const weekEndStr = `${weekEndDate.getFullYear()}-${String(weekEndDate.getMonth() + 1).padStart(2, "0")}-${String(weekEndDate.getDate()).padStart(2, "0")}`;
    fetch(
      `${baseUrl}/phpcode/get_calendar_tasks.php?start=${weekStartStr}&end=${weekEndStr}`,
    )
      .then((res) => res.json())
      .then((tasksByDate) => {
        populateWeekView(tasksByDate);
        enableWeekDayClick(tasksByDate); // 👈 nieuw
      });
  }

  // Clear and rebuild mobile week view
  if (mobileWeekView) {
    mobileWeekView.innerHTML = "";
    // Haal taken op voor deze week
    const weekStartStr = `${weekStart.getFullYear()}-${String(weekStart.getMonth() + 1).padStart(2, "0")}-${String(weekStart.getDate()).padStart(2, "0")}`;
    const weekEndDate = new Date(weekStart);
    weekEndDate.setDate(weekStart.getDate() + 6);
    const weekEndStr = `${weekEndDate.getFullYear()}-${String(weekEndDate.getMonth() + 1).padStart(2, "0")}-${String(weekEndDate.getDate()).padStart(2, "0")}`;
    fetch(
      `${baseUrl}/phpcode/get_calendar_tasks.php?start=${weekStartStr}&end=${weekEndStr}`,
    )
      .then((res) => res.json())
      .then((tasksByDate) => {
        for (let i = 0; i < 7; i++) {
          const dayDate = new Date(weekStart);
          dayDate.setDate(weekStart.getDate() + i);
          const dayNumber = dayDate.getDate();
          const dateKey = `${dayDate.getFullYear()}-${String(dayDate.getMonth() + 1).padStart(2, "0")}-${String(dayDate.getDate()).padStart(2, "0")}`;
          const events = tasksByDate[dateKey] || [];
          const mobileDay = document.createElement("div");
          mobileDay.className = "mobile-week-day";
          let contentClass = "";
          if (events.length > 0 && events[0].color === "#cccccc")
            contentClass = "green-day";
          if (events.length > 0 && events[0].color === "#ffb8d1")
            contentClass = "pink-day";
          // Genereer de dagcel
          mobileDay.innerHTML = `
                        <div class="mobile-day-label">${daysOfWeek[i]}</div>
                        <div class="mobile-day-content ${contentClass}">
                            <div class="mobile-day-number">${dayNumber}</div>
                            ${events
                              .map(
                                (ev, idx) => `
                                <div class=\"mobile-week-event\" data-idx=\"${idx}\" style=\"cursor:pointer;\">
                                    <div class=\"event-time\">${ev.start} - ${ev.end}</div>
                                    <div class=\"event-title\">${ev.title}</div>
                                </div>
                            `,
                              )
                              .join("")}
                            ${events.length > 0 ? `<div class=\"event-author\">${events[0].author || ""}</div>` : ""}
                        </div>
                    `;
          // Click handler op dag zelf (voor dagen zonder taken)
          mobileDay.onclick = function (e) {
            // Alleen uitvoeren als er niet op een taak geklikt is
            if (!e.target.closest(".mobile-week-event")) {
              showMobileTasksForDay(dateKey, events);
            }
          };
          // Click handler op elke taak
          setTimeout(() => {
            const eventDivs = mobileDay.querySelectorAll(".mobile-week-event");
            eventDivs.forEach((evDiv) => {
              evDiv.onclick = function (e) {
                e.stopPropagation();
                showMobileTasksForDay(dateKey, events);
              };
            });
          }, 0);
          mobileWeekView.appendChild(mobileDay);
        }
      });
  }
}

// Initialize with current month

// Init week view direct op juiste week en update info
document.addEventListener("DOMContentLoaded", function () {
  updateCalendarTitle();
  regenerateCalendar(); // 👈 deze ontbrak
  updateWeekInfo();
  regenerateWeekView();
});

console.log("Agenda pagina geladen!");
