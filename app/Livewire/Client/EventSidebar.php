<?php

namespace App\Livewire\Client;

use App\Models\Event;
use App\Models\BankAccount;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class EventSidebar extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $showBankModal = false;
    public $title = '';
    public $description = '';
    public $start_date = '';
    public $end_date = '';
    public $location_name = '';
    public $status = 'draft';

    public $bank_name = '';
    public $account_name = '';
    public $account_number = '';
    public $branch_name = '';
    public $swift_code = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:2000',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'location_name' => 'nullable|string|max:255',
        'status' => 'required|in:draft,published,cancelled',
    ];

    public function getMyEventsProperty()
    {
        return Event::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
    }

    public function openCreateModal()
    {
        $this->reset(['title','description','start_date','end_date','location_name','status']);
        $this->status = 'draft';
        $this->showCreateModal = true;
    }

    public function createEvent()
    {
        $this->validate();

        $event = Event::create([
            'user_id' => Auth::id(),
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
            'location_name' => $this->location_name ?: null,
            'status' => $this->status,
        ]);

        $this->showCreateModal = false;
        $this->openBankModal();
        session()->flash('success', 'Event created. Next, add your bank account.');
    }

    public function openBankModal()
    {
        $this->reset(['bank_name','account_name','account_number','branch_name','swift_code']);
        $this->showBankModal = true;
    }

    public function saveBankAccount()
    {
        $this->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:60',
            'branch_name' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:60',
        ]);

        BankAccount::create([
            'user_id' => Auth::id(),
            'bank_name' => $this->bank_name,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'branch_name' => $this->branch_name ?: null,
            'swift_code' => $this->swift_code ?: null,
            'status' => 'unverified',
        ]);

        $this->showBankModal = false;
        session()->flash('success', 'Bank account saved.');
    }

    public function render()
    {
        return view('livewire.client.event-sidebar', [
            'events' => $this->myEvents,
        ]);
    }
}
