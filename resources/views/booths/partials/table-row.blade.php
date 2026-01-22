<tr>
    <td>
        <input type="checkbox" class="booth-checkbox" value="{{ $booth->id }}">
    </td>
    <td>
        @if($booth->booth_image)
            <img src="{{ asset($booth->booth_image) }}" alt="Booth Image" class="booth-image-preview" onclick="viewImage('{{ asset($booth->booth_image) }}')">
        @else
            <div style="width: 60px; height: 60px; background: #f3f4f6; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                <i class="fas fa-image"></i>
            </div>
        @endif
    </td>
    <td>
        <strong style="font-size: 16px; font-weight: 700; color: #111827;">{{ $booth->booth_number }}</strong>
    </td>
    <td>
        <span class="badge badge-info">
            {{ $booth->boothType ? $booth->boothType->name : ($booth->type == 1 ? 'Booth' : 'Space Only') }}
        </span>
    </td>
    <td>
        <span style="color: #4b5563; font-weight: 500;">{{ $booth->floorPlan ? $booth->floorPlan->name : 'N/A' }}</span>
    </td>
    <td>
        <span style="color: #374151; font-weight: 500;">{{ $booth->client ? $booth->client->company : 'N/A' }}</span>
    </td>
    <td>
        <span style="color: #4b5563; font-weight: 500;">{{ $booth->category ? $booth->category->name : 'N/A' }}</span>
    </td>
    <td>
        <span class="badge badge-{{ $booth->getStatusColor() }}">
            {{ $booth->getStatusLabel() }}
        </span>
    </td>
    <td>
        <strong style="color: #10b981; font-size: 15px; font-weight: 700;">${{ number_format($booth->price, 2) }}</strong>
    </td>
    <td>
        <span style="color: #6b7280;">{{ $booth->area_sqm ? number_format($booth->area_sqm, 2) . ' m²' : 'N/A' }}</span>
    </td>
    <td>
        <span style="color: #6b7280;">{{ $booth->capacity ? $booth->capacity . ' people' : 'N/A' }}</span>
    </td>
    <td>
        <div class="action-buttons">
            <button type="button" class="btn btn-sm btn-info btn-action" onclick="viewBooth({{ $booth->id }})" title="View">
                <i class="fas fa-eye"></i>
            </button>
            <button type="button" class="btn btn-sm btn-primary btn-action" onclick="editBooth({{ $booth->id }})" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteBooth({{ $booth->id }})" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </td>
</tr>
