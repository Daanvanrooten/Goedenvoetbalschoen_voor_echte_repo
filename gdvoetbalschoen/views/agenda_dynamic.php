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

// Agenda events - dit zou normaal uit een database komen
$events = [
    [
        'date' => '2025-04-01',
        'title' => '5-Minute Workouts for Busy People',
        'author' => 'Robert Fox',
        'color' => 'green'
    ],
    [
        'date' => '2025-04-04',
        'title' => 'How to Start Exercising as a Beginner',
        'author' => 'Annette Black',
        'color' => 'pink'
    ],
    [
        'date' => '2025-04-06',
        'title' => 'Best Stretches to Improve Flexibility',
        'author' => 'Dianne Russell',
        'color' => 'green'
    ],
    [
        'date' => '2025-04-14',
        'title' => 'How to Stay Motivated to Work Out',
        'author' => 'Kristin Watson',
        'color' => 'pink'
    ],
    [
        'date' => '2025-04-18',
        'title' => 'The Benefits of Walking Every Day',
        'author' => 'Devon Lane',
        'color' => 'pink'
    ],
    [
        'date' => '2025-04-20',
        'title' => 'Strength Training vs. Cardio: Which is Better?',
        'author' => 'Eleanor Pena',
        'color' => 'yellow'
    ],
    [
        'date' => '2025-04-23',
        'title' => 'Simple Exercises to Reduce Back Pain',
        'author' => 'Jane Cooper',
        'color' => 'green'
    ],
    [
        'date' => '2025-04-26',
        'title' => 'How to Create a Workout Routine That Works for You',
        'author' => 'Marvin McKinney',
        'color' => 'pink'
    ]
];
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
                <div class="calendar-header">
                    <button class="account-btn" onclick="window.location.href='account.php'">
                        Account
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </button>
                    <h2 class="calendar-title"><?php echo htmlspecialchars($userName); ?>'s schema/Mijn schema</h2>
                    <div class="view-toggle">
                        <button class="toggle-btn" data-view="week">week</button>
                        <button class="toggle-btn active" data-view="month">maand</button>
                    </div>
                </div>

                <div class="calendar-grid">
                    <!-- Week numbers column -->
                    <div class="week-numbers">
                        <div class="week-header"></div>
                        <div class="week-number">30</div>
                        <div class="week-number">31</div>
                    </div>

                    <!-- Calendar days -->
                    <div class="days-grid">
                        <!-- Header with day numbers -->
                        <div class="day-header prev-month">30</div>
                        <div class="day-header prev-month">31</div>
                        <div class="day-header current-month">
                            <span class="month-label">April</span> 1
                        </div>
                        <div class="day-header current-month">2</div>
                        <div class="day-header current-month">3</div>
                        <div class="day-header current-month">4</div>
                        <div class="day-header weekend">5</div>

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

                        <!-- Days row 2 -->
                        <div class="day-header current-month">6</div>
                        <div class="day-header current-month">7</div>
                        <div class="day-header current-month">8</div>
                        <div class="day-header current-month">9</div>
                        <div class="day-header current-month">10</div>
                        <div class="day-header current-month">11</div>
                        <div class="day-header weekend">12</div>

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

                        <!-- Days row 3 -->
                        <div class="day-header current-month">13</div>
                        <div class="day-header current-month">14</div>
                        <div class="day-header current-month">15</div>
                        <div class="day-header current-month">16</div>
                        <div class="day-header current-month">17</div>
                        <div class="day-header current-month">18</div>
                        <div class="day-header weekend">19</div>

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

                        <!-- Days row 4 -->
                        <div class="day-header current-month">20</div>
                        <div class="day-header current-month">21</div>
                        <div class="day-header current-month">22</div>
                        <div class="day-header current-month">23</div>
                        <div class="day-header current-month">24</div>
                        <div class="day-header current-month">25</div>
                        <div class="day-header weekend">26</div>

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

                        <!-- Days row 5 -->
                        <div class="day-header current-month">27</div>
                        <div class="day-header current-month">28</div>
                        <div class="day-header current-month">29</div>
                        <div class="day-header current-month">30</div>

                        <!-- Week 5 -->
                        <div class="day-cell current-month">27</div>
                        <div class="day-cell current-month">28</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="../js/agenda.js"></script>
</body>
</html>
