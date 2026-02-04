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
    
    <div class="NavBar">
        <a href="admin_dashboard.php">Ga naar admin</a>
    </div>
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