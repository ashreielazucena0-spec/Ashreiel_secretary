<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="fw-bold mb-1">Reports</h2>
    <p class="text-muted mb-0">Click any card to open the full list behind it. Every report can be printed.</p>
</div>

<h6 class="text-muted text-uppercase small fw-semibold mb-2">Modules</h6>
<div class="row g-3 mb-4">
    <?php foreach ($counts as $key => $c) : ?>
        <div class="col-xl-3 col-md-6">
            <a href="<?= base_url('barangay/reports/' . $key) ?>" class="stat-card-link">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi <?= esc($c['icon']) ?>"></i></div>
                    <div>
                        <small><?= esc($c['title']) ?></small>
                        <h3><?= number_format($c['count']) ?></h3>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<h6 class="text-muted text-uppercase small fw-semibold mb-2">Residents by category</h6>
<div class="row g-3 mb-4">
    <?php foreach ($groups as $key => $g) : ?>
        <div class="col-xl-3 col-md-6">
            <a href="<?= base_url('barangay/reports/residents?filter=' . $key) ?>" class="stat-card-link">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi <?= esc($g['icon']) ?>"></i></div>
                    <div>
                        <small><?= esc($g['title']) ?></small>
                        <h3><?= number_format($g['count']) ?></h3>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="content-card">
    <div class="card-header-custom"><h5>Registered Residents by Purok</h5></div>
    <p class="text-muted small">Click a purok to see the people living in it.</p>
    <?php if (empty($purokCounts)) : ?>
        <p class="text-muted mb-0">No resident records yet.</p>
    <?php else :
        $max = max($purokCounts) ?: 1; ?>
        <?php foreach ($purokCounts as $purok => $count) :
            $href = $purok === 'Unspecified' ? base_url('barangay/reports/residents') : base_url('barangay/reports/residents?purok=' . rawurlencode($purok)); ?>
            <a href="<?= $href ?>" class="d-block text-decoration-none text-reset mb-3 purok-row">
                <div class="d-flex justify-content-between mb-1">
                    <span><?= esc($purok) ?> <i class="bi bi-box-arrow-up-right small text-muted"></i></span>
                    <strong><?= (int) $count ?></strong>
                </div>
                <div class="progress" style="height:8px">
                    <div class="progress-bar bg-navy" style="width: <?= round($count / $max * 100) ?>%"></div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
