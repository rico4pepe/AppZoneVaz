<ul class="list-group">
    @foreach ($users as $user)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ $user->display_name ?? $user->phone_number }}
            <span class="badge bg-primary rounded-pill">{{ $user->created_at->format('d M') }}</span>
        </li>
    @endforeach
</ul>
