<?php
/**
 * Migratie script voor bestaande herhalende taken
 * Dit script genereert slots voor bestaande DAILY/WEEKLY/MONTHLY taken
 * die nog geen einddatum hebben (oude systeem).
 * 
 * BELANGRIJK: Dit script moet één keer handmatig uitgevoerd worden
 * na het implementeren van de einddatum functionaliteit.
 */

session_start();
require_once '../../config/db_connection.php';

// Check of gebruiker admin is
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    http_response_code(403);
    die('Alleen admins kunnen dit migratie script uitvoeren');
}

try {
    $conn = getDbConnection();
    
    // Haal alle herhalende taken op die mogelijk geen complete slots hebben
    $stmt = $conn->prepare("
        SELECT t.task_id, t.title, t.frequency, t.start_time, t.end_time,
               MIN(ts.slot_date) as first_slot_date,
               MAX(ts.slot_date) as last_slot_date,
               COUNT(ts.slot_id) as slot_count
        FROM tasks t
        LEFT JOIN task_slots ts ON ts.task_id = t.task_id
        WHERE t.frequency IN ('DAILY', 'WEEKLY', 'MONTHLY') AND t.is_active = 1
        GROUP BY t.task_id
    ");
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Migratie Rapport - Herhalende Taken</h2>";
    echo "<p>Gevonden taken: " . count($tasks) . "</p>";
    
    foreach ($tasks as $task) {
        echo "<hr>";
        echo "<h3>{$task['title']} (ID: {$task['task_id']})</h3>";
        echo "<p>Frequentie: {$task['frequency']}</p>";
        echo "<p>Eerste slot: {$task['first_slot_date']}</p>";
        echo "<p>Laatste slot: {$task['last_slot_date']}</p>";
        echo "<p>Aantal slots: {$task['slot_count']}</p>";
        
        // Voor deze migratie: genereer slots voor de komende 3 maanden als einddatum
        if ($task['first_slot_date'] && $task['slot_count'] > 0) {
            $startDate = new DateTime($task['first_slot_date']);
            $endDate = new DateTime();
            $endDate->modify('+3 months'); // Default: 3 maanden vooruit
            
            echo "<p style='color: blue;'>Genereer extra slots tot: " . $endDate->format('Y-m-d') . "</p>";
            
            // Haal bestaande slot datums op
            $stmtExisting = $conn->prepare("SELECT slot_date FROM task_slots WHERE task_id = ?");
            $stmtExisting->execute([$task['task_id']]);
            $existingDates = $stmtExisting->fetchAll(PDO::FETCH_COLUMN);
            
            // Haal capacity en start/end tijd op van eerste slot
            $stmtFirstSlot = $conn->prepare("SELECT capacity FROM task_slots WHERE task_id = ? LIMIT 1");
            $stmtFirstSlot->execute([$task['task_id']]);
            $firstSlot = $stmtFirstSlot->fetch(PDO::FETCH_ASSOC);
            $capacity = $firstSlot['capacity'] ?? 1;
            
            $currentDate = clone $startDate;
            $newSlotsCreated = 0;
            $stmtInsert = $conn->prepare("INSERT INTO task_slots (task_id, slot_date, start_time, end_time, capacity, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            
            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                
                // Alleen toevoegen als deze datum nog niet bestaat
                if (!in_array($dateStr, $existingDates)) {
                    $stmtInsert->execute([
                        $task['task_id'],
                        $dateStr,
                        $task['start_time'],
                        $task['end_time'],
                        $capacity
                    ]);
                    $newSlotsCreated++;
                }
                
                // Verhoog datum op basis van frequentie
                if ($task['frequency'] === 'DAILY') {
                    $currentDate->modify('+1 day');
                } elseif ($task['frequency'] === 'WEEKLY') {
                    $currentDate->modify('+1 week');
                } elseif ($task['frequency'] === 'MONTHLY') {
                    $currentDate->modify('+1 month');
                }
            }
            
            echo "<p style='color: green;'>✓ {$newSlotsCreated} nieuwe slots aangemaakt</p>";
        } else {
            echo "<p style='color: orange;'>Geen eerste slot gevonden - handmatige actie vereist</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3 style='color: green;'>Migratie voltooid!</h3>";
    echo "<p><a href='../../views/agenda.php'>Terug naar agenda</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Fout bij migratie: " . htmlspecialchars($e->getMessage()) . "</p>";
}
