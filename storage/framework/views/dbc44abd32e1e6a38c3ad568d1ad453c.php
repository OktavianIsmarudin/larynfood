<?php ($entry = $entry ?? null); ?>

<div class="mb-3">
    <label class="form-label">Judul</label>
    <input type="text" name="judul" class="form-control" value="<?php echo e(old('judul', $entry->judul ?? '')); ?>" required>
</div>

<div class="mb-3">
    <label class="form-label">Topik</label>
    <select name="topik" class="form-select" required>
        <?php $__currentLoopData = ['umum','produk','checkout','tracking','pembayaran']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($topic); ?>" <?php if(old('topik', $entry->topik ?? 'umum') === $topic): echo 'selected'; endif; ?>><?php echo e(ucfirst($topic)); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Pertanyaan</label>
    <textarea name="pertanyaan" class="form-control" rows="4" required><?php echo e(old('pertanyaan', $entry->pertanyaan ?? '')); ?></textarea>
</div>

<div class="mb-3">
    <label class="form-label">Jawaban</label>
    <textarea name="jawaban" class="form-control" rows="6" required><?php echo e(old('jawaban', $entry->jawaban ?? '')); ?></textarea>
</div>

<div class="mb-3">
    <label class="form-label">Kata Kunci</label>
    <textarea name="kata_kunci" class="form-control" rows="3"><?php echo e(old('kata_kunci', $entry->kata_kunci ?? '')); ?></textarea>
    <small class="text-muted">Pisahkan dengan koma, misalnya: promo, ongkir, diskon</small>
</div>

<div class="mb-3">
    <label class="form-label">Instruksi AI</label>
    <textarea name="instruksi_ai" class="form-control" rows="4"><?php echo e(old('instruksi_ai', $entry->instruksi_ai ?? '')); ?></textarea>
    <small class="text-muted">Opsional. Dipakai sebagai konteks tambahan untuk AI.</small>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Urutan</label>
        <input type="number" name="urutan" class="form-control" min="0" value="<?php echo e(old('urutan', $entry->urutan ?? 0)); ?>">
    </div>
    <div class="col-md-6 mb-3 d-flex align-items-end">
        <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" <?php if(old('is_active', $entry->is_active ?? true)): echo 'checked'; endif; ?>>
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
    </div>
</div>
<?php /**PATH C:\Pongs\pengmas semester 8\sistem\sistem inventory\sistem inventory\resources\views/knowledge-base/_form.blade.php ENDPATH**/ ?>