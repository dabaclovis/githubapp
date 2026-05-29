<div class="container py-5 bg-light">
    <div class="bg-white rounded shadow p-5">
        <h1 class="display-4 fw-bold mb-4 text-center text-primary">
            <i class="fas fa-cogs me-2"></i>
            Welcome to the Services Application
        </h1>
        <p class="lead text-secondary mb-4 text-center">
            Manage your events, posts, comments, replies, and users with ease. All services include full CRUD
            functionality for seamless management.
        </p>
        <div class="row g-4">
            @foreach($services as $service)
            <div class="col-md-6 w3-card w3-card-animate mb-2">
                <div class="card h-100 border-{{ $service['color'] }}">
                    <div class="card-body">
                        <h2 class="card-title h4 text-{{ $service['color'] }}">
                            <i class="{{ $service['icon'] }} me-2"></i>{{ $service['title'] }}
                        </h2>
                        <p class="card-text">{{ $service['description'] }}</p>
                        <a href="{{ $service['link'] }}" class="btn btn-outline-{{ $service['btn'] }}">Go to {{
                            $service['title'] }}</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-5 text-center">
            <p class="text-muted lead"><i class="fas fa-check-circle text-success me-2"></i>All features are designed
                for simplicity and efficiency. Start managing your services now!</p>
        </div>
    </div>
</div>