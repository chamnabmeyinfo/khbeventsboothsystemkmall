

<?php $__env->startSection('title', 'Client Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-user me-2"></i>Client Details</h2>
    </div>
    <div class="col-auto">
        <a href="<?php echo e(route('clients.edit', $client)); ?>" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Clients
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Client Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">ID:</th>
                        <td><?php echo e($client->id); ?></td>
                    </tr>
                    <tr>
                        <th>Name:</th>
                        <td><?php echo e($client->name); ?></td>
                    </tr>
                    <tr>
                        <th>Company:</th>
                        <td><?php echo e($client->company ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Position:</th>
                        <td><?php echo e($client->position ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td><?php echo e($client->phone_number ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Gender:</th>
                        <td>
                            <?php if($client->sex == 1): ?> Male
                            <?php elseif($client->sex == 2): ?> Female
                            <?php else: ?> N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Booths (<?php echo e($client->booths->count()); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if($client->booths->count() > 0): ?>
                    <ul class="list-group">
                        <?php $__currentLoopData = $client->booths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booth): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item">
                            <a href="<?php echo e(route('booths.show', $booth)); ?>"><?php echo e($booth->booth_number); ?></a>
                            <span class="badge bg-<?php echo e($booth->getStatusColor()); ?> float-end">
                                <?php echo e($booth->getStatusLabel()); ?>

                            </span>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No booths assigned.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\KHB\khbevents\kmall\kmallxmas-laravel\resources\views/clients/show.blade.php ENDPATH**/ ?>