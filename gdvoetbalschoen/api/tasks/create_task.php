<?php
session_start();
require_once '../../config/db_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
    exit;
}


$user_id = $_SESSION['user']['id'];
$title = trim($_POST['taaknaam'] ?? '');
$category = $_POST['categorie'] ?? null;
$date = $_POST['datum'] ?? null;
$start_time = $_POST['start_time'] ?? null;
$end_time = $_POST['end_time'] ?? null;
$herhaling = $_POST['herhaling'] ?? 'eenmalig';
$eind_datum = $_POST['eind_datum'] ?? null;
$maxleden = $_POST['maxleden'] ?? null;
$beschrijving = trim($_POST['beschrijving'] ?? '');
// Personeel (array van user_id's)
$personeel = isset($_POST['personeel']) ? (array)$_POST['personeel'] : [];

// Nieuwe velden voor dag/week/maand/jaar en frequentie
$day = isset($_POST['day']) ? intval($_POST['day']) : null;
$week = isset($_POST['week']) ? intval($_POST['week']) : null;
$month = isset($_POST['month']) ? intval($_POST['month']) : null;
$year = isset($_POST['year']) ? intval($_POST['year']) : null;
$herhaling = $_POST['herhaling'] ?? 'eenmalig';
$frequency = null;
if ($herhaling === 'dagelijks') $frequency = 'DAILY';
elseif ($herhaling === 'wekelijks') $frequency = 'WEEKLY';
elseif ($herhaling === 'maandelijks') $frequency = 'MONTHLY';
else $frequency = null;

// Validatie
$errors = [];
if (empty($title)) $errors[] = 'Taaknaam is verplicht';
if (empty($category)) $errors[] = 'Categorie is verplicht';
if (empty($date)) $errors[] = 'Datum is verplicht';
if (empty($start_time)) $errors[] = 'Start tijd is verplicht';
if (empty($end_time)) $errors[] = 'Eind tijd is verplicht';

// Validatie: check of datum niet in het verleden is
if (!empty($date)) {
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    $selectedDate = new DateTime($date);
    $selectedDate->setTime(0, 0, 0);
    
    if ($selectedDate < $today) {
        $errors[] = 'Datum kan niet in het verleden liggen';
    }
    
    // Als datum vandaag is, check of starttijd niet in verleden is
    if ($selectedDate->format('Y-m-d') === $today->format('Y-m-d') && !empty($start_time)) {
        $now = new DateTime();
        $startDateTime = new DateTime($date . ' ' . $start_time);
        // Geef 2 minuten speling
        $now->modify('-2 minutes');
        if ($startDateTime < $now) {
            $errors[] = 'Starttijd kan niet in het verleden liggen';
        }
    }
}

// Extra validatie: check of eindtijd na starttijd is
if (!empty($start_time) && !empty($end_time)) {
    $start = strtotime($start_time);
    $end = strtotime($end_time);
    if ($end <= $start) {
        $errors[] = 'Eindtijd moet later zijn dan starttijd';
    }
}

// Validatie voor herhalende taken
if ($herhaling !== 'eenmalig') {
    if (empty($eind_datum)) {
        $errors[] = 'Eind datum is verplicht voor herhalende taken';
    } else {
        // Check of einddatum niet in verleden is
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        $eindDatumObj = new DateTime($eind_datum);
        $eindDatumObj->setTime(0, 0, 0);
        
        if ($eindDatumObj < $today) {
            $errors[] = 'Einddatum kan niet in het verleden liggen';
        }
        
        // Check of einddatum na startdatum is
        if (!empty($date)) {
            $startDatumObj = new DateTime($date);
            $startDatumObj->setTime(0, 0, 0);
            if ($eindDatumObj < $startDatumObj) {
                $errors[] = 'Einddatum moet later zijn dan startdatum';
            }
        }
    }
}
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    $conn = getDbConnection();
    // Insert task met frequentie en dag/week/maand/jaar velden
    $stmt = $conn->prepare("INSERT INTO tasks (title, description, category_id, is_active, created_at, frequency, start_time, end_time, day, week, month, year) VALUES (?, ?, ?, 1, NOW(), ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $beschrijving, $category, $frequency, $start_time, $end_time, $day, $week, $month, $year]);
    $task_id = $conn->lastInsertId();

    // Bereken welke datums slots moeten krijgen
    $slotDates = [];
    if ($frequency === null) {
        // Eenmalige taak
        $slotDates[] = $date;
    } else {
        // Herhalende taak - genereer slots tot einddatum
        $currentDate = new DateTime($date);
        $endDate = new DateTime($eind_datum);
        
        while ($currentDate <= $endDate) {
            $slotDates[] = $currentDate->format('Y-m-d');
            
            // Verhoog datum op basis van frequentie
            if ($frequency === 'DAILY') {
                $currentDate->modify('+1 day');
            } elseif ($frequency === 'WEEKLY') {
                $currentDate->modify('+1 week');
            } elseif ($frequency === 'MONTHLY') {
                $currentDate->modify('+1 month');
            }
        }
    }

    // Voeg slots toe voor alle berekende datums
    $stmtSlot = $conn->prepare("INSERT INTO task_slots (task_id, slot_date, start_time, end_time, capacity, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $slotIds = [];
    foreach ($slotDates as $slotDate) {
        $stmtSlot->execute([$task_id, $slotDate, $start_time, $end_time, $maxleden ?? 1]);
        $slotIds[] = $conn->lastInsertId();
    }

    // Personeel koppelen aan taak (task_registrations) - OPTIONEEL
    // Admin kan nu taken aanmaken zonder personeel toe te wijzen
    if (!empty($personeel)) {
        $stmtReg = $conn->prepare("INSERT INTO task_registrations (slot_id, user_id) VALUES (?, ?)");
        foreach ($slotIds as $slot_id) {
            foreach ($personeel as $userId) {
                if (!empty($userId)) {
                    $stmtReg->execute([$slot_id, $userId]);
                }
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Taak succesvol opgeslagen!', 'task_id' => $task_id, 'category_id' => $category]);
} catch (PDOException $e) {
    error_log('Taak opslaan fout: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
