@props(['notifications'])

<div class="nav-item dropdown" x-data="{ open: false }" @click.away="open = false">
    <button class="nav-link position-relative" @click="open = !open">
        <i class="fas fa-bell"></i>
        @if($notifications->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $notifications->count() }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0" x-show="open"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
        style="width: 320px; max-height: 400px; overflow-y: auto;">

        <div class="p-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Notifications</h6>
                @if($notifications->count() > 0)
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0">
                            Mark all as read
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="list-group list-group-flush">
            @forelse($notifications as $notification)
                <div class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            @if($notification->data['type'] === 'appointment_created')
                                <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                            @elseif($notification->data['type'] === 'appointment_accepted')
                                <div class="avatar avatar-sm bg-success-subtle text-white rounded-circle">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            @elseif($notification->data['type'] === 'appointment_rejected')
                                <div class="avatar avatar-sm bg-danger-subtle text-danger rounded-circle">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            @elseif($notification->data['type'] === 'appointment_status_changed')
                                <div class="avatar avatar-sm bg-info-subtle text-info rounded-circle">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                            @else
                                <div class="avatar avatar-sm bg-secondary-subtle text-secondary rounded-circle">
                                    <i class="fas fa-bell"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-1">{{ $notification->data['message'] }}</p>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        @if(!$notification->read_at)
                            <div class="flex-shrink-0 ms-2">
                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0">
                                        <i class="fas fa-check text-white"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No notifications</p>
                </div>
            @endforelse
        </div>

        @if($notifications->count() > 0)
            <div class="p-3 border-top text-center">
                <a href="{{ route('notifications.index') }}" class="text-decoration-none">
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>