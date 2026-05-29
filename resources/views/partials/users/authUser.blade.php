<nav class="navbar navbar-expand-md navbar-dark bg-dark stacktrace shadow-sm border-0">
    <div class="container">
        <a class="navbar-brand" href="{{ route('users.dashboard') }}">{{ $brand ?? 'SAAS' }}</a>
        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId"
            aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('users.dashboard') }}" wire:navigate>Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('services.discussion') }}" wire:navigate>
                        <i class="fa fa-folder text-info" aria-hidden="true"></i>
                        Articles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('services.calendar') }}" wire:navigate>
                        <i class="fa fa-calendar text-info" aria-hidden="true"></i>
                        Calendar
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdownId" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">Dropdown</a>
                    <div class="dropdown-menu" aria-labelledby="dropdownId">
                        <a class="dropdown-item" href="#">Action 1</a>
                        <a class="dropdown-item" href="#">Action 2</a>
                    </div>
                </li>
            </ul>
            {{-- dropdown menu for user actions --}}
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user-circle text-primary" aria-hidden="true"></i>
                        {{ Str::ucfirst(Auth::user()->fname) }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="#">Profile</a>
                        <a class="dropdown-item" href="#">Settings</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" wire:navigate href="{{ route('logout') }}">
                            Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
