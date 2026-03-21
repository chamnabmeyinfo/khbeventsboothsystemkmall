{{--
  Full clients grid: sort row + per-column filter row (fields associated with #clientsFilterToolbarForm).
  Expects: $clients, $clientsSortUrl, $clientsSortIcon (closures), $sortBy, $sortDir
--}}
@php
    $f = fn (string $key) => request()->input('filter.'.$key, '');
    $clientTableColspan = 17;
@endphp
<div class="clients-table-scroll looker-table-wrapper">
    <table class="looker-table mb-0 clients-density-default clients-table-fluid clients-table-resizable" id="clientsDataTable">
        <colgroup id="clientsDataTableColgroup">
            <col data-clients-col-idx="0" style="width: 44px;" />
            <col data-clients-col-idx="1" />
            <col data-clients-col-idx="2" />
            <col data-clients-col-idx="3" />
            <col data-clients-col-idx="4" />
            <col data-clients-col-idx="5" />
            <col data-clients-col-idx="6" />
            <col data-clients-col-idx="7" />
            <col data-clients-col-idx="8" />
            <col data-clients-col-idx="9" />
            <col data-clients-col-idx="10" />
            <col data-clients-col-idx="11" />
            <col data-clients-col-idx="12" />
            <col data-clients-col-idx="13" />
            <col data-clients-col-idx="14" />
            <col data-clients-col-idx="15" />
            <col data-clients-col-idx="16" style="min-width: 108px;" />
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2" class="align-middle text-center">
                    <input type="checkbox" id="selectAllClients" class="form-check-input" aria-label="Select all on this page">
                </th>
                <th class="align-top" data-clients-col="id">
                    <a href="{{ $clientsSortUrl('id') }}" class="clients-sort-link">ID {!! $clientsSortIcon('id') !!}</a>
                </th>
                <th class="align-top" data-clients-col="company">
                    <a href="{{ $clientsSortUrl('company') }}" class="clients-sort-link">Company {!! $clientsSortIcon('company') !!}</a>
                </th>
                <th class="align-top" data-clients-col="company_name_khmer">
                    <a href="{{ $clientsSortUrl('company_name_khmer') }}" class="clients-sort-link">Co. (KM) {!! $clientsSortIcon('company_name_khmer') !!}</a>
                </th>
                <th class="align-top" data-clients-col="name">
                    <a href="{{ $clientsSortUrl('name') }}" class="clients-sort-link">Name {!! $clientsSortIcon('name') !!}</a>
                </th>
                <th class="align-top" data-clients-col="sex">
                    <a href="{{ $clientsSortUrl('sex') }}" class="clients-sort-link">Gender {!! $clientsSortIcon('sex') !!}</a>
                </th>
                <th class="align-top" data-clients-col="position">
                    <a href="{{ $clientsSortUrl('position') }}" class="clients-sort-link">Position {!! $clientsSortIcon('position') !!}</a>
                </th>
                <th class="align-top" data-clients-col="phone_number">
                    <a href="{{ $clientsSortUrl('phone_number') }}" class="clients-sort-link">Phone {!! $clientsSortIcon('phone_number') !!}</a>
                </th>
                <th class="align-top" data-clients-col="phone_1">
                    <a href="{{ $clientsSortUrl('phone_1') }}" class="clients-sort-link">Phone 1 {!! $clientsSortIcon('phone_1') !!}</a>
                </th>
                <th class="align-top" data-clients-col="email">
                    <a href="{{ $clientsSortUrl('email') }}" class="clients-sort-link">Email {!! $clientsSortIcon('email') !!}</a>
                </th>
                <th class="align-top" data-clients-col="address">
                    <a href="{{ $clientsSortUrl('address') }}" class="clients-sort-link">Address {!! $clientsSortIcon('address') !!}</a>
                </th>
                <th class="align-top" data-clients-col="tax_id">
                    <a href="{{ $clientsSortUrl('tax_id') }}" class="clients-sort-link">Tax ID {!! $clientsSortIcon('tax_id') !!}</a>
                </th>
                <th class="align-top" data-clients-col="website">
                    <a href="{{ $clientsSortUrl('website') }}" class="clients-sort-link">Website {!! $clientsSortIcon('website') !!}</a>
                </th>
                <th class="align-top" data-clients-col="notes">
                    <a href="{{ $clientsSortUrl('notes') }}" class="clients-sort-link">Notes {!! $clientsSortIcon('notes') !!}</a>
                </th>
                <th class="align-top" data-clients-col="booths_count">
                    <a href="{{ $clientsSortUrl('booths_count') }}" class="clients-sort-link">Booths {!! $clientsSortIcon('booths_count') !!}</a>
                </th>
                <th class="align-top" data-clients-col="books_count">
                    <a href="{{ $clientsSortUrl('books_count') }}" class="clients-sort-link">Bookings {!! $clientsSortIcon('books_count') !!}</a>
                </th>
                <th rowspan="2" class="align-middle">Actions</th>
            </tr>
            <tr class="clients-filter-row">
                <th data-clients-col="id">
                    <label class="visually-hidden" for="flt_id">Filter by ID</label>
                    <input type="text" inputmode="numeric" class="form-control form-control-sm" id="flt_id" name="filter[id]" form="clientsFilterToolbarForm" value="{{ $f('id') }}" placeholder="ID" autocomplete="off">
                </th>
                <th>
                    <label class="visually-hidden" for="flt_company">Filter company</label>
                    <input type="text" class="form-control form-control-sm" id="flt_company" name="filter[company]" form="clientsFilterToolbarForm" value="{{ $f('company') }}" placeholder="Contains…" autocomplete="organization">
                </th>
                <th data-clients-col="company_name_khmer">
                    <label class="visually-hidden" for="flt_company_kh">Filter Khmer company</label>
                    <input type="text" class="form-control form-control-sm" id="flt_company_kh" name="filter[company_name_khmer]" form="clientsFilterToolbarForm" value="{{ $f('company_name_khmer') }}" placeholder="Contains…" autocomplete="off">
                </th>
                <th data-clients-col="name">
                    <label class="visually-hidden" for="flt_name">Filter name</label>
                    <input type="text" class="form-control form-control-sm" id="flt_name" name="filter[name]" form="clientsFilterToolbarForm" value="{{ $f('name') }}" placeholder="Contains…" autocomplete="name">
                </th>
                <th>
                    <label class="sr-only" for="flt_sex">Filter gender</label>
                    <select class="form-select form-select-sm" id="flt_sex" name="filter[sex]" form="clientsFilterToolbarForm">
                        <option value="">All</option>
                        <option value="1" {{ (string) $f('sex') === '1' ? 'selected' : '' }}>Male</option>
                        <option value="2" {{ (string) $f('sex') === '2' ? 'selected' : '' }}>Female</option>
                        <option value="3" {{ (string) $f('sex') === '3' ? 'selected' : '' }}>Other</option>
                    </select>
                </th>
                <th data-clients-col="position">
                    <label class="visually-hidden" for="flt_position">Filter position</label>
                    <input type="text" class="form-control form-control-sm" id="flt_position" name="filter[position]" form="clientsFilterToolbarForm" value="{{ $f('position') }}" placeholder="Contains…" autocomplete="off">
                </th>
                <th data-clients-col="phone_number">
                    <label class="visually-hidden" for="flt_phone">Filter phone</label>
                    <input type="text" class="form-control form-control-sm" id="flt_phone" name="filter[phone_number]" form="clientsFilterToolbarForm" value="{{ $f('phone_number') }}" placeholder="Contains…" autocomplete="off">
                </th>
                <th data-clients-col="phone_1">
                    <input type="text" class="form-control form-control-sm" name="filter[phone_1]" form="clientsFilterToolbarForm" value="{{ $f('phone_1') }}" placeholder="Contains…" autocomplete="off" aria-label="Filter phone 1">
                </th>
                <th data-clients-col="email">
                    <input type="text" class="form-control form-control-sm" name="filter[email]" form="clientsFilterToolbarForm" value="{{ $f('email') }}" placeholder="Contains…" autocomplete="off" aria-label="Filter email">
                </th>
                <th data-clients-col="address">
                    <input type="text" class="form-control form-control-sm" name="filter[address]" form="clientsFilterToolbarForm" value="{{ $f('address') }}" placeholder="Contains…" autocomplete="street-address" aria-label="Filter address">
                </th>
                <th data-clients-col="tax_id">
                    <input type="text" class="form-control form-control-sm" name="filter[tax_id]" form="clientsFilterToolbarForm" value="{{ $f('tax_id') }}" placeholder="Contains…" autocomplete="off" aria-label="Filter tax id">
                </th>
                <th data-clients-col="website">
                    <input type="text" class="form-control form-control-sm" name="filter[website]" form="clientsFilterToolbarForm" value="{{ $f('website') }}" placeholder="Contains…" autocomplete="off" aria-label="Filter website">
                </th>
                <th data-clients-col="notes">
                    <input type="text" class="form-control form-control-sm" name="filter[notes]" form="clientsFilterToolbarForm" value="{{ $f('notes') }}" placeholder="Contains…" autocomplete="off" aria-label="Filter notes">
                </th>
                <th data-clients-col="booths_count">
                    <label class="visually-hidden" for="flt_booths">Minimum booths</label>
                    <input type="number" min="1" class="form-control form-control-sm" id="flt_booths" name="filter[booths_count]" form="clientsFilterToolbarForm" value="{{ $f('booths_count') }}" placeholder="Min ≥" inputmode="numeric">
                </th>
                <th data-clients-col="books_count">
                    <label class="visually-hidden" for="flt_books">Minimum bookings</label>
                    <input type="number" min="1" class="form-control form-control-sm" id="flt_books" name="filter[books_count]" form="clientsFilterToolbarForm" value="{{ $f('books_count') }}" placeholder="Min ≥" inputmode="numeric">
                </th>
            </tr>
        </thead>
        <tbody id="tableClientsBody">
            @forelse($clients as $client)
                @include('clients.partials.table-row', ['client' => $client])
            @empty
                <tr>
                    <td colspan="{{ $clientTableColspan }}" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-users-slash fa-3x mb-3" aria-hidden="true"></i>
                            <p class="mb-0">No clients match your filters.</p>
                            <button type="button" class="action-btn action-btn-primary mt-3" onclick="showCreateClientModal()">
                                <i class="fas fa-plus" aria-hidden="true"></i> Create first client
                            </button>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
