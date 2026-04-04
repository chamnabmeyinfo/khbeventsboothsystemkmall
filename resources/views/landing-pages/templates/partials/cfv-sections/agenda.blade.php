<section class="lv-section lv-section--agenda" data-lp-section="agenda" aria-labelledby="lvAgendaHeading">
    <div class="lv-container">
        <h2 id="lvAgendaHeading" data-lv-key="agenda_title">{{ $agendaTitle }}</h2>
        @if(!empty($agendaDayBundle['use_tabs']))
            <div class="lv-agenda-tabs" role="region" aria-label="{{ $agendaTitle }}">
                @foreach($agendaDayBundle['groups'] as $ti => $_tabGroup)
                    <input class="lv-agenda-tab-input" type="radio" name="lv-agenda-itinerary-{{ $landingPage->id }}" id="lv-agenda-{{ $landingPage->id }}-{{ $ti }}" {{ $ti === 0 ? 'checked' : '' }}>
                @endforeach
                <div class="lv-agenda-tablist" role="tablist">
                    @foreach($agendaDayBundle['groups'] as $ti => $tabGroup)
                        <label class="lv-agenda-tab" for="lv-agenda-{{ $landingPage->id }}-{{ $ti }}" id="lv-agenda-tablabel-{{ $landingPage->id }}-{{ $ti }}" role="tab">{{ $tabGroup['label'] }}</label>
                    @endforeach
                </div>
                <div class="lv-agenda-panels">
                    @foreach($agendaDayBundle['groups'] as $ti => $tabGroup)
                        <div class="lv-agenda-panel" role="tabpanel" aria-labelledby="lv-agenda-tablabel-{{ $landingPage->id }}-{{ $ti }}">
                            <div class="lv-agenda-table-wrap">
                                <table class="lv-agenda-table">
                                    <caption class="sr-only">{{ $agendaTitle }} — {{ $tabGroup['label'] }}</caption>
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ $agendaHdrSlot }}</th>
                                            <th scope="col">{{ $agendaHdrActivity }}</th>
                                            <th scope="col">{{ $agendaHdrDetail }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tabGroup['rows'] as $row)
                                            @php
                                                $slot = trim((string) ($row['slot'] ?? ''));
                                                $act = trim((string) ($row['activity'] ?? ''));
                                                $det = trim((string) ($row['detail'] ?? ''));
                                            @endphp
                                            <tr>
                                                <td class="lv-agenda-slot">{{ $slot !== '' ? $slot : '—' }}</td>
                                                <td class="lv-agenda-activity">{{ $act !== '' ? $act : '—' }}</td>
                                                <td class="lv-agenda-detail">{{ $det !== '' ? $det : '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="lv-agenda-table-wrap">
                <table class="lv-agenda-table">
                    <caption class="sr-only">{{ $agendaTitle }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ $agendaHdrSlot }}</th>
                            <th scope="col">{{ $agendaHdrActivity }}</th>
                            <th scope="col">{{ $agendaHdrDetail }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agendaItems as $row)
                            @php
                                $slot = trim((string) ($row['slot'] ?? ''));
                                $act = trim((string) ($row['activity'] ?? ''));
                                $det = trim((string) ($row['detail'] ?? ''));
                            @endphp
                            <tr>
                                <td class="lv-agenda-slot">{{ $slot !== '' ? $slot : '—' }}</td>
                                <td class="lv-agenda-activity">{{ $act !== '' ? $act : '—' }}</td>
                                <td class="lv-agenda-detail">{{ $det !== '' ? $det : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
