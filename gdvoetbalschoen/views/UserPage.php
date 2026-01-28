<?php
    session_start();
    
    include("../phpcode/db_connection.php");
    $conn = getDbConnection();    

    if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
    }
    $user = $_SESSION['user'];
    $UserID = $user['id'];

    $sqla = "SELECT slot_id FROM task_registrations WHERE user_id = :user_id LIMIT 1";

    $stmta = $conn->prepare($sqla);
    $stmta->execute([':user_id' => $UserID]);
    $Getregistrations = $stmta->fetch(PDO::FETCH_ASSOC);
    $slotID = (int)$Getregistrations['slot_id'];

    $sqlb = "SELECT task_id FROM task_slots WHERE slot_id = :slot_id LIMIT 1";

    $stmtb = $conn->prepare($sqlb);
    $stmtb->execute([':slot_id' => $slotID]);
    $GetSlots = $stmtb->fetch(PDO::FETCH_ASSOC);
    $taskID = (int)$GetSlots['task_id'];

    $sql = "SELECT * FROM tasks WHERE task_id = :task_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':task_id' => $taskID]);
    $AllTasks = $stmt->fetchall(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/agenda.css">
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
            </nav>
        </div>
    </header>

    <div class="MainArea">
        <?php
            foreach($AllTasks as $Task){
                echo $Task["title"]; echo "<br>";
            }
        ?>

    </div>
</body>
</html>