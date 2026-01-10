# How to Add Bulk Operations to Existing Views

## Quick Guide

To add bulk operations to any table view (booths, clients, etc.), follow these steps:

### 1. Add Checkboxes to Table

Add a checkbox column to your table:

```blade
<!-- In your table header -->
<thead>
    <tr>
        <th>
            <input type="checkbox" id="selectAll">
        </th>
        <th>ID</th>
        <th>Name</th>
        <!-- other columns -->
    </tr>
</thead>

<!-- In your table body -->
<tbody>
    @foreach($items as $item)
    <tr>
        <td>
            <input type="checkbox" class="item-checkbox" value="{{ $item->id }}">
        </td>
        <td>{{ $item->id }}</td>
        <td>{{ $item->name }}</td>
        <!-- other columns -->
    </tr>
    @endforeach
</tbody>
```

### 2. Add Bulk Actions Toolbar

Add above or below your table:

```blade
<div class="bulk-actions mb-3" style="display: none;">
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-primary" onclick="bulkUpdate()">
            <i class="fas fa-edit mr-1"></i>Bulk Update
        </button>
        <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
            <i class="fas fa-trash mr-1"></i>Bulk Delete
        </button>
    </div>
    <span class="ml-3" id="selectedCount">0 selected</span>
</div>
```

### 3. Add JavaScript

Add this JavaScript to your view:

```javascript
<script>
// Select all checkbox
$('#selectAll').on('change', function() {
    $('.item-checkbox').prop('checked', $(this).prop('checked'));
    updateBulkActions();
});

// Individual checkbox
$('.item-checkbox').on('change', function() {
    updateBulkActions();
    $('#selectAll').prop('checked', $('.item-checkbox:checked').length === $('.item-checkbox').length);
});

function updateBulkActions() {
    const count = $('.item-checkbox:checked').length;
    if (count > 0) {
        $('.bulk-actions').show();
        $('#selectedCount').text(count + ' selected');
    } else {
        $('.bulk-actions').hide();
    }
}

function bulkUpdate() {
    const ids = $('.item-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (ids.length === 0) {
        alert('Please select items');
        return;
    }
    
    // Show modal or prompt for field and value
    const field = prompt('Field to update (e.g., status):');
    const value = prompt('New value:');
    
    if (field && value) {
        fetch('/bulk/booths/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ids: ids,
                field: field,
                value: value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function bulkDelete() {
    const ids = $('.item-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (ids.length === 0) {
        alert('Please select items');
        return;
    }
    
    if (!confirm('Are you sure you want to delete ' + ids.length + ' item(s)?')) {
        return;
    }
    
    fetch('/bulk/booths/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            ids: ids
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>
```

### 4. Example: Add to Booths Index

You can add this to `resources/views/booths/index.blade.php` without modifying the floor plan functionality. Just add the bulk operations section before or after the floor plan designer.

---

**Note:** This is completely optional and won't affect existing functionality!
