{{-- Assuming this is resources/views/livewire/content-manager.blade.php --}}
<div> {{-- Livewire components require a single root element --}}
  
    <div class="main-content">
        {{-- Your Navbar code remains here --}}
        <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 rounded shadow-sm">
          <div class="container-fluid">
            <button class="navbar-toggler d-md-none" type="button" id="sidebarToggle">
              <span class="navbar-toggler-icon"></span>
            </button>
            <h4 class="mb-0">Content Management {{ $contentId ? '(Editing)' : '(Create New)' }}</h4>
            <div class="d-flex align-items-center">
              <span class="me-2">Admin Namessss</span>
              <div class="admin-avatar rounded-circle d-flex align-items-center justify-content-center">
                <span>A</span>
              </div>
            </div>
          </div>
        </nav>

        {{-- Your static tabs remain here - these don't seem connected to the component logic --}}
         <ul class="nav nav-tabs mb-4">
           <li class="nav-item">
             <a class="nav-link active" href="#">All Content</a>
           </li>
           <li class="nav-item">
             <a class="nav-link" href="#">Polls</a>
           </li>
           <li class="nav-item">
             <a class="nav-link" href="#">Quizzes</a>
           </li>
           <li class="nav-item">
             <a class="nav-link" href="#">Trivia</a>
           </li>
         </ul>

        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center bg-white">
            <h5 class="mb-0">{{ $contentId ? 'Edit Content' : 'Create New Content' }}</h5>
          </div>
          <div class="card-body">

            {{-- Display Success Message --}}
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            {{-- Use wire:submit to call the save method --}}
            <form wire:submit.prevent="save">
              <div class="mb-3">
                <label class="form-label">Content Type</label>
                 {{-- Use wire:model.live to update the UI instantly when type changes --}}
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="contentType" id="pollType" value="poll" wire:model.live="type">
                  <label class="form-check-label" for="pollType">Poll</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="contentType" id="quizType" value="quiz" wire:model.live="type">
                  <label class="form-check-label" for="quizType">Quiz</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="contentType" id="triviaType" value="trivia" wire:model.live="type">
                  <label class="form-check-label" for="triviaType">Trivia</label>
                </div>
                 @error('type') <span class="text-danger d-block">{{ $message }}</span> @enderror
              </div>

              <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                {{-- Bind title input to $title property --}}
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" placeholder="Enter content title" wire:model="title">
                @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
              </div>

              <div class="mb-3">
                 {{-- Dynamically change label based on type or keep generic --}}
                <label for="description" class="form-label">
                    @if($type === 'quiz') Question @else Description @endif
                    (Optional)
                </label>
                {{-- Bind description textarea to $description property --}}
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" rows="3" placeholder="Enter description or question" wire:model="description"></textarea>
                 @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
              </div>

              {{-- Conditionally show Options section only for Quizzes --}}
              @if ($type === 'quiz')
                  <div class="mb-3">
                    <label class="form-label">Options</label>
                    @error('options') <span class="text-danger d-block mb-2">{{ $message }}</span> @enderror
                    @error('correctOption') <span class="text-danger d-block mb-2">{{ $message }}</span> @enderror

                    <div class="border rounded p-3">
                       {{-- Loop through the $options array --}}
                      @foreach ($options as $index => $option)
                          <div class="option-item d-flex align-items-center mb-2" wire:key="option-{{ $index }}">
                             {{-- Bind option text input --}}
                            <input type="text" class="form-control me-2 @error('options.'.$index.'.text') is-invalid @enderror" placeholder="Option {{ $index + 1 }}" wire:model="options.{{ $index }}.text">

                            {{-- Radio button to select the correct answer --}}
                            <div class="form-check me-2">
                                <input class="form-check-input" type="radio" name="correctOption" id="correct_option_{{ $index }}" value="{{ $index }}" wire:model="correctOption">
                                <label class="form-check-label" for="correct_option_{{ $index }}">
                                  Correct
                                </label>
                              </div>

                             {{-- Button to remove the option --}}
                            <button type="button" class="btn btn-outline-danger btn-sm" wire:click.prevent="removeOption({{ $index }})" title="Remove Option">
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                           @error('options.'.$index.'.text') <span class="text-danger d-block mb-2">{{ $message }}</span> @enderror
                      @endforeach

                      {{-- Button to add a new option --}}
                      <button type="button" class="btn btn-outline-secondary w-100 mt-2" wire:click.prevent="addOption">
                        <i class="fas fa-plus me-1"></i> Add Option
                      </button>
                    </div>
                  </div>
              @endif {{-- End of conditional options section --}}

              {{-- Category (Not in class properties) --}}
              {{-- <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category">
                  <option value="">Select category</option>
                  <option value="general">General</option>
                  <option value="premier-league">Premier League</option>
                  <option value="la-liga">La Liga</option>
                  <option value="nba">NBA</option>
                  <option value="nfl">NFL</option>
                </select>
              </div> --}}

              <div class="mb-3">
                <label for="publishDate" class="form-label">Publish Date (Optional)</label>
                {{-- Bind publish date input to $publishAt --}}
                <input type="datetime-local" class="form-control @error('publishAt') is-invalid @enderror" id="publishDate" wire:model="publishAt">
                 @error('publishAt') <span class="invalid-feedback">{{ $message }}</span> @enderror
              </div>

              {{-- Status (Not in class properties - using isActive and isFeatured instead) --}}
              {{-- <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status">
                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
                  <option value="scheduled">Scheduled</option>
                </select>
              </div> --}}

              {{-- Example binding for isActive and isFeatured --}}
              <div class="mb-3">
                  <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="isActive" wire:model="isActive">
                      <label class="form-check-label" for="isActive">Active</label>
                  </div>
              </div>
              <div class="mb-3">
                  <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="isFeatured" wire:model="isFeatured">
                      <label class="form-check-label" for="isFeatured">Featured</label>
                  </div>
              </div>


              <div class="text-end">
                 {{-- Change button type to submit --}}
                <button type="submit" class="btn btn-primary">
                    {{-- Show loading indicator during save --}}
                    <span wire:loading wire:target="save">Saving...</span>
                    <span wire:loading.remove wire:target="save">{{ $contentId ? 'Update Content' : 'Create Content' }}</span>
                </button>
              </div>
            </form> {{-- End of form --}}
          </div>
        </div>

        {{-- Your content list code would go here --}}

      </div> {{-- End main-content --}}

</div> {{-- End Livewire root element --}}