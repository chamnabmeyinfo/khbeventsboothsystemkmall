

<?php $__env->startSection('title', 'Clients'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-users me-2"></i>Clients</h2>
    </div>
    <div class="col-auto">
        <div class="btn-group">
            <a href="<?php echo e(route('clients.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Client
            </a>
            <a href="<?php echo e(route('export.clients')); ?>" class="btn btn-success">
                <i class="fas fa-file-csv me-2"></i>Export CSV
            </a>
        </div>
    </div>
</div>

<!-- Advanced Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('clients.index')); ?>" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by name, company, phone, or position..." 
                           value="<?php echo e(request('search')); ?>">
                </div>
            </div>
            <div class="col-md-2">
                <select name="sort_by" class="form-select">
                    <option value="company" <?php echo e(request('sort_by') == 'company' ? 'selected' : ''); ?>>Company</option>
                    <option value="name" <?php echo e(request('sort_by') == 'name' ? 'selected' : ''); ?>>Name</option>
                    <option value="position" <?php echo e(request('sort_by') == 'position' ? 'selected' : ''); ?>>Position</option>
                    <option value="phone_number" <?php echo e(request('sort_by') == 'phone_number' ? 'selected' : ''); ?>>Phone</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>
                                    <a href="<?php echo e(route('clients.index', ['sort_by' => 'company', 'sort_dir' => $sortBy == 'company' && $sortDir == 'asc' ? 'desc' : 'asc', 'search' => request('search')])); ?>" class="text-decoration-none text-dark">
                                        Company
                                        <?php if($sortBy == 'company'): ?>
                                            <i class="fas fa-sort-<?php echo e($sortDir == 'asc' ? 'up' : 'down'); ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?php echo e(route('clients.index', ['sort_by' => 'name', 'sort_dir' => $sortBy == 'name' && $sortDir == 'asc' ? 'desc' : 'asc', 'search' => request('search')])); ?>" class="text-decoration-none text-dark">
                                        Name
                                        <?php if($sortBy == 'name'): ?>
                                            <i class="fas fa-sort-<?php echo e($sortDir == 'asc' ? 'up' : 'down'); ?>"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>Position</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($client->id); ?></td>
                        <td><?php echo e($client->company ?? 'N/A'); ?></td>
                        <td><?php echo e($client->name); ?></td>
                        <td><?php echo e($client->position ?? 'N/A'); ?></td>
                        <td><?php echo e($client->phone_number ?? 'N/A'); ?></td>
                        <td>
                            <a href="<?php echo e(route('clients.show', $client)); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('clients.edit', $client)); ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('clients.destroy', $client)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center">No clients found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <?php echo e($clients->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\KHB\khbevents\kmall\kmallxmas-laravel\resources\views/clients/index.blade.php ENDPATH**/ ?>