<?php $__env->startSection('title', 'Knowledge Base'); ?>
<?php $__env->startSection('page-title', 'Knowledge Base'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="font-weight: 700; color: #1A1A1A; font-size: 32px; margin: 0;">
                <i class="fas fa-robot" style="color: #F59E0B; margin-right: 12px; opacity: 0.8;"></i> Knowledge Base
            </h2>
            <small style="color: #6B7280; font-size: 14px;">Kelola pertanyaan, jawaban, keyword, dan instruksi AI chatbot</small>
        </div>
        <a href="<?php echo e(route('knowledge-base.create')); ?>" class="btn fw-bold" style="background-color: #F59E0B; color: white; padding: 12px 24px; font-size: 14px; border-radius: 8px; border: none; text-decoration: none;">
            <i class="fas fa-plus me-2"></i> Tambah Entri
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); background-color: #FFFFFF;">
        <div class="card-body p-0">
            <?php if($entries->count()): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size: 14px;">
                        <thead>
                            <tr style="background-color: #F9FAFB; border-bottom: 1px solid #E5E7EB;">
                                <th style="padding: 16px 20px;">Judul</th>
                                <th style="padding: 16px 20px;">Topik</th>
                                <th style="padding: 16px 20px;">Pertanyaan</th>
                                <th style="padding: 16px 20px;">Status</th>
                                <th style="padding: 16px 20px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td style="padding: 16px 20px; font-weight: 600;"><?php echo e($entry->judul); ?></td>
                                    <td style="padding: 16px 20px;"><?php echo e($entry->topik); ?></td>
                                    <td style="padding: 16px 20px; color: #6B7280;"><?php echo e(\Illuminate\Support\Str::limit($entry->pertanyaan, 90)); ?></td>
                                    <td style="padding: 16px 20px;">
                                        <?php if($entry->is_active): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 16px 20px; text-align: center;">
                                        <a href="<?php echo e(route('knowledge-base.edit', $entry)); ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="<?php echo e(route('knowledge-base.destroy', $entry)); ?>" method="POST" style="display: inline-block;" onsubmit="return confirm('Hapus entry ini?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-3"><?php echo e($entries->links()); ?></div>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; color: #9CA3AF;">
                    <i class="fas fa-robot" style="font-size: 64px; opacity: 0.3; margin-bottom: 16px;"></i>
                    <p style="font-size: 16px; font-weight: 500; color: #6B7280; margin: 0;">Belum ada knowledge base</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Pongs\pengmas semester 8\sistem\sistem inventory\sistem inventory\resources\views/knowledge-base/index.blade.php ENDPATH**/ ?>