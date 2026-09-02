<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle ?? 'Barangay Sumadat') ?> — Barangay Sumadat</title>

    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/barangay.css') ?>">
</head>
<body>

<div class="d-flex">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">

        <div class="brand">
            <div class="brand-logo"><img src="<?= base_url('images/logo.jpg') ?>" alt="Barangay Sumadat logo"></div>
            <div>
                <h6 class="mb-0">Barangay Sumadat</h6>
                <small>Dumalinao, Zamboanga del Sur</small>
            </div>
        </div>

        <div class="sidebar-menu">

            <small class="menu-title">MAIN MENU</small>

            <a href="<?= base_url('barangay/dashboard') ?>" class="menu-item <?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-grid"></i><span>Dashboard</span>
            </a>
            <a href="<?= base_url('barangay/residents') ?>" class="menu-item <?= ($active ?? '') === 'residents' ? 'active' : '' ?>">
                <i class="bi bi-people"></i><span>Residents</span>
            </a>
            <a href="<?= base_url('barangay/households') ?>" class="menu-item <?= ($active ?? '') === 'households' ? 'active' : '' ?>">
                <i class="bi bi-house"></i><span>Households</span>
            </a>
            <a href="<?= base_url('barangay/certificates') ?>" class="menu-item <?= ($active ?? '') === 'certificates' ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text"></i><span>Certificates</span>
            </a>
            <a href="<?= base_url('barangay/requests') ?>" class="menu-item <?= ($active ?? '') === 'requests' ? 'active' : '' ?>">
                <i class="bi bi-card-checklist"></i><span>Requests</span>
            </a>
            <a href="<?= base_url('barangay/cases') ?>" class="menu-item <?= ($active ?? '') === 'cases' ? 'active' : '' ?>">
                <i class="bi bi-shield-exclamation"></i><span>Complaints / Blotter</span>
            </a>
            <a href="<?= base_url('barangay/meetings') ?>" class="menu-item <?= ($active ?? '') === 'meetings' ? 'active' : '' ?>">
                <i class="bi bi-calendar-event"></i><span>Meetings &amp; Events</span>
            </a>

            <small class="menu-title mt-4">MANAGEMENT</small>

            <a href="<?= base_url('barangay/documents') ?>" class="menu-item <?= ($active ?? '') === 'documents' ? 'active' : '' ?>">
                <i class="bi bi-journal-text"></i><span>Ordinances &amp; Resolutions</span>
            </a>
            <a href="<?= base_url('barangay/payments') ?>" class="menu-item <?= ($active ?? '') === 'payments' ? 'active' : '' ?>">
                <i class="bi bi-cash-stack"></i><span>Payments</span>
            </a>
            <a href="<?= base_url('barangay/reports') ?>" class="menu-item <?= ($active ?? '') === 'reports' ? 'active' : '' ?>">
                <i class="bi bi-bar-chart"></i><span>Reports</span>
            </a>
            <a href="<?= base_url('barangay/officials') ?>" class="menu-item <?= ($active ?? '') === 'officials' ? 'active' : '' ?>">
                <i class="bi bi-person-badge"></i><span>Officials</span>
            </a>
            <a href="<?= base_url('barangay/users') ?>" class="menu-item <?= ($active ?? '') === 'users' ? 'active' : '' ?>">
                <i class="bi bi-person-lock"></i><span>User Management</span>
            </a>

            <small class="menu-title mt-4">SYSTEM</small>

            <a href="<?= base_url('barangay/activity-logs') ?>" class="menu-item <?= ($active ?? '') === 'activity_logs' ? 'active' : '' ?>">
                <i class="bi bi-clock-history"></i><span>Activity Logs</span>
            </a>
            <a href="<?= base_url('barangay/backup') ?>" class="menu-item <?= ($active ?? '') === 'backup' ? 'active' : '' ?>">
                <i class="bi bi-database"></i><span>Backup &amp; Restore</span>
            </a>
            <a href="<?= base_url('logout') ?>" class="menu-item">
                <i class="bi bi-box-arrow-right"></i><span>Log Out</span>
            </a>

        </div>

    </aside>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- MAIN CONTENT -->
    <main class="main-content w-100">

        <nav class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" id="sidebarToggle"><i class="bi bi-list fs-5"></i></button>
                <div>
                    <h6 class="mb-0"><?= esc($pageTitle ?? 'Dashboard') ?></h6>
                    <small class="text-muted">Barangay Secretary Management System</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="user-profile">
                    <div class="avatar"><?= esc(mb_strtoupper(mb_substr(session()->get('name') ?? 'A', 0, 1))) ?></div>
                    <div class="d-none d-md-block">
                        <strong><?= esc(session()->get('name') ?? 'Administrator') ?></strong>
                        <small><?= esc(session()->get('role') ?? 'Barangay Secretary') ?></small>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid page-content">

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>

        </div>

    </main>

</div>

<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('js/barangay.js') ?>"></script>

</body>
</html>
