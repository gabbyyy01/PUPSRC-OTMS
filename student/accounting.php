<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounting Office - Welcome</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/fe96d845ef.js" crossorigin="anonymous"></script>
    <script src="../node_modules/jquery/dist/jquery.min.js"></script>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <?php
            $office_name = "Accounting Office";
            include "navbar.php";
            include "../breadcrumb.php";
        ?>
        <div class="container-fluid accountingbanner header">
            <a href="#" class="header-btn btn btn-primary position-absolute p-3 m-2 bottom-0 start-0">Generate Inquiry</a>
            <?php
            $breadcrumbItems = [
                ['text' => 'Accounting Office', 'active' => true],
            ];

            echo generateBreadcrumb($breadcrumbItems, false);
            ?>
            <h1 class="display-1 header-text text-center text-light">Accounting Office</h1>
            <p class="header-text text-center text-light">Choose from one of the services below to get started</p>
        </div>
        <div class="container-fluid p-2 d-flex flex-wrap flex-column justify-content-center gap-2 text-center">
            <a href="accounting/payment1.php" class="btn btn-primary d-block text-decoration-none bg-maroon text-light p-4 rounded-0">
                <h2>PAYMENTS</h2>
                <p>Simplify your payments for campus documents</p>
            </a>
            <a href="accounting/offsetting1.php" class="btn btn-primary d-block text-decoration-none bg-maroon text-light p-4 rounded-0">
            <h2>OFFSETTING</h2>
                <p>Balance your campus accounts</p>
            </a>
            <a href="transactions.php" class="btn btn-primary d-block text-decoration-none bg-maroon text-light p-4 rounded-0">
            <h2>TRANSACTION HISTORY</h2>
                <p>Access your past and current transactions</p>
            </a>
        </div>
        <div class="push"></div>
    </div>
    <?php include '../footer.php'; ?>
    <script>
        $(document).ready(function(){
            $('.dropdown-submenu a.dropdown-toggle').on("click", function(e){
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
            });
        });
    </script>
    <script src="../dark_mode.js"></script>
    <!--End of content-->
</body>
</html>