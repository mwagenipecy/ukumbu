<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class Index extends Component
{
    use WithPagination;

    // Search and filter properties
    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;
    
    // Bulk actions
    public array $selectedUsers = [];
    public bool $selectAll = false;
    public bool $showBulkModal = false;
    
    // User creation/editing
    public bool $showUserModal = false;
    public bool $isEditMode = false;
    public ?User $editingUser = null;
    
    // Form fields for user modal
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirmation = '';
    public string $role = 'client';
    public bool $emailVerified = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:client,vendor,admin'],
        ];

        if ($this->isEditMode) {
            $rules['email'][] = 'unique:users,email,' . $this->editingUser->id;
            if (!empty($this->password)) {
                $rules['password'] = ['min:8', 'confirmed'];
                $rules['passwordConfirmation'] = ['required'];
            }
        } else {
            $rules['email'][] = 'unique:users,email';
            $rules['password'] = ['required', 'min:8', 'confirmed'];
            $rules['passwordConfirmation'] = ['required'];
        }

        return $rules;
    }

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'roleFilter', 'statusFilter']);
        $this->resetPage();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedUsers = $this->getFilteredUsers()->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function openUserModal($userId = null)
    {
        if ($userId) {
            $this->editingUser = User::findOrFail($userId);
            $this->isEditMode = true;
            $this->name = $this->editingUser->name;
            $this->email = $this->editingUser->email;
            $this->role = $this->editingUser->role;
            $this->emailVerified = !is_null($this->editingUser->email_verified_at);
        } else {
            $this->isEditMode = false;
            $this->editingUser = null;
        }
        
        $this->showUserModal = true;
    }

    public function closeUserModal()
    {
        $this->showUserModal = false;
        $this->reset(['name', 'email', 'password', 'passwordConfirmation', 'role', 'emailVerified', 'isEditMode', 'editingUser']);
    }

    public function saveUser()
    {
        $this->validate();

        try {
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'email_verified_at' => $this->emailVerified ? now() : null,
            ];

            if (!$this->isEditMode || !empty($this->password)) {
                $userData['password'] = Hash::make($this->password);
            }

            if ($this->isEditMode) {
                $this->editingUser->update($userData);
                session()->flash('success', 'User updated successfully.');
            } else {
                User::create($userData);
                session()->flash('success', 'User created successfully.');
            }

            $this->closeUserModal();
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while saving the user.');
        }
    }

    public function toggleBulkModal()
    {
        $this->showBulkModal = !$this->showBulkModal;
    }

    public function bulkDelete()
    {
        // Prevent deletion of current user
        $currentUserId = auth()->id();
        $usersToDelete = array_filter($this->selectedUsers, fn($id) => $id != $currentUserId);
        
        User::whereIn('id', $usersToDelete)->delete();
        
        $this->selectedUsers = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', count($usersToDelete) . ' users deleted successfully.');
    }

    public function bulkVerifyEmail()
    {
        User::whereIn('id', $this->selectedUsers)
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
        
        $this->selectedUsers = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', 'Selected users email verified successfully.');
    }

    public function bulkChangeRole($newRole)
    {
        User::whereIn('id', $this->selectedUsers)->update(['role' => $newRole]);
        
        $this->selectedUsers = [];
        $this->selectAll = false;
        $this->showBulkModal = false;
        
        session()->flash('success', 'Selected users role updated to ' . ucfirst($newRole) . ' successfully.');
    }

    public function deleteUser($userId)
    {
        if ($userId == auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $user = User::findOrFail($userId);
        $userName = $user->name;
        
        $user->delete();
        
        session()->flash('success', "User '{$userName}' has been deleted successfully.");
    }

    public function toggleEmailVerification($userId)
    {
        $user = User::findOrFail($userId);
        
        if ($user->email_verified_at) {
            $user->update(['email_verified_at' => null]);
            session()->flash('success', "Email verification removed for {$user->name}.");
        } else {
            $user->update(['email_verified_at' => now()]);
            session()->flash('success', "Email verified for {$user->name}.");
        }
    }

    public function impersonateUser($userId)
    {
        if ($userId == auth()->id()) {
            session()->flash('error', 'You cannot impersonate yourself.');
            return;
        }

        $user = User::findOrFail($userId);
        
        // Store original user ID in session for later restoration
        session(['impersonator' => auth()->id()]);
        auth()->login($user);
        
        return redirect()->route('dashboard')->with('success', "Now impersonating {$user->name}");
    }

    private function getFilteredUsers()
    {
        return User::query()
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function (Builder $query) {
                $query->where('role', $this->roleFilter);
            })
            ->when($this->statusFilter, function (Builder $query) {
                if ($this->statusFilter === 'verified') {
                    $query->whereNotNull('email_verified_at');
                } elseif ($this->statusFilter === 'unverified') {
                    $query->whereNull('email_verified_at');
                }
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $users = $this->getFilteredUsers()->paginate($this->perPage);
        
        $stats = [
            'total' => User::count(),
            'clients' => User::where('role', 'client')->count(),
            'vendors' => User::where('role', 'vendor')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'this_month' => User::whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year)
                              ->count(),
        ];

        return view('livewire.admin.user.index', [
            'users' => $users,
            'stats' => $stats,
        ]);
    }

    public function getRoleColor($role)
    {
        return match($role) {
            'admin' => 'bg-red-100 text-red-800',
            'vendor' => 'bg-blue-100 text-blue-800',
            'client' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getRoleIcon($role)
    {
        return match($role) {
            'admin' => 'fas fa-user-shield',
            'vendor' => 'fas fa-store',
            'client' => 'fas fa-user',
            default => 'fas fa-question-circle',
        };
    }

    public function getUserInitials($name)
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    public function getUserAvatarColor($userId)
    {
        $colors = [
            'bg-blue-100 text-blue-600',
            'bg-purple-100 text-purple-600',
            'bg-green-100 text-green-600',
            'bg-yellow-100 text-yellow-600',
            'bg-pink-100 text-pink-600',
            'bg-indigo-100 text-indigo-600',
        ];
        
        return $colors[$userId % count($colors)];
    }
}