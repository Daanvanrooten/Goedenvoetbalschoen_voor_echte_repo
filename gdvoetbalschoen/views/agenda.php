<?php
session_start();

// Simuleer ingelogde gebruiker (vervang dit later met echte database login)
if (!isset($_SESSION['user'])) {
    // Default test gebruiker
    $_SESSION['user'] = [
        'id' => 1,
        'voornaam' => 'Pietje',
        'achternaam' => 'Bell',
        'email' => 'pietje@example.com'
    ];
}

$user = $_SESSION['user'];
$userInitial = strtoupper(substr($user['voornaam'], 0, 1));
$userName = $user['voornaam'] . ' ' . $user['achternaam'];

// Haal huidige datum en weeknummers op
$currentDate = new DateTime();
$currentWeek = (int)$currentDate->format('W'); // Huidig weeknummer
$currentMonth = $currentDate->format('F'); // Maandnaam
$currentYear = $currentDate->format('Y');

// Bereken eerste dag van de maand
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
                <a href="../index.html" class="nav-icon home-icon" title="Home">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                </a>
                <a href="agenda.php" class="nav-icon calendar-icon active" title="Kalender">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                    </svg>
                </a>
                <a href="profiel.php" class="nav-icon profile-icon" title="Profiel">
                    <div class="profile-circle"><?php echo $userInitial; ?></div>
                </a>
            </nav>
        </div>
    </header>

    <main>
        <section class="calendar-section">
            <div class="container">
                <!-- Mobile create task button -->
                <div class="mobile-create-task">
                    <button class="create-task-btn">
                        + Taak aanmaken
                    </button>
                </div>

                <div class="calendar-controls">
                    <button class="account-btn" onclick="window.location.href='login.php'">
                        Account
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </button>
                </div>
                
                <div class="calendar-header">
                    <h2 class="calendar-title"><?php echo htmlspecialchars($userName); ?>'s schema/Mijn schema</h2>
                    <div class="view-toggle">
                        <button class="toggle-btn" data-view="week">week</button>
                        <button class="toggle-btn active" data-view="month">maand</button>
                    </div>
                </div>
                
                <div class="current-week-info">
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
                        <!-- Week 1 -->
                        <div class="day-cell prev-month"></div>
                        <div class="day-cell prev-month"></div>
                        <div class="day-cell current-month green">
                            <div class="event green-event">
                                <h3>5-Minute Workouts for Busy People</h3>
                                <p>Robert Fox</p>
                            </div>
                        </div>
                        <div class="day-cell current-month">2</div>
                        <div class="day-cell current-month">3</div>
                        <div class="day-cell current-month pink">
                            <div class="event pink-event">
                                <h3>How to Start Exercising as a Beginner</h3>
                                <p>Annette Black</p>
                            </div>
                        </div>
                        <div class="day-cell weekend">5</div>

                        <!-- Week 2 -->
                        <div class="day-cell current-month">6</div>
                        <div class="day-cell current-month green">
                            <div class="event green-event">
                                <h3>Best Stretches to Improve Flexibility</h3>
                                <p>Dianne Russell</p>
                            </div>
                        </div>
                        <div class="day-cell current-month">8</div>
                        <div class="day-cell current-month pink">9</div>
                        <div class="day-cell current-month">10</div>
                        <div class="day-cell current-month">11</div>
                        <div class="day-cell weekend">12</div>

                        <!-- Week 3 -->
                        <div class="day-cell current-month">13</div>
                        <div class="day-cell current-month pink">
                            <div class="event pink-event">
                                <h3>How to Stay Motivated to Work Out</h3>
                                <p>Kristin Watson</p>
                            </div>
                        </div>
                        <div class="day-cell current-month">15</div>
                        <div class="day-cell current-month">16</div>
                        <div class="day-cell current-month">17</div>
                        <div class="day-cell current-month pink">
                            <div class="event pink-event">
                                <h3>The Benefits of Walking Every Day</h3>
                                <p>Devon Lane</p>
                            </div>
                        </div>
                        <div class="day-cell weekend">19</div>

                        <!-- Week 4 -->
                        <div class="day-cell current-month yellow">
                            <div class="event yellow-event">
                                <h3>Strength Training vs. Cardio: Which is Better?</h3>
                                <p>Eleanor Pena</p>
                            </div>
                        </div>
                        <div class="day-cell current-month">21</div>
                        <div class="day-cell current-month">22</div>
                        <div class="day-cell current-month green">
                            <div class="event green-event">
                                <h3>Simple Exercises to Reduce Back Pain</h3>
                                <p>Jane Cooper</p>
                            </div>
                        </div>
                        <div class="day-cell current-month">24</div>
                        <div class="day-cell current-month">25</div>
                        <div class="day-cell current-month pink">
                            <div class="event pink-event">
                                <h3>How to Create a Workout Routine That Works for You</h3>
                                <p>Marvin McKinney</p>
                            </div>
                        </div>

                        <!-- Week 5 -->
                        <div class="day-cell current-month">27</div>
                        <div class="day-cell current-month">28</div>
                    </div>
                </div>

                <!-- Mobile Calendar -->
                <div class="mobile-calendar">
                    <table class="mobile-calendar-table">
                        <tbody>
                            <tr>
                                <td class="day-label">SUN</td>
                                <td>2</td>
                                <td>9</td>
                                <td>16</td>
                                <td>23</td>
                                <td class="green-cell">30</td>
                            </tr>
                            <tr>
                                <td class="day-label">MON</td>
                                <td>3</td>
                                <td>10</td>
                                <td>17</td>
                                <td>24</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="day-label">TUE</td>
                                <td>4</td>
                                <td>11</td>
                                <td>18</td>
                                <td>25</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="day-label">WED</td>
                                <td>5</td>
                                <td>12</td>
                                <td>19</td>
                                <td>26</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="day-label">THUR</td>
                                <td>6</td>
                                <td>13</td>
                                <td>20</td>
                                <td>27</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="day-label">FRI</td>
                                <td>7</td>
                                <td>14</td>
                                <td>21</td>
                                <td>28</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="day-label">Sat</td>
                                <td>1</td>
                                <td>8</td>
                                <td>15</td>
                                <td>22</td>
                                <td>29</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
                        
                        $daysOfWeek = ['SUN', 'MON', 'TUE', 'WED', 'THUR', 'FRI', 'SAT'];
                        
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
                            <div class="mobile-day-label">SUN</div>
                            <div class="mobile-day-content">
                                <div class="mobile-day-number">30</div>
                            </div>
                        </div>

                        <div class="mobile-week-day">
                            <div class="mobile-day-label">MON</div>
                            <div class="mobile-day-content">
                                <div class="mobile-day-number">31</div>
                            </div>
                        </div>

                        <div class="mobile-week-day">
                            <div class="mobile-day-label">TUE</div>
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
                            <div class="mobile-day-label">WED</div>
                            <div class="mobile-day-content">
                                <div class="mobile-day-number">2</div>
                            </div>
                        </div>

                        <div class="mobile-week-day">
                            <div class="mobile-day-label">THUR</div>
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
                            <div class="mobile-day-label">FRI</div>
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

    <script src="../js/agenda.js"></script>
</body>
</html>
