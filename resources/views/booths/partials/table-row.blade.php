<tr style="transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);" data-booth-id="{{ $booth->id }}">
    <td data-column="checkbox" data-column-index="0">
        <div class="d-flex align-items-center justify-content-center">
            <input type="checkbox" class="booth-checkbox" value="{{ $booth->id }}" style="width: 18px; height: 18px; cursor: pointer; accent-color: #667eea;">
        </div>
    </td>
    <td data-column="row_number" data-column-index="1" style="text-align: center; color: #64748b; font-weight: 600; font-size: 14px;">
        <span class="row-number">
            @if(isset($rowNumber))
                {{ $rowNumber }}
            @else
                {{ $loop->iteration + (($booths->currentPage() - 1) * $booths->perPage()) }}
            @endif
        </span>
    </td>
    <td data-column="image" data-column-index="2">
        @if(($boothImageUrl = \App\Helpers\AssetHelper::imageUrl($booth->booth_image)))
            <img src="{{ $boothImageUrl }}" alt="Booth Image" class="booth-image-preview" onclick="viewImage('{{ $boothImageUrl }}')" style="width: 56px; height: 56px; object-fit: cover; border-radius: 12px; border: 2px solid #e2e8f0; cursor: pointer; transition: all 0.2s ease;" onerror="this.style.display='none'; this.nextElementSibling && (this.nextElementSibling.style.display='flex');">
            <div style="display: none; width: 56px; height: 56px; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); border-radius: 12px; align-items: center; justify-content: center; color: #94a3b8; border: 2px solid #e2e8f0;"><i class="fas fa-image" style="font-size: 20px;"></i></div>
        @else
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #94a3b8; border: 2px solid #e2e8f0;">
                <i class="fas fa-image" style="font-size: 20px;"></i>
            </div>
        @endif
    </td>
    <td data-column="booth_number" data-column-index="3">
        <strong style="font-size: 15px; font-weight: 700; color: #1e293b; letter-spacing: -0.2px;">{{ $booth->booth_number }}</strong>
    </td>
    <td data-column="type" data-column-index="4">
        <span class="badge badge-info" style="padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 12px;">
            {{ $booth->boothType ? $booth->boothType->name : ($booth->type == 1 ? 'Booth' : 'Space Only') }}
        </span>
    </td>
    <td data-column="floor_plan" data-column-index="5">
        <span style="color: #475569; font-weight: 500; font-size: 14px;">{{ $booth->floorPlan ? $booth->floorPlan->name : 'N/A' }}</span>
    </td>
    <td data-column="company" data-column-index="6">
        <span style="color: #1e293b; font-weight: 600; font-size: 14px;">{{ $booth->client ? $booth->client->company : 'N/A' }}</span>
    </td>
    <td data-column="category" data-column-index="7">
        <span style="color: #475569; font-weight: 500; font-size: 14px;">{{ $booth->category ? $booth->category->name : 'N/A' }}</span>
    </td>
    <td data-column="status" data-column-index="8">
        <span class="badge badge-{{ $booth->getStatusColor() }}" style="padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 12px;">
            {{ $booth->getStatusLabel() }}
        </span>
    </td>
    <td data-column="price" data-column-index="9">
        <strong style="color: #10b981; font-size: 15px; font-weight: 700; letter-spacing: -0.3px;">${{ number_format($booth->price, 2) }}</strong>
    </td>
    <td data-column="area" data-column-index="10">
        <span style="color: #64748b; font-size: 14px; font-weight: 500;">{{ $booth->area_sqm ? number_format($booth->area_sqm, 2) . ' m²' : 'N/A' }}</span>
    </td>
    <td data-column="capacity" data-column-index="11">
        <span style="color: #64748b; font-size: 14px; font-weight: 500;">{{ $booth->capacity ? $booth->capacity . ' people' : 'N/A' }}</span>
    </td>
    <td data-column="actions" data-column-index="12">
        <div class="action-buttons" style="display: flex; gap: 6px; justify-content: center;">
            <button type="button" class="btn btn-sm btn-info btn-action" onclick="viewBooth({{ $booth->id }})" title="View" style="width: 36px; height: 36px; padding: 0; border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                <i class="fas fa-eye" style="font-size: 13px;"></i>
            </button>
            <button type="button" class="btn btn-sm btn-primary btn-action" onclick="editBooth({{ $booth->id }})" title="Edit" style="width: 36px; height: 36px; padding: 0; border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                <i class="fas fa-edit" style="font-size: 13px;"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteBooth({{ $booth->id }})" title="Delete" style="width: 36px; height: 36px; padding: 0; border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
                <i class="fas fa-trash" style="font-size: 13px;"></i>
            </button>
        </div>
    </td>
</tr>
