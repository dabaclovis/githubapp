<?php

namespace App\Livewire\Auths;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout(
    'components.layouts.app',
    [
        'title' => 'Register - Create a new account',
        'description' => 'Join us today! Create a new account to access exclusive features and stay connected.',
        'keywords' => 'register, sign up, create account, join',
    ]
)]

/**
 * Livewire Register Component
 * Handles user registration logic and validation.
 */
class Register extends Component
{
    /** @var string User's full name */
    public $name = '';

    /** @var string User's email address */
    public $email = '';

    /** @var string User's password */
    public $password = '';

    /** @var string Password confirmation */
    public $password_confirmation = '';

    /**
     * Validation rules for registration
     * @var array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->letters()->numbers()->symbols()],
        ];
    }
    /**
     * Custom validation messages
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    /**
     * Trim name and separate into first and last name (handles multiple spaces and middle names)
     * @return array [firstName, lastName]
     */
    public function separateName(): array
    {
        $name = trim(preg_replace('/\s+/', ' ', $this->name));
        $nameParts = explode(' ', $name);
        $fname = $nameParts[0] ?? '';
        $lname = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
        return [$fname, $lname];
    }

    /**
     * Create username from first letter of fname and all of lname + '01'
     * @param string $fname
     * @param string $lname
     * @return string
     */
    public function createUsername(string $fname, string $lname): string
    {
        $username = strtolower(substr($fname, 0, 1) . $lname . '01');
        // Remove spaces and special characters from username
        return preg_replace('/[^a-z0-9]/', '', $username) ?: 'user01';
    }

    /**
     * Create unique 7 digit number for each user
     * @return int
     */
    public function createUniqueNumber(): string
    {
        do {
            $number = (string) random_int(1000000, 9999999);
        } while (User::query()->where('person_id', $number)->exists());

        return $number;
    }

    public function createUniqueUsername(string $baseUsername): string
    {
        $baseUsername = Str::lower(trim($baseUsername));
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername) ?: 'user';
        $candidate = $baseUsername;
        $counter = 1;

        while (User::query()->where('username', $candidate)->exists()) {
            $candidate = $baseUsername . str_pad((string) $counter, 2, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $candidate;
    }

    public function createUniqueSlug(string $name): string
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

    /**
     * Handle registration logic
     */
    public function register(): void
    {
        $this->validate($this->rules(), $this->messages());

        [$fname, $lname] = $this->separateName();
        $fullName = trim($fname . ' ' . $lname);
        $username = $this->createUniqueUsername($this->createUsername($fname, $lname));
        $slug = $this->createUniqueSlug($fullName);

        User::create([
            'name' => $fullName,
            'fname' => $fname,
            'lname' => $lname,
            'slug' => $slug,
            'person_id' => $this->createUniqueNumber(),
            'username' => $username,
            'email' => strtolower(trim($this->email)),
            'password' => Hash::make($this->password),
        ]);

        session()->flash('message', 'Registration successful! You can now log in.');
        $this->redirect(route('auth.login'), navigate: true);
    }

    /**
     * Render the registration view
     */
    public function render()
    {
        return view('livewire.auths.register');
    }
}
