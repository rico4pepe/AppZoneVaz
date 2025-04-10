@extends('layouts.admin.layout')

@section('content')
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
                    <td>{{ \$content->title }}</td>
                    <td>{{ ucfirst(\$content->type) }}</td>
                    <td>{{ \$content->published_at ? \$content->published_at->format('d M Y, h:i A') : '—' }}</td>
                    <td>
                        <span class="badge bg-{{ \$content->is_featured ? 'success' : 'secondary' }}">
                            {{ \$content->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ \$content->is_active ? 'primary' : 'danger' }}">
                            {{ \$content->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" wire:click="edit({{ \$content->id }})">Edit</button>
                        <button class="btn btn-sm btn-secondary" wire:click="viewStats({{ \$content->id }})">Stats</button>
                        <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ \$content->id }})">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No content found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ \$contents->links() }}
</div>
@endsection
