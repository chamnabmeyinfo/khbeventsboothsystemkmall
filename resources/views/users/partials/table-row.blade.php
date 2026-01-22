<tr class="table-row-hover">
    <td>
        <input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->id }}">
    </td>
    <td>
        <strong class="text-primary">#{{ $user->id }}</strong>
    </td>
    <td>
        <div class="d-flex align-items-center">
            <div class="mr-2">
                <x-avatar 
                    :avatar="$user->avatar" 
                    :name="$user->username" 
                    :size="'sm'" 
                    :type="$user->isAdmin() ? 'admin' : 'user'"
                    :shape="'circle'"
                />
            </div>
            <div>
                <strong>{{ $user->username }}</strong>
                @php
                    try {
                        $bookingCount = $user->books()->count();
                        if ($bookingCount > 0) {
                            echo '<br><small class="text-muted"><i class="fas fa-calendar-check mr-1"></i>' . $bookingCount . ' booking(s)</small>';
                        }
                    } catch (\Exception $e) {
                        // Skip if relationship fails
                    }
                @endphp
            </div>
        </div>
    </td>
    <td>
        @if($user->isAdmin())
            <span class="badge badge-danger">
                <i class="fas fa-shield-alt mr-1"></i>Admin
            </span>
        @else
            <span class="badge badge-secondary">
                <i class="fas fa-user-tie mr-1"></i>Sale
            </span>
        @endif
    </td>
    <td>
        @if($user->role)
            <span class="badge badge-info">
                <i class="fas fa-user-shield mr-1"></i>{{ $user->role->name }}
            </span>
            @if($user->role->permissions->count() > 0)
            <br><small class="text-muted">
                {{ $user->role->permissions->count() }} permission(s)
            </small>
            @endif
        @else
            <span class="badge badge-light">
                <i class="fas fa-minus-circle mr-1"></i>No Role
            </span>
        @endif
    </td>
    <td>
        @if($user->isActive())
            <span class="badge badge-success status-toggle" onclick="toggleUserStatus({{ $user->id }}, {{ $user->status }})" style="cursor: pointer;" title="Click to deactivate">
                <i class="fas fa-check-circle mr-1"></i>Active
            </span>
        @else
            <span class="badge badge-warning status-toggle" onclick="toggleUserStatus({{ $user->id }}, {{ $user->status }})" style="cursor: pointer;" title="Click to activate">
                <i class="fas fa-times-circle mr-1"></i>Inactive
            </span>
        @endif
    </td>
    <td>
        @php
            try {
                $boothCount = $user->booths()->count();
                $bookingCount = $user->books()->count();
            } catch (\Exception $e) {
                $boothCount = 0;
                $bookingCount = 0;
            }
        @endphp
        <div>
            <small class="text-muted">
                <i class="fas fa-cube mr-1"></i>{{ $boothCount }} booth(s)
            </small>
        </div>
        <div>
            <small class="text-muted">
                <i class="fas fa-calendar mr-1"></i>{{ $bookingCount }} booking(s)
            </small>
        </div>
    </td>
    <td>
        <div class="btn-group btn-group-sm" role="group">
            <a href="{{ route('users.show', $user) }}" class="btn btn-info" title="View Details">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" class="btn btn-danger" onclick="deleteUser({{ $user->id }}, '{{ $user->username }}')" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </td>
</tr>
