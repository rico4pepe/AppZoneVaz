
<div class="main-content">

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Content List</h5>
        <select wire:model="filterType" class="form-select w-auto">
            <option value="">All Types</option>
            <option value="poll">Polls</option>
            <option value="quiz">Quizzes</option>
            <option value="trivia">Trivia</option>
        </select>
    </div>
    

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Published At</th>
                <th>Featured</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contents as $content)
                <tr>
                    <td>{{ $content->title }}</td>
                    <td>{{ ucfirst($content->type) }}</td>
                    <td>{{ $content->published_at ? $content->published_at->format('d M Y, h:i A') : '—' }}</td>
                    <td>
                        <span class="badge bg-{{ $content->is_featured ? 'success' : 'secondary' }}">
                            {{ $content->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $content->is_active ? 'primary' : 'danger' }}">
                            {{ $content->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" wire:click="edit({{ $content->id }})">Edit</button>
                        <button class="btn btn-sm btn-secondary" wire:click="viewStats({{ $content->id }})">Stats</button>
                        <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $content->id }})">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No content found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Delete Confirmation Modal -->
<div wire:ignore.self class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteConfirmLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this content? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button wire:click="deleteConfirmed" class="btn btn-danger">Yes, Delete</button>
      </div>
    </div>
  </div>
</div>



    {{ $contents->links() }}
</div>
</div>

<script>
    window.addEventListener('show-sweet-alert', () => {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('deleteConfirmed');
            }
        });
    });

    window.addEventListener('deleted', () => {
        Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'The content has been removed.',
            timer: 2000,
            showConfirmButton: false
        });
    });
</script>


