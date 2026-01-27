<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}


$user = $_SESSION['user'];
$userInitial = '';
if (isset($user['first_name'])) {
    $userInitial = strtoupper(substr($user['first_name'], 0, 1));
} elseif (isset($user['voornaam'])) {
    $userInitial = strtoupper(substr($user['voornaam'], 0, 1));
}
$userName = '';
if (isset($user['first_name']) && isset($user['last_name'])) {
    $userName = $user['first_name'] . ' ' . $user['last_name'];
} elseif (isset($user['voornaam']) && isset($user['achternaam'])) {
    $userName = $user['voornaam'] . ' ' . $user['achternaam'];
}

// Haal huidige datum en weeknummers op
$currentDate = new DateTime();
$currentWeek = (int)$currentDate->format('W'); // Huidig weeknummer
$currentMonth = $currentDate->format('F'); // Maandnaam
$currentYear = $currentDate->format('Y');

// Bereken eerste dag van demaand
$firstDayOfMonth = new DateTime($currentYear . '-' . $currentDate->format('m') . '-01');
$firstWeekOfMonth = (int)$firstDayOfMonth->format('W');

// Als het januari is en week 52/53, dan is het vorige jaar
if ($currentDate->format('m') == '01' && $firstWeekOfMonth > 50) {
    $firstWeekOfMonth = 1;
}

// Bereken aantal weken in deze maand weergave (meestal 5-6 weken)
$lastDayOfMonth = new DateTime($currentYear . '-' . $currentDate->format('m') . '-' . $currentDate->format('t'));
$lastWeekOfMonth = (int)$lastDayOfMonth->format('W');

// Genereer array met weeknummers voor deze maand
$weekNumbers = [];
if ($firstWeekOfMonth <= $lastWeekOfMonth) {
    for ($i = $firstWeekOfMonth; $i <= $lastWeekOfMonth; $i++) {
        $weekNumbers[] = $i;
    }
} else {
    // Voor december/januari overgangen
    for ($i = $firstWeekOfMonth; $i <= 52; $i++) {
        $weekNumbers[] = $i;
    }
    for ($i = 1; $i <= $lastWeekOfMonth; $i++) {
        $weekNumbers[] = $i;
    }
}

// Zorg ervoor dat we tenminste 2 weeknummers hebben
while (count($weekNumbers) < 2) {
    $weekNumbers[] = end($weekNumbers) + 1;
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda - Gouden Schoen</title>
    <link rel="stylesheet" href="../css/agenda.css">
</head>

<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="../images/fc_team_zonder_plan.png" alt="FC Team zonder plan logo">
            </div>
            <nav>
                <a href="../index.php" class="nav-icon home-icon" title="Home">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                    </svg>
                </a>
                <a href="agenda.php" class="nav-icon calendar-icon active" title="Kalender">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z" />
                    </svg>
                </a>
                <?php if (isset($user['role_id']) && $user['role_id'] == 2): ?>
                    <a href="admin_dashboard.php" class="nav-icon admin-icon" title="Admin" style="color:#6b5b95;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="32" height="32">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" />
                        </svg>
                    </a>
                <?php endif; ?>
                <?php if ($userInitial): ?>
                    <div class="nav-icon profile-icon" title="Profiel" style="cursor:pointer;">
                        <div class="profile-circle" id="profileBtn"><?php echo $userInitial; ?></div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="nav-icon profile-icon" title="Profiel" id="profileBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <section class="calendar-section">
            <div class="container">
                <!-- Mobile create task button -->
                <?php if (isset($user['role_id']) && $user['role_id'] == 2): ?>
                    <div class="mobile-create-task">
                        <button class="create-task-btn">
                            + Taak aanmaken
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (isset($user['role_id']) && $user['role_id'] == 2): ?>
                    <div class="calendar-controls">
                        <button class="account-btn">
                            Taak aanmaken
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z" />
                            </svg>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="calendar-header">

                    <div class="view-toggle">
                        <button class="toggle-btn" data-view="week">week</button>
                        <button class="toggle-btn active" data-view="month">maand</button>
                    </div>
                </div>
                <div class="month-navigation">
                    <button class="month-nav-btn prev-month-btn" id="prevMonth" title="Vorige maand">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                        </svg>
                    </button>
                    <h2 class="calendar-title"></h2>
                    <button class="month-nav-btn next-month-btn" id="nextMonth" title="Volgende maand">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z" />
                        </svg>
                    </button>
                </div>

                <div class="current-week-info">
                    <div class="week-navigation" id="weekNavigation" style="display: none;">
                        <button class="month-nav-btn" id="prevWeek" title="Vorige week">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                            </svg>
                        </button>
                        <span class="selected-week-badge" style="margin: 0 10px; font-weight: 600; color: #6b5b95;">Week <span id="selectedWeekNum"></span></span>
                        <button class="month-nav-btn" id="nextWeek" title="Volgende week">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z" />
                            </svg>
                        </button>
                    </div>
                    <span class="info-label">Huidige week:</span>
                    <span class="week-badge"><?php echo $currentWeek; ?></span>
                    <span class="info-separator">|</span>
                    <span class="info-label"><?php echo $currentMonth . ' ' . $currentYear; ?></span>
                </div>

                <!-- Month View -->
                <div class="month-view">

                    <!-- Desktop Calendar -->
                    <div class="calendar-grid desktop-calendar">
                        <!-- Week numbers column -->
                        <div class="week-numbers">
                            <div class="week-header"></div>
                            <?php
                            // Toon maximaal 6 weeknummers (voor typische maandweergave)
                            $displayWeeks = array_slice($weekNumbers, 0, 6);
                            foreach ($displayWeeks as $weekNum):
                                $isCurrentWeek = ($weekNum == $currentWeek) ? 'current-week' : '';
                            ?>
                                <div class="week-number <?php echo $isCurrentWeek; ?>"><?php echo $weekNum; ?></div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Calendar days -->
                        <div class="days-grid">
                            <!-- Dynamisch gevuld door JavaScript -->
                        </div>
                    </div>

                    <!-- Mobile Calendar: dynamisch gevuld door JS -->
                    <div class="mobile-calendar"></div>
                    <noscript>
                        <div style="padding:1em;color:#b00;background:#fff3f3;border-radius:8px;text-align:center;">
                            Activeer JavaScript om de kalender te zien.
                        </div>
                    </noscript>
                </div>

                <!-- Week View -->
                <div class="week-view" style="display: none;">
                    <!-- Desktop Week View -->
                    <div class="desktop-week-view">
                        <div class="week-grid">
                            <?php
                            // Bereken de dagen van de huidige week (zondag tot zaterdag)
                            $weekStart = clone $currentDate;
                            $weekStart->modify('this week'); // Maandag van deze week

                            // Ga terug naar zondag
                            $weekStart->modify('-1 day');

                            $daysOfWeek = ['MA', 'DI', 'WO', 'DO', 'VR', 'ZA', 'ZO'];


                            for ($i = 0; $i < 6; $i++): // Toon 6 dagen (zondag t/m vrijdag zoals in design)
                                $day = clone $weekStart;
                                $day->modify("+$i days");

                                $isToday = ($day->format('Y-m-d') == $currentDate->format('Y-m-d'));
                                $dayNumber = $day->format('j');
                                $displayDate = ($i == 2) ? $day->format('F j') : $dayNumber; // Toon maand bij 3e dag
                            ?>
                                <div class="week-day-header <?php echo $isToday ? 'active-day' : ''; ?>">
                                    <div class="day-label"><?php echo $daysOfWeek[$i]; ?></div>
                                    <div class="day-number"><?php echo $displayDate; ?></div>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <div class="week-days-container">
                            <!-- Sunday -->
                            <div class="week-day-column"></div>

                            <!-- Monday -->
                            <div class="week-day-column"></div>

                            <!-- Tuesday (April 1) -->
                            <div class="week-day-column active-column">
                                <div class="week-event green-bg">
                                    <div class="event-time">10:00</div>
                                    <div class="event-title">5-Minute Workouts</div>
                                </div>
                                <div class="week-event green-bg">
                                    <div class="event-time">11:00</div>
                                    <div class="event-title">How to Start Exercising</div>
                                </div>
                                <div class="week-event green-bg">
                                    <div class="event-time">15:00</div>
                                    <div class="event-title">Strength Training</div>
                                </div>
                                <div class="week-event green-bg">
                                    <div class="event-time">18:00</div>
                                    <div class="event-title">How to Create a Workout</div>
                                    <div class="event-extra">...</div>
                                </div>
                                <div class="event-author">Robert Fox</div>
                            </div>

                            <!-- Wednesday -->
                            <div class="week-day-column"></div>

                            <!-- Thursday -->
                            <div class="week-day-column"></div>

                            <!-- Friday (April 4) -->
                            <div class="week-day-column">
                                <div class="week-event pink-bg">
                                    <div class="event-time">10:00</div>
                                    <div class="event-title">5-Minute Workouts</div>
                                </div>
                                <div class="week-event pink-bg">
                                    <div class="event-time">11:00</div>
                                    <div class="event-title">How to Start Exercising</div>
                                </div>
                                <div class="week-event pink-bg">
                                    <div class="event-time">15:00</div>
                                    <div class="event-title">Strength Training</div>
                                </div>
                                <div class="week-event pink-bg">
                                    <div class="event-time">18:00</div>
                                    <div class="event-title">How to Create a Workout</div>
                                    <div class="event-extra">...</div>
                                </div>
                                <div class="event-author">Annette Black</div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Week View -->
                    <div class="mobile-week-view">
                        <div class="mobile-week-day">
                            <div class="mobile-day-label">Ma</div>
                            <div class="mobile-day-content">
                                <div class="mobile-day-number">30</div>
                            </div>
                        </div>

                        <div class="mobile-week-day">
                            <div class="mobile-day-label">Di</div>
                            <div class="mobile-day-content">
                                <div class="mobile-day-number">31</div>
                            </div>
                        </div>

                        <div class="mobile-week-day">
                            <div class="mobile-day-label">Wo</div>
                            <div class="mobile-day-content green-day">
                                <div class="mobile-day-number">1</div>
                                <div class="mobile-week-event">
                                    <div class="event-time">15:00</div>
                                    <div class="event-title">Strength Training</div>
                                </div>
                                <div class="mobile-week-event">
                                    <div class="event-time">18:00</div>
                                    <div class="event-title">How to Create a Workout</div>
                                </div>
                                <div class="mobile-week-event">
                                    <div class="event-time">18:00</div>
                                    <div class="event-title">How to Create a Workout</div>
                                </div>
                                <div class="event-author">Robert Fox</div>
                            </div>
                        </div>

                        <div class="mobile-week-day">
                            <div class="mobile-day-label">Do</div>
                            <div class="mobile-day-content">
                                <div class="mobile-day-number">2</div>
                            </div>
                        </div>

                        <div class="mobile-week-day">
                            <div class="mobile-day-label">Vr</div>
                            <div class="mobile-day-content pink-day">
                                <div class="mobile-day-number">3</div>
                                <div class="mobile-week-event">
                                    <div class="event-time">15:00</div>
                                    <div class="event-title">Strength Training</div>
                                </div>
                                <div class="mobile-week-event">
                                    <div class="event-time">18:00</div>
                                    <div class="event-title">How to Create a Workout</div>
                                </div>
                                <div class="mobile-week-event">
                                    <div class="event-time">18:00</div>
                                    <div class="event-title">How to Create a Workout</div>
                                </div>
                                <div class="event-author">Annette Black</div>
                            </div>
                        </div>

                        <div class="mobile-week-day">
                            <div class="mobile-day-label">Za</div>
                            <div class="mobile-day-content pink-day">
                                <div class="mobile-day-number">4</div>
                                <div class="mobile-week-event">
                                    <div class="event-time">15:00</div>
                                    <div class="event-title">Strength Training</div>
                                </div>
                                <div class="mobile-week-event">
                                    <div class="event-time">18:00</div>
                                    <div class="event-title">How to Create a Workout</div>
                                </div>
                                <div class="mobile-week-event">
                                    <div class="event-time">18:00</div>
                                    <div class="event-title">How to Create a Workout</div>
                                </div>
                                <div class="event-author">Robert Fox</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Tasks Section -->
                <div class="mobile-tasks-section">
                    <h3 class="tasks-date">Taken 5 April</h3>
                    <div class="tasks-list" id="tasksList">
                        <div class="task-item">
                            <div class="task-time">18:00</div>
                            <div class="task-title">Strength Training</div>
                        </div>
                        <div class="task-item">
                            <div class="task-time">18:00</div>
                            <div class="task-title">How to Create a Workout</div>
                        </div>
                        <div class="task-item">
                            <div class="task-time">18:00</div>
                            <div class="task-title">How to Create a Workout</div>
                        </div>
                    </div>
                    <div class="no-tasks" id="noTasks" style="display: none;">
                        <p>U heeft geen taken op deze dag</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php if (isset($user['role_id']) && $user['role_id'] == 2): ?>
        <!-- Taak Aanmaken Modal -->
        <div id="taakModal" class="taak-modal">
            <div class="taak-modal-content">
                <h2 class="taak-modal-title">Taak aanmaken</h2>
                <form id="taakForm" class="taak-form">
                    <!-- ...bestaande formulier velden... -->
                    <?php /* De volledige inhoud van het formulier blijft ongewijzigd */ ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Taak naam</label>
                            <input type="text" name="taaknaam" placeholder="Placeholder" required>
                        </div>
                        <div class="form-group">
                            <label>Categorie</label>
                            <select name="categorie" id="categorieSelect" required>
                                <option value="">Selecteer categorie...</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label>Personeel toevoegen</label>
                            <div style="position:relative;">
                                <input type="text" id="personeelInput" placeholder="Zoek personeel..." autocomplete="off" style="width:100%;">
                                <div id="personeelSuggestions" class="personeel-suggestions" style="border:1px solid #ccc;display:none;position:absolute;z-index:10;background:#fff;max-height:150px;overflow-y:auto;width:100%;top:100%;left:0;"></div>
                            </div>
                            <div id="selectedPersoneel" class="selected-personeel" style="margin-top:8px;display:flex;flex-wrap:wrap;gap:6px;"></div>
                            <input type="hidden" name="personeel" id="personeelHidden">
                            <small>Typ om personeel te zoeken en klik om toe te voegen. Klik op een naam onder de input om te verwijderen.</small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Datum</label>
                            <input type="date" name="datum" id="datumInput" required>
                        </div>
                        <div class="form-group">
                            <label>Start tijd</label>
                            <input type="time" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label>Eind tijd</label>
                            <input type="time" name="end_time" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Dag</label>
                            <input type="number" name="day" id="dayInput" min="1" max="31" readonly>
                        </div>
                        <div class="form-group">
                            <label>Week</label>
                            <input type="number" name="week" id="weekInput" min="1" max="53" readonly>
                        </div>
                        <div class="form-group">
                            <label>Maand</label>
                            <input type="number" name="month" id="monthInput" min="1" max="12" readonly>
                        </div>
                        <div class="form-group">
                            <label>Jaar</label>
                            <input type="number" name="year" id="yearInput" min="2020" max="2100" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Herhaling</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="herhaling" value="eenmalig" checked>
                                    <span>Eenmalig</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="herhaling" value="dagelijks">
                                    <span>Dagelijks</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="herhaling" value="wekelijks">
                                    <span>Wekelijks</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="herhaling" value="maandelijks">
                                    <span>Maandelijks</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Max aantal leden</label>
                            <select name="maxleden">
                                <option value="">Selecteer aantal</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label>Beschrijving</label>
                        <textarea name="beschrijving" placeholder="Placeholder" rows="4"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Foto toevoegen</label>
                        <div class="file-upload">
                            <div class="upload-icon">
                                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke="#999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p>Voeg foto toe</p>
                            </div>
                            <input type="file" name="foto" accept="image/*" style="display: none;" id="fotoInput">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="submit-btn">Taak aanmaken</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Uitloggen Modal -->
    <div id="logoutModal" class="taak-modal">
        <div class="taak-modal-content logout-modal-content">
            <h2 class="taak-modal-title">Uitloggen</h2>
            <p class="modal-text">Weet je zeker dat je wilt uitloggen?</p>
            <div class="form-actions logout-actions">
                <button type="button" class="cancel-logout-btn">Annuleer</button>
                <button type="button" class="confirm-logout-btn">Ja</button>
            </div>
        </div>
    </div>

    <script src="../js/agenda.js"></script>
    <script>
        // User role voor admin checks
        const userIsAdmin = <?php echo (isset($user['role_id']) && $user['role_id'] == 2) ? 'true' : 'false'; ?>;

        // Gebruik de baseUrl uit agenda.js (al geladen hierboven)
        // Dynamisch categorieën laden
        function loadCategories() {
            fetch(baseUrl + '/phpcode/get_categories.php')
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('categorieSelect');
                    if (!select) return;
                    select.innerHTML = '<option value="">Selecteer categorie...</option>';
                    if (data.success && data.categories.length) {
                        data.categories.forEach(cat => {
                            select.innerHTML += `<option value="${cat.category_id}">${cat.name}</option>`;
                        });
                    }
                });
        }
        document.addEventListener('DOMContentLoaded', loadCategories);

        // Vul dag/week/maand/jaar automatisch in op basis van datum
        document.addEventListener('DOMContentLoaded', function() {
            const datumInput = document.getElementById('datumInput');
            const dayInput = document.getElementById('dayInput');
            const weekInput = document.getElementById('weekInput');
            const monthInput = document.getElementById('monthInput');
            const yearInput = document.getElementById('yearInput');
            if (datumInput) {
                datumInput.addEventListener('change', function() {
                    if (!this.value) return;
                    const date = new Date(this.value);
                    if (isNaN(date)) return;
                    // Dag
                    if (dayInput) dayInput.value = date.getDate();
                    // Maand (1-12)
                    if (monthInput) monthInput.value = date.getMonth() + 1;
                    // Jaar
                    if (yearInput) yearInput.value = date.getFullYear();
                    // Weeknummer
                    if (weekInput) {
                        // Bereken ISO weeknummer
                        const tempDate = new Date(date.getTime());
                        tempDate.setHours(0, 0, 0, 0);
                        // Donderdag in deze week bepaalt het weeknummer
                        tempDate.setDate(tempDate.getDate() + 3 - ((tempDate.getDay() + 6) % 7));
                        const week1 = new Date(tempDate.getFullYear(), 0, 4);
                        const weekNum = 1 + Math.round(((tempDate.getTime() - week1.getTime()) / 86400000 - 3 + ((week1.getDay() + 6) % 7)) / 7);
                        weekInput.value = weekNum;
                    }
                });
            }
        });
    </script>

</body>

</html>