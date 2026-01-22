<tr class="table-row-hover">
    <td>
        <input type="checkbox" class="form-check-input client-checkbox" value="{{ $client->id }}">
    </td>
    <td>
        <strong class="text-primary">#{{ $client->id }}</strong>
    </td>
    <td>
        <div class="d-flex align-items-center">
            <div class="mr-2">
                <i class="fas fa-building text-muted" style="font-size: 1.2rem;"></i>
            </div>
            <div>
                <strong>{{ $client->company ?? 'N/A' }}</strong>
            </div>
        </div>
    </td>
    <td>
        <div class="d-flex align-items-center">
            <div class="mr-2">
                <x-avatar 
                    :avatar="$client->avatar" 
                    :name="$client->name" 
                    :size="'sm'" 
                    :type="'client'"
                    :shape="'circle'"
                />
            </div>
            <div>
                <strong>{{ $client->name }}</strong>
                @if($client->sex)
                <br><small class="text-muted">
                    <i class="fas fa-{{ $client->sex == 1 ? 'mars' : 'venus' }} mr-1"></i>
                    {{ $client->sex == 1 ? 'Male' : 'Female' }}
                </small>
                @endif
            </div>
        </div>
    </td>
    <td>
        @if($client->position)
            <span class="badge badge-info">
                <i class="fas fa-briefcase mr-1"></i>{{ $client->position }}
            </span>
        @else
            <span class="text-muted">N/A</span>
        @endif
    </td>
    <td>
        @if($client->phone_number)
            <a href="tel:{{ $client->phone_number }}" class="text-primary">
                <i class="fas fa-phone mr-1"></i>{{ $client->phone_number }}
            </a>
        @else
            <span class="text-muted">N/A</span>
        @endif
    </td>
    <td>
        <div>
            <small class="text-muted">
                <i class="fas fa-store mr-1"></i>{{ $client->booths_count ?? 0 }} booth(s)
            </small>
        </div>
        <div>
            <small class="text-muted">
                <i class="fas fa-calendar mr-1"></i>{{ $client->books_count ?? 0 }} booking(s)
            </small>
        </div>
    </td>
    <td>
        <div class="btn-group btn-group-sm" role="group">
            <a href="{{ route('clients.show', $client) }}" class="btn btn-info" title="View Details">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" class="btn btn-danger" onclick="deleteClient({{ $client->id }}, '{{ $client->name }}')" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </td>
</tr>
