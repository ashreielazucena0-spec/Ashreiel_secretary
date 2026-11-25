<?php
/**
 * Body of the Add / Edit resident modal.
 *
 * @var array      $schema  residents schema
 * @var array|null $row     the resident being edited (null when adding)
 */
$row    = $row ?? null;
$f      = $schema['fields'];
$val    = fn ($k) => $row[$k] ?? '';
$hhId   = (string) ($row['household_id'] ?? '');

// text / date / select field from the schema, as a grid cell
$cell = function (string $key, string $cols = 'col-12 col-md-6 col-lg-3') use ($f, $val) {
    $field = $f[$key];
    ob_start(); ?>
    <div class="<?= $cols ?>">
        <label class="form-label"><?= esc($field['label']) ?><?= ! empty($field['required']) ? ' <span class="text-danger">*</span>' : '' ?></label>
        <?php if ($field['type'] === 'select') : ?>
            <select class="form-select" name="<?= esc($key) ?>" <?= ! empty($field['required']) ? 'required' : '' ?>>
                <option value="">Select...</option>
                <?php foreach ($field['options'] as $optVal => $optLabel) :
                    $ov = is_int($optVal) ? $optLabel : $optVal; ?>
                    <option value="<?= esc($ov) ?>" <?= (string) $val($key) === (string) $ov ? 'selected' : '' ?>><?= esc($optLabel) ?></option>
                <?php endforeach; ?>
            </select>
        <?php else : ?>
            <input class="form-control" type="<?= esc($field['type']) ?>" name="<?= esc($key) ?>"
                   value="<?= esc($val($key)) ?>" <?= ! empty($field['required']) ? 'required' : '' ?>>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
};
?>

<!-- 1. PERSONAL INFORMATION -->
<div class="rf-section">
    <div class="rf-title"><i class="bi bi-person-vcard"></i> Personal information</div>
    <div class="row g-3">
        <?= $cell('first') ?>
        <?= $cell('middle') ?>
        <?= $cell('last') ?>
        <?= $cell('birth') ?>
        <?= $cell('sex') ?>
        <?= $cell('civil') ?>
        <?= $cell('contact') ?>
        <?= $cell('occupation') ?>
    </div>
</div>

<!-- 2. HOUSEHOLD -->
<div class="rf-section">
    <div class="rf-title"><i class="bi bi-house-door"></i> Household</div>

    <div class="js-hh-picker" data-selected="<?= esc($hhId) ?>" data-required="<?= $row ? '0' : '1' ?>">
        <input type="hidden" name="hh_mode" value="existing">

        <div class="hh-seg" role="tablist" aria-label="Household">
            <button type="button" class="hh-seg-btn active" data-mode="existing" role="tab">
                <i class="bi bi-house-check"></i>
                <span><strong>Registered house</strong><small>Pick a house that is already on file</small></span>
            </button>
            <button type="button" class="hh-seg-btn" data-mode="new" role="tab">
                <i class="bi bi-house-add"></i>
                <span><strong>New household</strong><small>Register the house on the spot</small></span>
            </button>
        </div>

        <!-- existing house -->
        <div class="hh-panel js-hh-existing">
            <div class="row g-3 align-items-start">
                <div class="col-12 col-lg-6">
                    <label class="form-label">Household (head of the house) <span class="text-danger">*</span></label>
                    <select class="form-select" name="household_id" data-hh-select>
                        <option value="">Search by household head, street or purok...</option>
                    </select>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="hh-summary js-hh-summary" data-empty="1">
                        <div class="hh-summary-empty"><i class="bi bi-info-circle"></i> Choose a house to see its details. The resident's Purok follows the house.</div>
                        <dl class="hh-summary-body mb-0">
                            <div><dt>Household head</dt><dd data-f="head"></dd></div>
                            <div><dt>House No.</dt><dd data-f="no"></dd></div>
                            <div><dt>Purok</dt><dd data-f="purok"></dd></div>
                            <div><dt>Street</dt><dd data-f="street"></dd></div>
                            <div class="js-hh-s-bld"><dt>Building</dt><dd data-f="building"></dd></div>
                            <div><dt>Members</dt><dd data-f="members"></dd></div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- new household -->
        <div class="hh-panel js-hh-new d-none">
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label">House No. <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" name="house_no" placeholder="e.g. 105" disabled>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label">Purok <span class="text-danger">*</span></label>
                    <select class="form-select" name="purok" data-hh-purok disabled>
                        <option value="">Select...</option>
                        <?php foreach ($f['purok']['options'] as $optVal => $optLabel) :
                            $ov = is_int($optVal) ? $optLabel : $optVal; ?>
                            <option value="<?= esc($ov) ?>"><?= esc($optLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label">Street Name</label>
                    <input class="form-control" type="text" name="hh_street" disabled>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label">Living in a building?</label>
                    <select class="form-select" name="hh_in_building" data-searchable="false" disabled>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-lg-3 js-hh-bld d-none">
                    <label class="form-label">Building Name</label>
                    <input class="form-control" type="text" name="hh_building" disabled>
                </div>
                <div class="col-12 col-md-6 col-lg-3 js-hh-bld d-none">
                    <label class="form-label">Floor</label>
                    <input class="form-control" type="text" name="hh_floor" disabled>
                </div>
            </div>
            <div class="form-text mt-2"><i class="bi bi-person-check me-1"></i>This resident will be recorded as the head of the new household.</div>
        </div>
    </div>
</div>

<!-- 3. STATUS -->
<div class="rf-section">
    <div class="rf-title"><i class="bi bi-tags"></i> Status</div>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach (['voter', 'senior', 'pwd', 'infant'] as $k) :
            $on = $row && (string) $val($k) === (string) $f[$k]['checked']; ?>
            <label class="rf-chip" <?= in_array($k, ['senior', 'infant'], true) ? 'data-age-flag="' . $k . '"' : '' ?>>
                <input type="checkbox" name="<?= $k ?>" value="1" <?= $on ? 'checked' : '' ?>>
                <span><i class="bi bi-circle rf-off"></i><i class="bi bi-check-circle-fill rf-on"></i><?= esc($f[$k]['label']) ?></span>
            </label>
        <?php endforeach; ?>
    </div>
</div>

<!-- 4. PHOTO -->
<div class="rf-section mb-0">
    <div class="row g-3">
        <?= view('barangay/_photo_field', $row ? ['residentId' => $row['id'], 'current' => $row['photo'] ?? null] : []) ?>
    </div>
</div>