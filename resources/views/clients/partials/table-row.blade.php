@php
    $dash = '—';
    $sexLabel = match ((int) ($client->sex ?? 0)) {
        1 => 'Male',
        2 => 'Female',
        3 => 'Other',
        default => $dash,
    };
    $web = $client->website ? trim($client->website) : '';
    $webHref = '';
    if ($web !== '') {
        $webHref = \Illuminate\Support\Str::startsWith($web, ['http://', 'https://']) ? $web : 'https://'.$web;
    }
    $boothN = (int) ($client->booths_count ?? 0);
    $bookN = (int) ($client->books_count ?? 0);
@endphp
<tr class="table-row-hover clients-table-row">
    <td>
        <input type="checkbox" class="form-check-input client-checkbox" value="{{ $client->id }}" aria-label="Select client #{{ $client->id }}">
    </td>
    <td data-clients-col="id"><strong class="text-primary">#{{ $client->id }}</strong></td>
    <td data-clients-col="company">{{ ($client->company !== null && $client->company !== '') ? $client->company : $dash }}</td>
    <td class="small" data-clients-col="company_name_khmer">{{ ($client->company_name_khmer !== null && $client->company_name_khmer !== '') ? $client->company_name_khmer : $dash }}</td>
    <td class="clients-name-cell" data-clients-col="name">
        <div class="d-flex align-items-start gap-2">
            <div class="flex-shrink-0 pt-1">
                <x-avatar
                    :avatar="$client->avatar"
                    :name="$client->name"
                    :size="'sm'"
                    :type="'client'"
                    :shape="'circle'"
                />
            </div>
            <div class="clients-name-text">
                <strong>{{ $client->name }}</strong>
            </div>
        </div>
    </td>
    <td class="small" data-clients-col="sex">{{ $sexLabel }}</td>
    <td data-clients-col="position">
        @if($client->position)
            <span class="clients-position-badge text-wrap">{{ $client->position }}</span>
        @else
            <span class="text-muted">{{ $dash }}</span>
        @endif
    </td>
    <td class="small">
        @if($client->phone_number)
            <a href="tel:{{ $client->phone_number }}" class="text-primary text-nowrap">{{ $client->phone_number }}</a>
        @else
            <span class="text-muted">{{ $dash }}</span>
        @endif
    </td>
    <td class="small" data-clients-col="phone_1">{{ $client->phone_1 ?: $dash }}</td>
    <td class="small" data-clients-col="email">
        @if($client->email)
            <a href="mailto:{{ $client->email }}" class="text-primary">{{ $client->email }}</a>
        @else
            <span class="text-muted">{{ $dash }}</span>
        @endif
    </td>
    <td class="small" data-clients-col="address">{{ $client->address ?: $dash }}</td>
    <td class="small" data-clients-col="tax_id">{{ $client->tax_id ?: $dash }}</td>
    <td class="small" data-clients-col="website">
        @if($webHref !== '')
            <a href="{{ $webHref }}" target="_blank" rel="noopener noreferrer" class="text-primary">{{ $web }}</a>
        @else
            <span class="text-muted">{{ $dash }}</span>
        @endif
    </td>
    <td class="small" data-clients-col="notes">{{ $client->notes ?: $dash }}</td>
    <td data-clients-col="booths_count">
        <span class="status-badge-muted client-activity-badge">{{ $boothN }}&nbsp;Booth{{ $boothN === 1 ? '' : 's' }}</span>
    </td>
    <td data-clients-col="books_count">
        <span class="status-badge status-badge-green client-activity-badge">{{ $bookN }}&nbsp;Booking{{ $bookN === 1 ? '' : 's' }}</span>
    </td>
    <td>
        <div class="btn-group btn-group-sm" role="group" aria-label="Row actions">
            <a href="{{ route('clients.show', $client) }}" class="btn btn-info" title="View"><i class="fas fa-eye" aria-hidden="true"></i></a>
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning" title="Edit"><i class="fas fa-edit" aria-hidden="true"></i></a>
            <button type="button" class="btn btn-danger btn-client-delete-row" title="Delete"
                data-client-id="{{ $client->id }}"
                data-client-name="{{ e($client->name) }}">
                <i class="fas fa-trash" aria-hidden="true"></i>
            </button>
        </div>
    </td>
</tr>
