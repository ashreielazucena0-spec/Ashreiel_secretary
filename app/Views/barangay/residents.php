<?php
/**
 * @var array{
 *     title: string,
 *     icon: string,
 *     idPrefix: string,
 *     fields: array<string,array{label:string,type:string,required?:bool,options?:array<int|string,string>}>,
 *     columns: array<int,string>
 * } $schema
 * @var array<int,array<string,mixed>> $rows
 * @var string                         $module
 * @var string                         $search
 * @var array<string,string>           $residentLookup
 */
?>
<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php
$residentFields = ['resident', 'head', 'complainant', 'respondent'];
$fmtVal = function ($key, $val) use ($residentFields, $residentLookup) {
    if (in_array($key, $residentFields, true) && isset($residentLookup[$val])) {
        return $residentLookup[$val];
    }
    return $val;
};
$statusFields = ['status'];
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi <?= esc($schema['icon']) ?> me-2"></i><?= esc($schema['title']) ?></h2>
        <p class="text-muted mb-0"><?= count($rows) ?> record<?= count($rows) === 1 ? '' : 's' ?></p>
    </div>
    <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg me-1"></i> Add New
    </button>
</div>

<div class="content-card">

    <form method="get" class="mb-3">
        <div class="input-group search-box">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" name="q" value="<?= esc($search) ?>" placeholder="Search <?= esc(strtolower($schema['title'])) ?>...">
            <?php if ($search !== '') : ?>
                <a href="<?= base_url('barangay/' . $module) ?>" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (empty($rows)) : ?>
        <div class="empty-state">
            <i class="bi <?= esc($schema['icon']) ?>"></i>
            <p>No records found.</p>
        </div>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <?php foreach ($schema['columns'] as $col) : ?>
                            <th><?= esc($schema['columnLabels'][$col] ?? $schema['fields'][$col]['label'] ?? ucfirst($col)) ?></th>
                        <?php endforeach; ?>
                        <th>Login</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row) : ?>
                    <tr>
                        <td><span class="text-muted small"><?= esc($row['id']) ?></span></td>
                        <td>
                            <?php if (! empty($row['photo'])) : ?>
                                <img src="<?= base_url('files/resident/' . $row['id']) ?>?v=<?= esc(substr(md5($row['photo']), 0, 8)) ?>"
                                     alt="" loading="lazy" style="width:42px;height:42px;object-fit:cover;border-radius:50%;">
                            <?php else : ?>
                                <i class="bi bi-person-circle text-muted" style="font-size:2.3rem;line-height:1;"></i>
                            <?php endif; ?>
                        </td>
                        <?php foreach ($schema['columns'] as $col) :
                            $val = $fmtVal($col, $row[$col] ?? ''); ?>
                            <td>
                                <?php if (in_array($col, $statusFields, true) && $val !== '') : ?>
                                    <span class="badge-status status-<?= esc(strtolower((string) $val)) ?>"><?= esc($val) ?></span>
                                <?php elseif ($col === 'amount' && $val !== '') : ?>
                                    ₱<?= esc(number_format((float) $val, 2)) ?>
                                <?php else : ?>
                                    <?= esc((string) $val) ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td>
                            <?php if (isset($accounts[$row['id']])) : ?>
                                <code><?= esc($accounts[$row['id']]) ?></code>
                            <?php else : ?>
                                <span class="text-muted small">none yet</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <form method="post" action="<?= base_url('barangay/residents/reset-login/' . $row['id']) ?>" class="d-inline"
                                  onsubmit="return confirm('Create a new temporary password for this resident?');">
                                <button class="btn btn-sm btn-outline-secondary" title="Create / reset login"><i class="bi bi-key"></i></button>
                            </form>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal<?= esc(md5($row['id'])) ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#delModal<?= esc(md5($row['id'])) ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<script>window.HOUSEHOLDS = <?= json_encode($houseHints ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;</script>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" action="<?= base_url('barangay/' . $module . '/store') ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Add <?= esc(rtrim($schema['title'], 's')) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?= view('barangay/_resident_form', ['schema' => $schema, 'row' => null]) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-navy">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT + DELETE MODALS (one pair per row) -->
<?php foreach ($rows as $row) : ?>

    <div class="modal fade" id="editModal<?= esc(md5($row['id'])) ?>" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data" action="<?= base_url('barangay/' . $module . '/update/' . $row['id']) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit <?= esc(rtrim($schema['title'], 's')) ?> — <?= esc($row['id']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <?= view('barangay/_resident_form', ['schema' => $schema, 'row' => $row]) ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-navy">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delModal<?= esc(md5($row['id'])) ?>" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form method="post" action="<?= base_url('barangay/' . $module . '/delete/' . $row['id']) ?>">
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>
                        <p class="mb-0">Delete record <strong><?= esc($row['id']) ?></strong>? This can't be undone.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php endforeach; ?>

<?= $this->endSection() ?>