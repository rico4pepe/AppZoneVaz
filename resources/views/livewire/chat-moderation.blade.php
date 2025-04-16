
<div class="main-content">
<div class="container mt-4">
    <h5 class="mb-3">Chat Moderation Panel</h5>

    <div class="d-flex justify-content-between mb-2">
        <input type="text" class="form-control w-50" placeholder="Search message..." wire:model.debounce.500ms="search">
        <label class="form-check ms-3">
            <input class="form-check-input" type="checkbox" wire:model="showReportedOnly">
            <span class="form-check-label">Show Reported Only</span>
        </label>
    </div>

    <table class="table table-bordered table-sm">
        <thead class="table-light">
            <tr>
                <th>User</th>
                <th>Message</th>
                <th>Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($messages as $msg)
                <tr class="{{ $msg->is_hidden ? 'table-warning' : '' }}">
                    <td>{{ $msg->user->display_name ?? $msg->user->name }}</td>
                    <td>{{ $msg->message }}</td>
                    <td>{{ $msg->created_at->diffForHumans() }}</td>
                    <td>
                    @if (!$msg->is_hidden)
                        <button wire:click="hideMessage({{ $msg->id }})" class="btn btn-sm btn-warning">Hide</button>
                    @else
                        <span class="text-muted">Hidden</span>
                    @endif

                    <button wire:click="viewUserMessages({{ $msg->user_id }})" class="btn btn-sm btn-info">History</button>
                    <button wire:click="banUser({{ $msg->user_id }})" class="btn btn-sm btn-danger">Ban</button>

                    @if ($msg->reports->where('status', 'pending')->count())
                        <div class="mt-2">
                            <button wire:click="resolveReport({{ $msg->id }}, 'resolved')" class="btn btn-sm btn-success">Resolve</button>
                            <button wire:click="resolveReport({{ $msg->id }}, 'rejected')" class="btn btn-sm btn-outline-secondary">Reject</button>
                        </div>
                    @endif
                </td>

                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No messages found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>


