<tr class="table-row-hover">
    <td>
        <input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->id }}" aria-label="Select {{ $user->username }}">
    </td>
    <td>
        <span class="status-badge status-badge-blue">#{{ $user->id }}</span>
    </td>
    <td>
        <div class="d-flex align-items-center gap-2">
            <div class="flex-shrink-0">
                <x-avatar
                    :avatar="$user->avatar"
                    :name="$user->username"
                    :size="'sm'"
                    :type="$user->isAdmin() ? 'admin' : 'user'"
                    :shape="'circle'"
                />
            </div>
            <div class="min-w-0">
                <strong class="d-block text-truncate">{{ $user->username }}</strong>
                @php
                    try {
                        $bookingCount = $user->books()->count();
                        if ($bookingCount > 0) {
                            echo '<small class="text-muted d-block"><i class="fas fa-calendar-check me-1" aria-hidden="true"></i>' . $bookingCount . ' booking(s)</small>';
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
            <span class="status-badge status-badge-purple">
                <i class="fas fa-shield-alt me-1" aria-hidden="true"></i>Admin
            </span>
        @else
            <span class="status-badge status-badge-neutral">
                <i class="fas fa-user-tie me-1" aria-hidden="true"></i>Sale
            </span>
        @endif
    </td>
    <td>
        @if($user->role)
            <span class="status-badge status-badge-blue">
                <i class="fas fa-user-shield me-1" aria-hidden="true"></i>{{ $user->role->name }}
            </span>
            @if($user->role->permissions->count() > 0)
            <br><small class="text-muted">
                {{ $user->role->permissions->count() }} permission(s)
            </small>
            @endif
        @else
            <span class="status-badge status-badge-neutral">
                <i class="fas fa-minus-circle me-1" aria-hidden="true"></i>No role
            </span>
        @endif
    </td>
    <td>
        @if($user->isActive())
            <span class="status-badge status-badge-green status-toggle" role="button" tabindex="0" onclick="toggleUserStatus({{ $user->id }}, {{ $user->status }})" onkeydown="if(event.key==='Enter'){toggleUserStatus({{ $user->id }}, {{ $user->status }})}" title="Click to deactivate">
                <i class="fas fa-check-circle me-1" aria-hidden="true"></i>Active
            </span>
        @else
            <span class="status-badge status-badge-orange status-toggle" role="button" tabindex="0" onclick="toggleUserStatus({{ $user->id }}, {{ $user->status }})" onkeydown="if(event.key==='Enter'){toggleUserStatus({{ $user->id }}, {{ $user->status }})}" title="Click to activate">
                <i class="fas fa-times-circle me-1" aria-hidden="true"></i>Inactive
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
                <i class="fas fa-cube me-1" aria-hidden="true"></i>{{ $boothCount }} booth(s)
            </small>
        </div>
        <div>
            <small class="text-muted">
                <i class="fas fa-calendar me-1" aria-hidden="true"></i>{{ $bookingCount }} booking(s)
            </small>
        </div>
    </td>
    <td>
        <div class="d-flex flex-wrap gap-1">
            <a href="{{ route('users.show', $user) }}" class="action-btn action-btn-secondary users-table-action-btn" title="View details">
                <i class="fas fa-eye" aria-hidden="true"></i>
            </a>
            <a href="{{ route('users.edit', $user) }}" class="action-btn action-btn-secondary users-table-action-btn" title="Edit">
                <i class="fas fa-edit" aria-hidden="true"></i>
            </a>
            @if(auth()->user()->isAdmin() && $user->id != auth()->id())
            <button type="button" class="action-btn action-btn-secondary users-table-action-btn text-danger border-danger-subtle" title="Delete" onclick="deleteUser({{ $user->id }}, {{ json_encode($user->username) }})">
                <i class="fas fa-trash" aria-hidden="true"></i>
            </button>
            @endif
        </div>
    </td>
</tr>
