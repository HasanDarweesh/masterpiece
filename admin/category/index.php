<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px 20px;
            color: white;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php?page=dashboard">
                                Dashboard <span class="sr-only">(current)</span>
                            </a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" href="index.php?page=admin_management">
                                Admin Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=category">
                                Category Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=product">
                                Product Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=customer">
                                Customer Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=order">
                                Order Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=coupon">
                                Coupon Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=report">
                                Reports Management
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="content">
                    <?php
                        $page = $_GET['page'] ?? 'dashboard';
                        switch ($page) {
                           
                            case 'add_category':
                                include 'add_category.php';
                                break;
                            case 'edit_category':
                                include 'edit_category.php';
                                break;
                            case 'delete_category':
                                include 'delete_category.php';
                                break;
                            case 'category':
                                include 'category.php';
                                break;
                            case 'product':
                                include '../php_project_group_2/admin/product/product.php';
                                break;
                            case 'report':
                                include 'report.php';
                                break;
                            
                        }
                    ?>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="holder.js"></script>
</body>
</html>
