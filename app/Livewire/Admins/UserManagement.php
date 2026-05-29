<?php

namespace App\Livewire\Admins;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admins', [
    'title' => 'Admin - User Management',
    'description' => 'Manage users, roles, and account details.',
    'keywords' => 'admin, users, management',
])]
class UserManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingUserId = null;

    public string $name = '';
    public string $email = '';
    public string $role = 'user';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        $baseRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingUserId)],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ];

        if ($this->editingUserId) {
            $baseRules['password'] = ['nullable', 'confirmed', 'min:8'];
        } else {
            $baseRules['password'] = ['required', 'confirmed', 'min:8'];
        }

        return $baseRules;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = '';
        $this->password_confirmation = '';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function saveUser(): void
    {
        $validated = $this->validate();

        if ($this->editingUserId) {
            $user = User::query()->findOrFail($this->editingUserId);

            $fullName = trim($validated['name']);
            [$fname, $lname] = $this->separateName($fullName);

            $user->name = $fullName;
            $user->fname = $fname;
            $user->lname = $lname;
            $user->email = strtolower(trim($validated['email']));
            $user->role = $validated['role'];

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            session()->flash('message', 'User updated successfully.');
        } else {
            $fullName = trim($validated['name']);
            [$fname, $lname] = $this->separateName($fullName);
            $username = $this->generateUniqueUsername($fname, $lname);

            $user = new User();
            $user->name = $fullName;
            $user->fname = $fname;
            $user->lname = $lname;
            $user->email = strtolower(trim($validated['email']));
            $user->role = $validated['role'];
            $user->username = $username;
            $user->slug = $this->generateUniqueSlug($fullName);
            $user->person_id = $this->generateUniquePersonId();
            $user->password = Hash::make($validated['password']);
            $user->save();

            session()->flash('message', 'User created successfully.');
        }

        $this->closeModal();
    }

    public function deleteUser(int $userId): void
    {
        if (auth()->id() === $userId) {
            session()->flash('message', 'You cannot delete your own account.');
            return;
        }

        $user = User::query()->findOrFail($userId);
        $user->delete();

        session()->flash('message', 'User deleted successfully.');
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->reset(['editingUserId', 'name', 'email', 'password', 'password_confirmation']);
        $this->role = 'user';
    }

    private function separateName(string $name): array
    {
        $cleanName = trim(preg_replace('/\s+/', ' ', $name) ?? '');
        $parts = explode(' ', $cleanName);

        $fname = $parts[0] ?? '';
        $lname = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        return [$fname, $lname];
    }

    private function generateUniqueUsername(string $fname, string $lname): string
    {
        $base = strtolower(substr($fname, 0, 1) . $lname);
        $base = preg_replace('/[^a-z0-9]/', '', $base) ?: 'user';

        $candidate = $base;
        $counter = 1;

        while (User::query()->where('username', $candidate)->exists()) {
            $candidate = $base . str_pad((string) $counter, 2, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $candidate;
    }

    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'user';
        $candidate = $baseSlug;
        $counter = 1;

        while (User::query()->where('slug', $candidate)->exists()) {
            $candidate = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $candidate;
    }

    private function generateUniquePersonId(): string
    {
        do {
            $candidate = (string) random_int(1000000, 9999999);
        } while (User::query()->where('person_id', $candidate)->exists());

        return $candidate;
    }

    public function render()
    {
        $users = User::query()
            ->select(['id', 'name', 'email', 'username', 'role', 'created_at'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('username', 'like', '%' . $this->search . '%')
                        ->orWhere('role', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admins.user-management', compact('users'));
    }
}
