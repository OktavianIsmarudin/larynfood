<?php $__env->startSection('title', 'Tambah Knowledge Base'); ?>
<?php $__env->startSection('page-title', 'Tambah Knowledge Base'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4" style="background-color: #F8FAFC; min-height: 100vh;">
    <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); background-color: #FFFFFF;">
        <div class="card-body p-4">
            <form action="<?php echo e(route('knowledge-base.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo $__env->make('knowledge-base._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <button type="submit" class="btn btn-warning fw-bold">Simpan</button>
                <a href="<?php echo e(route('knowledge-base.index')); ?>" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Pongs\pengmas semester 8\sistem\sistem inventory\sistem inventory\resources\views/knowledge-base/create.blade.php ENDPATH**/ ?>