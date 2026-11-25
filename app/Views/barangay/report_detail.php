<?php
/**
 * Printable report page.
 * @var string $title
 * @var array<int,string> $subtitle
 * @var array<string,string> $columns   key => heading
 * @var array<int,array<string,mixed>> $rows
 * @var array|null $total               [label, value]
 * @var callable|null $rowLink          fn(row) => [url, label]
 * @var array<int,string> $filters
 * @var array<string,string> $values
 */
$backUrl = $backUrl ?? base_url('barangay/reports');
$office  = 'Barangay Sumadat — Dumalinao, Zamboanga del Sur';
?>
<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 no-print">
    <div>
        <a href="<?= esc($backUrl) ?>" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Back</a>
        <h2 class="fw-bold mb-0"><?= esc($title) ?></h2>
    </div>
    <button class="btn btn-navy" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print</button>
</div>

<?php if (! empty($filters)) : ?>
    <form method="get" class="content-card mb-3 no-print">
        <div class="row g-2 align-items-end">
            <?php if (in_array('purok', $filters, true)) : ?>
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Purok</label>
                    <select name="purok" class="form-select form-select-sm" data-searchable="false">
                        <option value="">All puroks</option>
                        <?php foreach ($puroks as $p) : ?>
                            <option value="<?= esc($p) ?>" <?= $values['purok'] === $p ? 'selected' : '' ?>><?= esc($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <?php if (in_array('filter', $filters, true)) : ?>
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Show</label>
                    <select name="filter" class="form-select form-select-sm" data-searchable="false">
                        <option value="">Everyone</option>
                        <?php foreach ($groupOptions as $k => $label) : ?>
                            <option value="<?= esc($k) ?>" <?= $values['filter'] === $k ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <?php if (in_array('dates', $filters, true)) : ?>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">From</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="<?= esc($values['from']) ?>">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1">To</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="<?= esc($values['to']) ?>">
                </div>
            <?php endif; ?>
            <?php if (in_array('status', $filters, true)) : ?>
                <div class="col-12 col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm" data-searchable="false">
                        <option value="">All</option>
                        <?php foreach ($statuses as $st) : ?>
                            <option value="<?= esc($st) ?>" <?= $values['status'] === $st ? 'selected' : '' ?>><?= esc($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <?php if (in_array('type', $filters, true)) : ?>
                <div class="col-12 col-md-3">
                    <label class="form-label small mb-1">Type</label>
                    <select name="type" class="form-select form-select-sm" data-searchable="false">
                        <option value="">All</option>
                        <?php foreach ($types as $t) : ?>
                            <option value="<?= esc($t) ?>" <?= $values['type'] === $t ? 'selected' : '' ?>><?= esc($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-auto">
                <button class="btn btn-sm btn-navy">Apply</button>
                <a href="<?= base_url('barangay/reports/' . $module) ?>" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </div>
    </form>
<?php endif; ?>

<div class="content-card report-sheet">

    <!-- letterhead: visible on screen and on paper -->
    <div class="report-head text-center mb-3">
        <img src="<?= base_url('images/logo.jpg') ?>" alt="" class="report-logo">
        <div class="fw-bold fs-5">Barangay Sumadat</div>
        <div class="small text-muted">Dumalinao, Zamboanga del Sur</div>
        <h4 class="fw-bold mt-3 mb-1"><?= esc($title) ?></h4>
        <?php if (! empty($subtitle)) : ?><div class="small"><?= esc(implode(' · ', $subtitle)) ?></div><?php endif; ?>
        <div class="small text-muted">Printed <?= esc(date('F j, Y g:i A')) ?></div>
    </div>

    <div class="d-flex justify-content-between mb-2 small">
        <span><strong><?= count($rows) ?></strong> record<?= count($rows) === 1 ? '' : 's' ?></span>
        <?php if ($total) : ?><span><?= esc($total[0]) ?>: <strong><?= esc($total[1]) ?></strong></span><?php endif; ?>
    </div>

    <?php if (empty($rows)) : ?>
        <div class="empty-state"><i class="bi bi-inbox"></i><p>No records found.</p></div>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle report-table">
                <thead>
                    <tr>
                        <th style="width:42px">#</th>
                        <?php foreach ($columns as $label) : ?><th><?= esc($label) ?></th><?php endforeach; ?>
                        <?php if ($rowLink) : ?><th class="no-print"></th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $i => $row) : ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <?php foreach ($columns as $key => $label) : ?>
                            <td><?= esc((string) ($row[$key] ?? '')) ?></td>
                        <?php endforeach; ?>
                        <?php if ($rowLink) : [$url, $linkLabel] = $rowLink($row); ?>
                            <td class="no-print text-nowrap"><a href="<?= esc($url) ?>" class="btn btn-sm btn-outline-navy"><?= esc($linkLabel) ?></a></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="report-sign d-none">
        <div class="mt-5 d-flex justify-content-between small">
            <span>Prepared by: <strong><?= esc(session()->get('name') ?? '') ?></strong></span>
            <span>Noted by: ______________________ Punong Barangay</span>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
