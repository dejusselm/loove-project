<?php
session_start();

if (!isset($_SESSION["userId"])) {
    header("Location: login.php");
    exit;
}

include "../controllers/dashboardController.php";
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="style/dashboard.css" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Dashboard</h1>
        <a href="profile.php"><img src="../public/uploads/<?php echo htmlspecialchars($data["avatar"]) ?>"
                style="max-width:50px ;border:0.5rem solid; border-radius: 50%; "></a>
    </header>
    <main>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptates sed dignissimos deleniti nihil ex, iure
            maxime magnam expedita doloremque cupiditate pariatur blanditiis incidunt natus sapiente nulla eos,
            reiciendis asperiores maiores.</p>
    </main>
    <footer>
        <a href=" login.php"><i class="fa-solid fa-arrow-right-from-bracket" style="font-size:50px"></i></a>
    </footer>
</body>

</html>