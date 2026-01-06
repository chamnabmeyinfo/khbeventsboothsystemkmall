

<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>
<?php $__env->startSection('breadcrumb', 'Dashboard v1'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo e($stats['total_booths']); ?></h3>
                <p>Booths</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?php echo e($stats['available_booths']); ?></h3>
                <p>Available</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-2 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo e($stats['reserved_booths']); ?></h3>
                <p>Reserve</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-2 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?php echo e($stats['confirmed_booths']); ?></h3>
                <p>Books</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-2 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo e($stats['paid_booths']); ?></h3>
                <p>Paid</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <?php if(auth()->user()->isAdmin()): ?>
    <!-- Left col -->
    <section class="col-lg-12 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Table User</h3>
            </div>
            <div class="card-body">
                <table id="dataUser" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>UserName</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Reserve</th>
                        <th>Booking</th>
                        <th>Paid</th>
                        <th>Last Login</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $userStats ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($usr['id'] ?? 'N/A'); ?></td>
                            <td><?php echo e($usr['username'] ?? 'N/A'); ?></td>
                            <td><?php echo e($usr['type'] ?? 'N/A'); ?></td>
                            <td>
                                <input type="checkbox" 
                                       class="usrStatus" 
                                       data-id="<?php echo e($usr['id'] ?? 0); ?>" 
                                       name="my-checkbox" 
                                       <?php echo e(($usr['status'] ?? 0) == 1 ? 'checked' : ''); ?>

                                       data-bootstrap-switch 
                                       data-off-color="danger" 
                                       data-on-color="success">
                            </td>
                            <td><?php echo e($usr['reserve'] ?? 0); ?></td>
                            <td><?php echo e($usr['booking'] ?? 0); ?></td>
                            <td><?php echo e($usr['paid'] ?? 0); ?></td>
                            <td><?php echo e($usr['last_login'] ?? 'N/A'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center">No users found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <!-- /.Left col -->
    
    <!-- right col -->
    <section class="col-lg-12 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Table Client</h3>
                <a href="<?php echo e(route('export.bookings')); ?>" class="btn btn-success float-md-right" id="btnExportExcel">Export Excel</a>
            </div>
            <div class="card-body">
                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>BookID</th>
                        <th>ClientName</th>
                        <th>CompanyName</th>
                        <th>Contact</th>
                        <th>SaleBy</th>
                        <th>Reserve</th>
                        <th>Booking</th>
                        <th>Paid</th>
                        <th>Category</th>
                        <th>DateBooking</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $clientData ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($data['book_id'] ?? 'N/A'); ?></td>
                            <td><?php echo e($data['client_name'] ?? 'N/A'); ?></td>
                            <td><?php echo e($data['company'] ?? 'N/A'); ?></td>
                            <td><?php echo e($data['phone'] ?? 'N/A'); ?></td>
                            <td><?php echo e($data['user_name'] ?? 'N/A'); ?></td>
                            <td style="color: blue;"><?php echo e(isset($data['status']) && $data['status'] == 3 ? ($data['booth_number'] ?? '') : ''); ?></td>
                            <td style="color:#d15757;"><?php echo e(isset($data['status']) && $data['status'] == 2 ? ($data['booth_number'] ?? '') : ''); ?></td>
                            <td style="color:red;"><?php echo e(isset($data['status']) && $data['status'] == 5 ? ($data['booth_number'] ?? '') : ''); ?></td>
                            <td>
                                <?php if(!empty($data['category_name'])): ?>
                                    <?php echo e($data['category_name']); ?>

                                    <?php if(!empty($data['sub_category_name'])): ?>
                                        > <?php echo e($data['sub_category_name']); ?>

                                    <?php endif; ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($data['date_book'] ?? 'N/A'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <!-- right col -->
</div>
<!-- /.row (main row) -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css">

<script>
$(document).ready(function () {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000
    });
    
    // Initialize DataTables only if tables exist and have proper structure
    if ($("#example2").length && $("#example2 tbody tr").length > 0) {
        try {
            $("#example2").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false
            });
        } catch (e) {
            console.error("Error initializing example2 DataTable:", e);
        }
    }
    
    if ($("#dataUser").length && $("#dataUser tbody tr").length > 0) {
        try {
            $("#dataUser").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false
            });
        } catch (e) {
            console.error("Error initializing dataUser DataTable:", e);
        }
    }
    
    // Bootstrap Switch initialization
    if (typeof $.fn.bootstrapSwitch !== 'undefined') {
        $("input[data-bootstrap-switch]").each(function(){
            try {
                $(this).bootstrapSwitch({
                    state: $(this).prop("checked"),
                    size: "small"
                });
            } catch (e) {
                console.error("Error initializing bootstrap switch:", e);
            }
        });
        
        // User Status Switch button
        $(".usrStatus").on("switchChange.bootstrapSwitch", function(event, state) {
            var id = $(this).data("id");
            if (id && id > 0) {
                var url = "/users/" + id + "/status?status=" + (state ? 1 : 0);
                ajax(url);
            }
        });
    }
    
    function ajax(url) {
        $.ajax({
            url: url,
            dataType: "json",
            type: "get",
            contentType: "application/x-www-form-urlencoded",
            success: function(data) {
                Toast.fire({
                    icon: "success",
                    title: "Your update Successful!"
                });
            },
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
                Toast.fire({
                    icon: "error",
                    title: "Your update Error!"
                });
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.adminlte', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\KHB\khbevents\kmall\kmallxmas-laravel\resources\views/dashboard/index-adminlte.blade.php ENDPATH**/ ?>