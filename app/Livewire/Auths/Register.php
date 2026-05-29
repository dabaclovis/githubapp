<?php

namespace App\Livewire\Auths;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rules\Password;

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
    public function rules()
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
    public function messages()
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
    public function separateName()
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
    public function createUsername($fname, $lname)
    {
        $username = strtolower(substr($fname, 0, 1) . $lname . '01');
        // Remove spaces and special characters from username
        $username = preg_replace('/[^a-z0-9]/', '', $username);
        return $username;
    }

    /**
     * Create unique 7 digit number for each user
     * @return int
     */
    public function createUniqueNumber()
    {
        return rand(1000000, 9999999);
    }
    // generate unique slug for user
    public function createSlug($name)
    {
        $slug = strtolower(trim(preg_replace('/\s+/', '-', $name)));
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        $existingSlugCount = \App\Models\User::where('slug', 'like', $slug . '%')->count();
        return $existingSlugCount > 0 ? $slug . '-' . ($existingSlugCount + 1) : $slug;
    }

    /**
     * Handle registration logic
     */
    public function register()
    {
        $this->validate($this->rules(), $this->messages());

        [$fname, $lname] = $this->separateName();

        \App\Models\User::create([
            'name' => $fname . ' ' . $lname,
            'fname' => $fname,
            'lname' => $lname,
            'slug' => $this->createSlug($fname . ' ~' . $lname),
            'person_id' => $this->createUniqueNumber(),
            'username' => $this->createUsername($fname, $lname),
            'email' => strtolower(trim($this->email)),
            'password' => bcrypt($this->password),
        ]);

        session()->flash('message', 'Registration successful! You can now log in.');
        return redirect()->route('auth.login');
    }

    /**
     * Render the registration view
     */
    public function render()
    {
        return view('livewire.auths.register');
    }
}
