<?php

namespace App\Livewire\Services;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Calendar Scheduler'])]
class Calendar extends Component
{
    public int $year;
    public int $month;

    public bool $showModal = false;
    public bool $showViewModal = false;
    public ?int $editingEventId = null;
    public ?array $viewingEvent = null;

    // Form fields
    public string $name = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $location = '';
    public string $description = '';
    public string $type = 'meeting';
    public bool $all_day = false;
    public string $organizer = '';
    public string $attendees = '';
    public string $status = 'scheduled';
    public string $event_date = '';

    protected function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'start_time'  => $this->all_day ? 'nullable' : 'required|date',
            'end_time'    => 'nullable|date',
            'location'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type'        => 'required|in:meeting,appointment,reminder',
            'organizer'   => 'nullable|string|max:255',
            'attendees'   => 'nullable|string',
            'status'      => 'required|string',
            'event_date'  => 'nullable|date',
        ];
    }

    public function mount(int $year = 0, int $month = 0): void
    {
        $now = Carbon::now();
        $this->year  = $year  ?: $now->year;
        $this->month = $month ?: $now->month;

        // Clamp month to valid range
        if ($this->month < 1 || $this->month > 12) {
            $this->month = $now->month;
        }
    }

    public function prevMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->redirect(route('services.calendar.month', ['year' => $date->year, 'month' => $date->month]));
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->redirect(route('services.calendar.month', ['year' => $date->year, 'month' => $date->month]));
    }

    public function goToToday(): void
    {
        $now = Carbon::now();
        $this->redirect(route('services.calendar.month', ['year' => $now->year, 'month' => $now->month]));
    }

    public function openCreateModal(?string $date = null): void
    {
        $this->resetForm();
        if ($date) {
            $this->event_date = $date;
            $this->start_time = $date . 'T09:00';
            $this->end_time   = $date . 'T10:00';
        }
        $this->showModal = true;
    }

    public function openEditModal(int $eventId): void
    {
        $event = Event::findOrFail($eventId);
        $this->editingEventId = $eventId;
        $this->name        = $event->name;
        $this->start_time  = Carbon::parse($event->start_time)->format('Y-m-d\TH:i');
        $this->end_time    = $event->end_time ? Carbon::parse($event->end_time)->format('Y-m-d\TH:i') : '';
        $this->location    = $event->location ?? '';
        $this->description = $event->description ?? '';
        $this->type        = $event->type;
        $this->all_day     = (bool) $event->all_day;
        $this->organizer   = $event->organizer ?? '';
        $this->attendees   = $event->attendees ?? '';
        $this->status      = $event->status;
        $this->event_date  = $event->event_date ? Carbon::parse($event->event_date)->format('Y-m-d') : '';
        $this->showModal   = true;
    }

    public function viewEvent(int $eventId): void
    {
        $event = Event::findOrFail($eventId);
        $this->viewingEvent   = $event->toArray();
        $this->showViewModal  = true;
        $this->showModal      = false;
    }

    public function saveEvent(): void
    {
        $this->validate();

        $eventDate = $this->event_date ?: ($this->start_time ? Carbon::parse($this->start_time)->toDateString() : now()->toDateString());

        // For all-day events, ensure start_time is set
        $startTime = $this->all_day
            ? Carbon::parse($eventDate)->startOfDay()->toDateTimeString()
            : $this->start_time;

        $data = [
            'name'        => $this->name,
            'start_time'  => $startTime,
            'end_time'    => $this->end_time ?: null,
            'location'    => $this->location ?: null,
            'description' => $this->description ?: null,
            'type'        => $this->type,
            'all_day'     => $this->all_day,
            'organizer'   => $this->organizer ?: null,
            'attendees'   => $this->attendees ?: null,
            'status'      => $this->status,
            'event_date'  => $eventDate,
        ];

        if ($this->editingEventId) {
            Event::findOrFail($this->editingEventId)->update($data);
            session()->flash('message', 'Event updated successfully!');
        } else {
            $data['eventsable_type'] = 'App\\Models\\User';
            $data['eventsable_id']   = Auth::id() ?? 1;
            Event::create($data);
            session()->flash('message', 'Event created successfully!');
        }

        $this->closeModal();
    }

    public function deleteEvent(int $eventId): void
    {
        Event::findOrFail($eventId)->delete();
        $this->closeModal();
        session()->flash('message', 'Event deleted.');
    }

    public function closeModal(): void
    {
        $this->showModal     = false;
        $this->showViewModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->name           = '';
        $this->start_time     = '';
        $this->end_time       = '';
        $this->location       = '';
        $this->description    = '';
        $this->type           = 'meeting';
        $this->all_day        = false;
        $this->organizer      = '';
        $this->attendees      = '';
        $this->status         = 'scheduled';
        $this->event_date     = '';
        $this->editingEventId = null;
        $this->viewingEvent   = null;
        $this->resetValidation();
    }

    public function render()
    {
        $firstDay       = Carbon::create($this->year, $this->month, 1);
        $daysInMonth    = $firstDay->daysInMonth;
        $startDayOfWeek = $firstDay->dayOfWeek;
        $monthName      = $firstDay->format('F Y');

        // Events grouped by day of month
        $byDate = Event::whereYear('event_date', $this->year)
            ->whereMonth('event_date', $this->month)
            ->get()
            ->groupBy(fn ($e) => Carbon::parse($e->event_date)->day);

        // Events with no event_date, grouped by start_time day
        $byStart = Event::whereNull('event_date')
            ->whereYear('start_time', $this->year)
            ->whereMonth('start_time', $this->month)
            ->get()
            ->groupBy(fn ($e) => Carbon::parse($e->start_time)->day);

        // Merge both collections
        foreach ($byStart as $day => $dayEvents) {
            $byDate[$day] = isset($byDate[$day])
                ? $byDate[$day]->merge($dayEvents)
                : $dayEvents;
        }

        return view('livewire.services.calendar', [
            'firstDay'       => $firstDay,
            'daysInMonth'    => $daysInMonth,
            'startDayOfWeek' => $startDayOfWeek,
            'monthName'      => $monthName,
            'events'         => $byDate,
            'today'          => Carbon::today(),
        ]);
    }
}

