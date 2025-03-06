<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <style>
        /* Navbar Styling */
        .navbar {
            background-color: #000 !important;
            border-color: #000 !important;
        }

        .navbar-dark .navbar-brand,
        .navbar-dark .navbar-nav .nav-link {
            color: #fff !important;
        }

        .navbar-dark .navbar-nav .nav-link.active {
            background-color: #000 !important;
            border-color: #000 !important;
        }
    </style>

    <div class="container-fluid">
        <a class="navbar-brand" href="../home/index.php">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <!-- The logout link -->
                    <a class="nav-link" href="../logout/logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
