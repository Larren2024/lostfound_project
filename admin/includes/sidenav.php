<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <div class="sb-sidenav-menu-heading">Admin</div>

                <a class="nav-link" href="dashboard.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>

                <div class="sb-sidenav-menu-heading">Interface</div>

                <a class="nav-link" href="users.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Users
                </a>


                <div class="sb-sidenav-menu-heading">Lost Items Management</div>
                
                <a class="nav-link collapsed" href="lost-items.php" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                <div class="sb-nav-link-icon"><i class="fas fa-box-open"></i></div>
                Lost Items
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="#">Manage Lost Items</a>
                    <a class="nav-link" href="#">View Lost Items</a>
                </nav>
                </div>


                <div class="sb-sidenav-menu-heading">Found Items Management</div>
                
                <a class="nav-link collapsed" href="found-items.php" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                <div class="sb-nav-link-icon"><i class="fas fa-hand-holding"></i></div>
                Found Items
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="#">Manage Found Items</a>
                    <a class="nav-link" href="#">View Found Items</a>
                </nav>
                </div>


                <div class="sb-sidenav-menu-heading">Claims Management</div>
                
                <a class="nav-link collapsed" href="claims.php" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                Claims
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="#">Manage Claims</a>
                    <a class="nav-link" href="#">View Claims</a>
                </nav>
                </div>

                <a class="nav-link" href="#">
                    <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                    Notifications
                </a>

                <a class="nav-link" href="#">
                    <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                    Audit Logs
                </a>

                <a class="nav-link text-danger" href="../logout.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                    Logout
                </a>

            </div>
        </div>
    </nav>
</div>
