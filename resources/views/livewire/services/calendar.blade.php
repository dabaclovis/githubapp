<div>
    <style>
        .cal-cell {
            min-height: 110px;
            cursor: pointer;
            transition: background 0.15s;
            vertical-align: top;
        }
        .cal-cell:hover { background: #f0f7ff; }
        .cal-cell.today-cell { background: #e8f4fd; }
        .cal-cell.blank-cell { background: #fafafa; cursor: default; }
        .event-pill {
            display: block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.72rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #fff;
            margin-bottom: 2px;
            cursor: pointer;
        }
        .pill-meeting     { background: #007bff; }
        .pill-appointment { background: #28a745; }
        .pill-reminder    { background: #fd7e14; }
        .day-num { font-size: 0.85rem; font-weight: 600; }
        .today-num {
            background: #007bff;
            color: #fff;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-box {
            background: #fff;
            border-radius: 8px;
            width: 620px;
            max-width: 96vw;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .modal-body-scroll { overflow-y: auto; flex: 1; }
        .legend-dot {
            width: 12px; height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
    </style>

    {{-- Flash message --}}
    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Calendar Card --}}
    <div class="card shadow-sm mt-3">
        {{-- Header --}}
        <div class="card-header d-flex justify-content-between align-items-center" style="background:#1a73e8;">
            <div class="d-flex align-items-center">
                <button wire:click="prevMonth" class="btn btn-sm btn-light mr-2">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button wire:click="nextMonth" class="btn btn-sm btn-light mr-2">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <h5 class="mb-0 text-white font-weight-bold">{{ $monthName }}</h5>
                <button wire:click="goToToday" class="btn btn-sm btn-outline-light ml-3" style="font-size:0.8rem;">Today</button>
            </div>
            <button wire:click="openCreateModal" class="btn btn-sm btn-light text-primary font-weight-bold">
                <i class="fas fa-plus mr-1"></i> New Event
            </button>
        </div>

        {{-- Legend --}}
        <div class="px-3 py-2 border-bottom d-flex align-items-center" style="gap:16px; font-size:0.8rem; color:#555;">
            <span><span class="legend-dot" style="background:#007bff;"></span> Meeting</span>
            <span><span class="legend-dot" style="background:#28a745;"></span> Appointment</span>
            <span><span class="legend-dot" style="background:#fd7e14;"></span> Reminder</span>
        </div>

        {{-- Day-of-week headers --}}
        <div class="row no-gutters border-bottom">
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dayLabel)
                <div class="col text-center py-2 border-right bg-light" style="font-size:0.8rem; font-weight:600; color:#666;">
                    {{ $dayLabel }}
                </div>
            @endforeach
        </div>

        {{-- Calendar Grid --}}
        @php $dayCount = 1; @endphp
        @for($row = 0; $row < 6; $row++)
            @if($dayCount > $daysInMonth) @break @endif
            <div class="row no-gutters">
                @for($col = 0; $col < 7; $col++)
                    @php
                        $isBlank = ($row === 0 && $col < $startDayOfWeek) || $dayCount > $daysInMonth;
                        $currentDay = $isBlank ? null : $dayCount;
                        $isToday = $currentDay &&
                            $today->year == $year &&
                            $today->month == $month &&
                            $today->day == $currentDay;
                        $dayEvents = $currentDay ? ($events[$currentDay] ?? collect()) : collect();
                        if (!$isBlank) $dayCount++;
                        $dateStr = $currentDay ? sprintf('%04d-%02d-%02d', $year, $month, $currentDay) : '';
                    @endphp
                    <div class="col border-right border-bottom cal-cell {{ $isToday ? 'today-cell' : '' }} {{ $isBlank ? 'blank-cell' : '' }}"
                         @if($currentDay) wire:click="openCreateModal('{{ $dateStr }}')" @endif>
                        @if($currentDay)
                            <div class="p-1">
                                <span class="day-num {{ $isToday ? 'today-num' : '' }}">{{ $currentDay }}</span>
                                <div class="mt-1">
                                    @foreach($dayEvents->take(3) as $event)
                                        <span class="event-pill pill-{{ $event->type }}"
                                              wire:click.stop="viewEvent({{ $event->id }})"
                                              title="{{ $event->name }}">
                                            {{ $event->name }}
                                        </span>
                                    @endforeach
                                    @if($dayEvents->count() > 3)
                                        <span style="font-size:0.7rem; color:#888;">+{{ $dayEvents->count() - 3 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        @endfor
    </div>

    {{-- ===== Create / Edit Modal ===== --}}
    @if($showModal)
    <div class="modal-overlay" wire:click.self="closeModal">
        <div class="modal-box">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom" style="background:#1a73e8; border-radius:8px 8px 0 0;">
                <h6 class="mb-0 text-white font-weight-bold">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    {{ $editingEventId ? 'Edit Event' : 'New Event' }}
                </h6>
                <button wire:click="closeModal" class="btn btn-sm btn-light">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body-scroll p-4">
                <form wire:submit="saveEvent">
                    {{-- Name --}}
                    <div class="form-group">
                        <label class="font-weight-bold" style="font-size:0.85rem;">Event Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                               wire:model="name" placeholder="e.g. Team Standup">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Type & Status --}}
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label class="font-weight-bold" style="font-size:0.85rem;">Type <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm @error('type') is-invalid @enderror" wire:model="type">
                                <option value="meeting">📋 Meeting</option>
                                <option value="appointment">📅 Appointment</option>
                                <option value="reminder">🔔 Reminder</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-6">
                            <label class="font-weight-bold" style="font-size:0.85rem;">Status</label>
                            <select class="form-control form-control-sm" wire:model="status">
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div class="form-group">
                        <label class="font-weight-bold" style="font-size:0.85rem;">Event Date</label>
                        <input type="date" class="form-control form-control-sm @error('event_date') is-invalid @enderror"
                               wire:model="event_date">
                        @error('event_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- All Day --}}
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="all_day" wire:model="all_day">
                            <label class="custom-control-label" for="all_day" style="font-size:0.85rem;">All Day Event</label>
                        </div>
                    </div>

                    {{-- Start & End Time --}}
                    @if(!$all_day)
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label class="font-weight-bold" style="font-size:0.85rem;">Start Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control form-control-sm @error('start_time') is-invalid @enderror"
                                   wire:model="start_time">
                            @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-6">
                            <label class="font-weight-bold" style="font-size:0.85rem;">End Time</label>
                            <input type="datetime-local" class="form-control form-control-sm @error('end_time') is-invalid @enderror"
                                   wire:model="end_time">
                            @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    @else
                    {{-- Hidden start_time for all-day (required by validation) --}}
                    <input type="hidden" wire:model="start_time">
                    @endif

                    {{-- Location --}}
                    <div class="form-group">
                        <label class="font-weight-bold" style="font-size:0.85rem;">Location</label>
                        <input type="text" class="form-control form-control-sm" wire:model="location"
                               placeholder="Room, address or video link">
                    </div>

                    {{-- Organizer & Attendees --}}
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label class="font-weight-bold" style="font-size:0.85rem;">Organizer</label>
                            <input type="text" class="form-control form-control-sm" wire:model="organizer"
                                   placeholder="Name or email">
                        </div>
                        <div class="form-group col-6">
                            <label class="font-weight-bold" style="font-size:0.85rem;">Attendees</label>
                            <input type="text" class="form-control form-control-sm" wire:model="attendees"
                                   placeholder="Comma-separated names/emails">
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="form-group">
                        <label class="font-weight-bold" style="font-size:0.85rem;">Description</label>
                        <textarea class="form-control form-control-sm" wire:model="description" rows="3"
                                  placeholder="Optional notes or agenda..."></textarea>
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                        @if($editingEventId)
                            <button type="button"
                                    wire:click="deleteEvent({{ $editingEventId }})"
                                    wire:confirm="Are you sure you want to delete this event?"
                                    class="btn btn-sm btn-danger">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        @else
                            <div></div>
                        @endif
                        <div>
                            <button type="button" wire:click="closeModal" class="btn btn-sm btn-secondary mr-2">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                {{ $editingEventId ? 'Update' : 'Save Event' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== View Event Modal ===== --}}
    @if($showViewModal && $viewingEvent)
    @php
        $typeColors = ['meeting' => '#007bff', 'appointment' => '#28a745', 'reminder' => '#fd7e14'];
        $statusColors = ['scheduled' => 'primary', 'completed' => 'success', 'cancelled' => 'danger', 'pending' => 'warning'];
        $typeIcons = ['meeting' => 'fa-users', 'appointment' => 'fa-calendar-check', 'reminder' => 'fa-bell'];
        $vType = $viewingEvent['type'] ?? 'meeting';
        $vStatus = $viewingEvent['status'] ?? 'scheduled';
    @endphp
    <div class="modal-overlay" wire:click.self="closeModal">
        <div class="modal-box" style="max-width: 520px;">
            <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center"
                 style="background: {{ $typeColors[$vType] ?? '#007bff' }}; border-radius:8px 8px 0 0;">
                <div class="text-white">
                    <i class="fas {{ $typeIcons[$vType] ?? 'fa-calendar' }} mr-2"></i>
                    <strong>{{ ucfirst($vType) }}</strong>
                </div>
                <button wire:click="closeModal" class="btn btn-sm btn-light"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-4">
                <h5 class="font-weight-bold mb-3">{{ $viewingEvent['name'] }}</h5>

                <div class="mb-2">
                    <span class="badge badge-{{ $statusColors[$vStatus] ?? 'primary' }} mb-2">{{ ucfirst($vStatus) }}</span>
                </div>

                @if($viewingEvent['event_date'])
                <p class="mb-2 text-muted" style="font-size:0.9rem;">
                    <i class="fas fa-calendar mr-2 text-primary"></i>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($viewingEvent['event_date'])->format('D, M j, Y') }}
                </p>
                @endif

                @if(!$viewingEvent['all_day'])
                <p class="mb-2 text-muted" style="font-size:0.9rem;">
                    <i class="fas fa-clock mr-2 text-primary"></i>
                    <strong>Time:</strong>
                    {{ \Carbon\Carbon::parse($viewingEvent['start_time'])->format('g:i A') }}
                    @if($viewingEvent['end_time'])
                        – {{ \Carbon\Carbon::parse($viewingEvent['end_time'])->format('g:i A') }}
                    @endif
                </p>
                @else
                <p class="mb-2 text-muted" style="font-size:0.9rem;">
                    <i class="fas fa-sun mr-2 text-warning"></i> All Day Event
                </p>
                @endif

                @if($viewingEvent['location'])
                <p class="mb-2 text-muted" style="font-size:0.9rem;">
                    <i class="fas fa-map-marker-alt mr-2 text-danger"></i>
                    <strong>Location:</strong> {{ $viewingEvent['location'] }}
                </p>
                @endif

                @if($viewingEvent['organizer'])
                <p class="mb-2 text-muted" style="font-size:0.9rem;">
                    <i class="fas fa-user mr-2 text-info"></i>
                    <strong>Organizer:</strong> {{ $viewingEvent['organizer'] }}
                </p>
                @endif

                @if($viewingEvent['attendees'])
                <p class="mb-2 text-muted" style="font-size:0.9rem;">
                    <i class="fas fa-users mr-2 text-secondary"></i>
                    <strong>Attendees:</strong> {{ $viewingEvent['attendees'] }}
                </p>
                @endif

                @if($viewingEvent['description'])
                <div class="mt-3 p-3 bg-light rounded" style="font-size:0.9rem;">
                    <strong><i class="fas fa-align-left mr-1"></i> Notes:</strong>
                    <p class="mb-0 mt-1 text-muted">{{ $viewingEvent['description'] }}</p>
                </div>
                @endif

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button wire:click="deleteEvent({{ $viewingEvent['id'] }})"
                            wire:confirm="Are you sure you want to delete this event?"
                            class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                    <div>
                        <button wire:click="closeModal" class="btn btn-sm btn-secondary mr-2">Close</button>
                        <button wire:click="openEditModal({{ $viewingEvent['id'] }})" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

