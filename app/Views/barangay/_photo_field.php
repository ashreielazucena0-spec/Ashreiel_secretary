<?php
/**
 * Photo picker used by the Add / Edit resident forms.
 * @var string|null $residentId  set when editing (shows the current photo)
 * @var string|null $current     stored photo file name, if any
 */
$residentId = $residentId ?? null;
$current    = $current ?? null;
$hasPhoto   = $residentId && $current;
$src        = $hasPhoto ? base_url('files/resident/' . $residentId) . '?v=' . substr(md5((string) $current), 0, 8) : '';
?>
<div class="col-12 js-photo-widget" data-original="<?= esc($src) ?>">
    <label class="form-label">Photo</label>
    <div class="d-flex align-items-start gap-3 flex-wrap">
        <div class="js-photo-preview border rounded bg-light d-flex align-items-center justify-content-center overflow-hidden"
             style="width:120px;height:120px;flex:none;">
            <img class="js-photo-img <?= $hasPhoto ? '' : 'd-none' ?>" src="<?= esc($src) ?>" alt="Resident photo"
                 style="width:100%;height:100%;object-fit:cover;">
            <i class="bi bi-person-circle text-muted js-photo-empty <?= $hasPhoto ? 'd-none' : '' ?>" style="font-size:3.5rem;"></i>
        </div>

        <div class="flex-grow-1" style="min-width:220px;">
            <div class="d-flex gap-2 flex-wrap mb-2">
                <button type="button" class="btn btn-sm btn-outline-secondary js-photo-camera">
                    <i class="bi bi-camera me-1"></i> Take photo
                </button>
                <label class="btn btn-sm btn-outline-secondary mb-0">
                    <i class="bi bi-folder2-open me-1"></i> Choose file
                    <input type="file" class="d-none js-photo-file" name="photo" accept="image/*">
                </label>
                <button type="button" class="btn btn-sm btn-outline-danger js-photo-clear <?= $hasPhoto ? '' : 'd-none' ?>">
                    <i class="bi bi-x-lg me-1"></i> Remove
                </button>
            </div>

            <div class="js-photo-camera-box d-none mb-2">
                <video class="js-photo-video rounded bg-dark" autoplay playsinline muted style="width:100%;max-width:320px;"></video>
                <div class="mt-2 d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-navy js-photo-snap"><i class="bi bi-record-circle me-1"></i> Capture</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary js-photo-cancel">Cancel</button>
                </div>
            </div>

            <input type="file" class="d-none js-photo-fallback" accept="image/*" capture="user">
            <input type="hidden" name="photo_data" class="js-photo-data">
            <input type="hidden" name="remove_photo" value="0" class="js-photo-remove">
            <div class="form-text">Take a picture with the camera or pick one from your files (JPG, PNG or WebP). Optional.</div>
        </div>
    </div>
</div>
