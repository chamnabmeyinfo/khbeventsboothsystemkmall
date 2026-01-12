<style>
    /* ============================================
       MODERN HR MODULE DESIGN SYSTEM 2026
       Glassmorphism + Gradients + Smooth Animations
       ============================================ */
    
    :root {
        --hr-primary: #667eea;
        --hr-secondary: #764ba2;
        --hr-accent: #f093fb;
        --hr-success: #1cc88a;
        --hr-warning: #f6c23e;
        --hr-danger: #e74a3b;
        --hr-info: #36b9cc;
        --hr-dark: #1a1a2e;
        --hr-light: rgba(255, 255, 255, 0.05);
    }
    
    /* Page Header - Modern Design */
    .content-header {
        background: linear-gradient(135deg, 
            rgba(102, 126, 234, 0.1) 0%,
            rgba(118, 75, 162, 0.1) 100%);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(102, 126, 234, 0.2);
        padding: 1.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 16px 16px;
    }
    
    .content-header h1 {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
        letter-spacing: 0.5px;
    }
    
    /* Modern Statistics Cards */
    .stat-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(102, 126, 234, 0.1);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 
            0 8px 32px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--hr-primary) 0%, var(--hr-secondary) 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 
            0 16px 48px rgba(102, 126, 234, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.9);
    }
    
    .stat-card:hover::before {
        transform: scaleX(1);
    }
    
    .stat-card-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        margin-bottom: 1rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover .stat-card-icon {
        transform: scale(1.1) rotate(5deg);
    }
    
    .stat-card-icon.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stat-card-icon.success {
        background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
    }
    
    .stat-card-icon.warning {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
    }
    
    .stat-card-icon.danger {
        background: linear-gradient(135deg, #e74a3b 0%, #c23321 100%);
    }
    
    .stat-card-icon.info {
        background: linear-gradient(135deg, #36b9cc 0%, #2c9faf 100%);
    }
    
    .stat-card-value {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0.5rem 0;
        line-height: 1.2;
    }
    
    .stat-card-label {
        color: #6c757d;
        font-weight: 600;
        font-size: 0.95rem;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    
    /* Modern Info Boxes */
    .info-box-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(102, 126, 234, 0.1);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }
    
    .info-box-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--hr-primary) 0%, var(--hr-secondary) 100%);
    }
    
    .info-box-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(102, 126, 234, 0.15);
    }
    
    .info-box-icon-modern {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        margin-right: 1rem;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    /* Modern Cards */
    .card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(102, 126, 234, 0.1);
        border-radius: 16px;
        box-shadow: 
            0 8px 32px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card-modern:hover {
        box-shadow: 
            0 12px 48px rgba(102, 126, 234, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.9);
    }
    
    .card-header-modern {
        background: linear-gradient(135deg, 
            rgba(102, 126, 234, 0.1) 0%,
            rgba(118, 75, 162, 0.1) 100%);
        border-bottom: 1px solid rgba(102, 126, 234, 0.2);
        padding: 1.25rem 1.5rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    
    .card-header-modern h3 {
        margin: 0;
        color: #1a1a2e;
        font-weight: 800;
    }
    
    .card-header-modern i {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Modern Buttons */
    .btn-modern {
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-modern-primary:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        color: white;
    }
    
    .btn-modern-success {
        background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
        color: white;
    }
    
    .btn-modern-info {
        background: linear-gradient(135deg, #36b9cc 0%, #2c9faf 100%);
        color: white;
    }
    
    .btn-modern-warning {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        color: white;
    }
    
    .btn-modern-danger {
        background: linear-gradient(135deg, #e74a3b 0%, #c23321 100%);
        color: white;
    }
    
    /* Modern Tables */
    .table-modern {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, 
            rgba(102, 126, 234, 0.1) 0%,
            rgba(118, 75, 162, 0.1) 100%);
    }
    
    .table-modern thead th {
        border: none;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        font-size: 0.85rem;
        padding: 1rem;
        color: #1a1a2e;
    }
    
    .table-modern tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(102, 126, 234, 0.05);
    }
    
    .table-modern tbody tr:hover {
        background: rgba(102, 126, 234, 0.05);
        transform: scale(1.01);
    }
    
    .table-modern tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    /* Badge Modern */
    .badge-modern {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        letter-spacing: 0.3px;
        font-size: 0.85rem;
    }
    
    .badge-modern-success {
        background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
        color: white;
    }
    
    .badge-modern-warning {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        color: white;
    }
    
    .badge-modern-danger {
        background: linear-gradient(135deg, #e74a3b 0%, #c23321 100%);
        color: white;
    }
    
    .badge-modern-info {
        background: linear-gradient(135deg, #36b9cc 0%, #2c9faf 100%);
        color: white;
    }
    
    .badge-modern-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    /* Form Modern */
    .form-control-modern {
        border-radius: 12px;
        border: 2px solid rgba(102, 126, 234, 0.1);
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* Progress Bar Modern */
    .progress-modern {
        height: 8px;
        border-radius: 10px;
        background: rgba(102, 126, 234, 0.1);
        overflow: hidden;
    }
    
    .progress-bar-modern {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        transition: width 0.6s ease;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }
        
        .card-modern {
            margin-bottom: 1rem;
        }
    }
</style>
