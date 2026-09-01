<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barangay Sumadat — Secretary System</title>
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/barangay.css') ?>">
</head>
<body class="auth-body">

<div class="auth-wrap">
    <div class="auth-card">

        <div class="auth-head">
            <div class="auth-logo"><img src="<?= base_url('images/logo.jpg') ?>" alt="Barangay Sumadat logo"></div>
            <h1>Barangay Sumadat</h1>
            <p>Dumalinao, Zamboanga del Sur</p>
        </div>

        <?php $tab = session()->getFlashdata('activeTab') ?? ($activeTab ?? 'login'); ?>

        <ul class="nav nav-pills auth-tabs mb-3" role="tablist">
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link w-100 <?= $tab === 'login' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#loginPane" type="button">Log In</button>
            </li>
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link w-100 <?= $tab === 'register' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#registerPane" type="button">Create Account</button>
            </li>
        </ul>

        <div class="tab-content">

            <div class="tab-pane fade <?= $tab === 'login' ? 'show active' : '' ?>" id="loginPane">

                <?php if (session()->getFlashdata('loginError')) : ?>
                    <div class="alert alert-danger py-2"><?= esc(session()->getFlashdata('loginError')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('registerSuccess')) : ?>
                    <div class="alert alert-success py-2"><?= esc(session()->getFlashdata('registerSuccess')) ?></div>
                <?php endif; ?>

                <form method="post" action="<?= site_url('login') ?>" class="auth-form">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input class="form-control" name="username" required autocomplete="username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input class="form-control" name="password" type="password" required autocomplete="current-password">
                    </div>
                    <button class="btn btn-gold w-100" type="submit">Log In</button>
                    <div class="demo-hint"><b></b><b></b></div>
                </form>

            </div>

            <div class="tab-pane fade <?= $tab === 'register' ? 'show active' : '' ?>" id="registerPane">

                <?php if (session()->getFlashdata('registerError')) : ?>
                    <div class="alert alert-danger py-2"><?= esc(session()->getFlashdata('registerError')) ?></div>
                <?php endif; ?>

                <form method="post" action="<?= site_url('register') ?>" class="auth-form">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input class="form-control" name="username" required autocomplete="username">
                    </div>
                
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input class="form-control" name="password" type="password" required minlength="6" autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input class="form-control" name="password2" type="password" required minlength="6" autocomplete="new-password">
                    </div>
                    <button class="btn btn-gold w-100" type="submit">Create Account</button>
                </form>

            </div>

        </div>

    </div>
</div>

<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
