<?php

    session_start();

    include("../phpcode/db_connection.php");
    $conn = getDbConnection();    

    if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
    }

    $isAdmin = isset($_SESSION['user']['role_id']) && $_SESSION['user']['role_id'] == 2;
    if (!$isAdmin) {
        header('Location: ../index.php');
        exit();
    }

    $sqlGetAllTasks = "SELECT DISTINCT t.* FROM tasks t 
                    INNER JOIN task_slots ts ON t.task_id = ts.task_id 
                    ORDER BY t.task_id";
    $stmtTasks = $conn->prepare($sqlGetAllTasks);
    $stmtTasks->execute();
    $Tasks = $stmtTasks->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/TaskManager.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="../assets/images/fc_team_zonder_plan.png" alt="FC Team zonder plan logo">
            </div>
            <nav>
                <a href="../index.php" class="nav-icon home-icon" title="Home">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                    </svg>
                </a>
                <a href="agenda.php" class="nav-icon calendar-icon" title="Kalender">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z" />
                    </svg>
                </a>
                <a href="admin_dashboard.php" class="nav-icon admin-icon" title="Admin" style="color:#6b5b95;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="32" height="32">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" />
                    </svg>
                </a>
            </nav>
        </div>
    </header>

    <div class="MainArea">
        <div class="TaskBlock">
            <div class="TaskArea">
                <?php
                foreach($Tasks as $Task){
                if($Task["frequency"] == null){
                    $Task["frequency"] = "ONCE";
                }
                echo "<div class='TaskElement'>" .
                        "<div class='Title'><h3>Title</h3>" . htmlspecialchars($Task["title"]) . "</div>" .
                        "<div class='Date'><h3>Date</h3>". htmlspecialchars($Task["day"]) . "-" . htmlspecialchars($Task["month"]) . "-" . htmlspecialchars($Task["year"]) . "</div>" .
                        "<div class='Frequency'><h3>Frequency</h3>". htmlspecialchars($Task["frequency"]) . "</div>" .
                        "<button name='EditTask' value="  . htmlspecialchars($Task["task_id"]) .  " class='Edit'>Edit</button>" .
                    "</div>"
                    ;
                }
                ?>
            </div>
        </div>
    </div>

    <div id="EditBlock">
        <div class="CloseBar">
            <button id="CloseField">X</button>
        </div>
        <div class="EditArea">
            <input name="Title" id="Title">
            <input type="time" name="TimeStart" id="TimeStart">
            <input type="time" name="TimeEnd" id="TimeEnd">
            <select name="categorie" id="Category" required>
                <option value="">Selecteer categorie...</option>
            </select>
            <div class="UpdateBar">
                <button id="UpdateTask">Update</button>
                <button id="DeleteTask">Delete</button>
            </div>
            
        </div>
    </div>

    <script src="../js/TaskManager.js"></script>
</body>
</html>