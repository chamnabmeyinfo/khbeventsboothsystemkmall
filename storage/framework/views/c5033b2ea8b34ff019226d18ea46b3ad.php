<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'KHB Booths Booking System'); ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo e(route('dashboard')); ?>">
                <i class="fas fa-calendar-alt me-2"></i>KHB Booths
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('booths.index')); ?>">Booths</a>
                    </li>
                    <?php if(auth()->guard()->check()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('booths.my-booths')); ?>">My Booths</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('clients.index')); ?>">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('books.index')); ?>">Bookings</a>
                    </li>
                    <?php if(auth()->user()->isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('users.index')); ?>">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('categories.index')); ?>">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('settings.index')); ?>">Settings</a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if(auth()->guard()->check()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo e(auth()->user()->username); ?>

                            <?php if(auth()->user()->isAdmin()): ?>
                                <span class="badge bg-danger ms-1">Admin</span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('login')); ?>">Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-4">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- SweetAlert2 for beautiful modals -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Panzoom Library for Zoom & Pan -->
    <script src="https://cdn.jsdelivr.net/npm/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>
    
    <!-- Custom Notification System -->
    <script>
    // Custom notification functions to replace browser alerts
    window.showNotification = function(message, type = 'info', title = null) {
        const config = {
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: type === 'error' ? 5000 : 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        };

        const icons = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info',
            'question': 'question'
        };

        return Swal.fire({
            ...config,
            icon: icons[type] || 'info',
            title: title || (type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : 'Info'),
            text: message
        });
    };

    // Copy text to clipboard
    window.copyToClipboard = function(text) {
        // Try modern clipboard API first
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text).then(function() {
                return true;
            }).catch(function(err) {
                console.error('Failed to copy using clipboard API:', err);
                return fallbackCopyToClipboard(text);
            });
        } else {
            // Fallback for older browsers
            return fallbackCopyToClipboard(text);
        }
    };

    // Fallback copy method for older browsers
    function fallbackCopyToClipboard(text) {
        return new Promise(function(resolve, reject) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                if (successful) {
                    resolve(true);
                } else {
                    reject(new Error('Copy command failed'));
                }
            } catch (err) {
                document.body.removeChild(textArea);
                reject(err);
            }
        });
    }

    // Replace alert() with custom notification
    window.customAlert = function(message, type = 'info', title = null) {
        const isError = type === 'error';
        const fullMessage = title ? (title + '\n\n' + message) : message;
        
        return Swal.fire({
            icon: type === 'error' ? 'error' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info',
            title: title || (type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : type === 'warning' ? 'Warning!' : 'Information'),
            html: message + (isError ? '<br><br><button id="copyErrorBtn" class="btn btn-sm btn-outline-light mt-2" style="font-size: 12px;"><i class="fas fa-copy me-1"></i>Copy Error Message</button>' : ''),
            confirmButtonText: 'OK',
            confirmButtonColor: type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : type === 'warning' ? '#ffc107' : '#007bff',
            buttonsStyling: true,
            customClass: {
                popup: 'custom-swal-popup'
            },
            didOpen: function() {
                // Add copy button functionality for error messages
                if (isError) {
                    const copyBtn = document.getElementById('copyErrorBtn');
                    if (copyBtn) {
                        copyBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            copyToClipboard(fullMessage).then(function() {
                                // Show success feedback
                                const originalHtml = copyBtn.innerHTML;
                                copyBtn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                                copyBtn.classList.remove('btn-outline-light');
                                copyBtn.classList.add('btn-success');
                                copyBtn.disabled = true;
                                
                                // Reset after 2 seconds
                                setTimeout(function() {
                                    copyBtn.innerHTML = originalHtml;
                                    copyBtn.classList.remove('btn-success');
                                    copyBtn.classList.add('btn-outline-light');
                                    copyBtn.disabled = false;
                                }, 2000);
                            }).catch(function(err) {
                                console.error('Failed to copy:', err);
                                // Show error feedback
                                const originalHtml = copyBtn.innerHTML;
                                copyBtn.innerHTML = '<i class="fas fa-times me-1"></i>Failed';
                                copyBtn.classList.remove('btn-outline-light');
                                copyBtn.classList.add('btn-danger');
                                
                                setTimeout(function() {
                                    copyBtn.innerHTML = originalHtml;
                                    copyBtn.classList.remove('btn-danger');
                                    copyBtn.classList.add('btn-outline-light');
                                }, 2000);
                            });
                        });
                    }
                }
            }
        });
    };

    // Replace confirm() with custom confirmation dialog
    window.customConfirm = function(message, title = 'Confirm Action', confirmText = 'Yes', cancelText = 'No') {
        return Swal.fire({
            title: title,
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            buttonsStyling: true,
            reverseButtons: true,
            customClass: {
                popup: 'custom-swal-popup'
            }
        }).then((result) => {
            return result.isConfirmed;
        });
    };

    // Standalone copy function that can be used anywhere
    // Usage: copyToClipboard('text to copy').then(() => console.log('Copied!'));
    window.copyTextToClipboard = function(text, showNotification = true) {
        return copyToClipboard(text).then(function() {
            if (showNotification) {
                showNotification('Text copied to clipboard!', 'success');
            }
            return true;
        }).catch(function(err) {
            console.error('Failed to copy:', err);
            if (showNotification) {
                showNotification('Failed to copy text. Please try manually.', 'error');
            }
            return false;
        });
    };

    // Override native alert and confirm (optional - can be removed if you prefer explicit calls)
    // Uncomment these if you want to automatically replace all alert() and confirm() calls
    /*
    window.alert = function(message) {
        return window.customAlert(message, 'info');
    };
    
    window.confirm = function(message) {
        return window.customConfirm(message, 'Confirm', 'OK', 'Cancel');
    };
    */
    </script>
    
    <style>
    .custom-swal-popup {
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }
    
    #copyErrorBtn {
        transition: all 0.3s ease;
    }
    
    #copyErrorBtn:hover {
        transform: scale(1.05);
    }
    
    /* Make sure copy button is visible in error modals */
    .swal2-popup .btn-outline-light {
        border-color: rgba(255, 255, 255, 0.5);
        color: #fff;
    }
    
    .swal2-popup .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.8);
    }
    </style>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\KHB\khbevents\kmall\kmallxmas-laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>