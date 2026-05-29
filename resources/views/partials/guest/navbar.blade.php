<nav @class(["navbar", "navbar-expand-md" , "navbar-dark" , "bg-dark" ])>
    <div @class(["container-fluid"])>
        <a @class(["navbar-brand"]) href="{{ route('pages.index') }}" wire:navigate>SAAS</a>
        <button @class(["navbar-toggler", "d-lg-none" ]) type="button" data-toggle="collapse"
            data-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false"
            aria-label="Toggle navigation">
            <span @class(["navbar-toggler-icon"])></span>
        </button>
        <div @class(["collapse", "navbar-collapse" ]) id="collapsibleNavId">
            <ul @class(["navbar-nav", "mr-auto" , "mt-2" , "mt-lg-0" ])>
                <li @class(["nav-item"])>
                    <a @class(["nav-link",'active'=> request()->routeIs('pages.index') ])
                        href="{{ route('pages.index') }}" wire:navigate>Home </a>
                </li>
                <li @class(["nav-item"])>
                    <a @class(["nav-link"]) href="#">Link</a>
                </li>
                <li @class(["nav-item", "dropdown" ])>
                    <a @class(["nav-link", "dropdown-toggle" ]) href="#" id="dropdownId" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">Dropdown</a>
                    <div @class(["dropdown-menu"]) aria-labelledby="dropdownId">
                        <a @class(["dropdown-item"]) href="#">Action 1</a>
                        <a @class(["dropdown-item"]) href="#">Action 2</a>
                    </div>
                </li>
            </ul>
            <ul @class(["navbar-nav", "ml-auto" , "mt-2" , "mt-lg-0" ])>
                <li @class(["nav-item"])>
                    <a @class(["nav-link"]) href="{{ route('auth.login') }}" wire:navigate>Login</a>
                </li>
                <li @class(["nav-item"])>
                    <a @class(["nav-link"]) href="{{ route('auth.register') }}" wire:navigate>Register</a>
                </li>
            </ul>
        </div>
    </div>
</nav>