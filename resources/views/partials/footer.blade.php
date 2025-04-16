   
   <!-- Favorite Team Setup Modal -->
<div class="modal fade" id="onboardingModal" tabindex="-1" aria-labelledby="onboardingLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('user.setup-preference') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="onboardingLabel">Set Up Your Fan Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input name="username" class="form-control" id="username" value="{{ old('username', auth()->user()?->name) }}" required>
          </div>

          <div class="mb-3">
            <label for="league" class="form-label">Favorite League</label>
            <select class="form-select" id="league" name="league_id" required>
              <option value="">-- Select League --</option>
              <option value="1">Premier League</option>
              <option value="2">La Liga</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="team" class="form-label">Favorite Team</label>
            <select class="form-select" id="team" name="team_id" required>
              <option value="">-- Select Team --</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Preferences</button>
        </div>
      </form>
    </div>
  </div>
</div>


   <footer class="bg-dark text-white text-center py-3 mt-5 footer">
        <p class="mb-0">&copy;  {{ date('Y') }} MTN Football FansZone. All Rights Reserved.</p>
    </footer>

    <script>
    const teamsByLeague = {
        1: [ { id: 101, name: 'Arsenal' }, { id: 102, name: 'Chelsea' }, { id: 103, name: 'Liverpool' } ],
        2: [ { id: 201, name: 'Barcelona' }, { id: 202, name: 'Real Madrid' }, { id: 203, name: 'Atletico Madrid' } ],
    };

    document.getElementById('league').addEventListener('change', function () {
        const selectedLeague = this.value;
        const teamDropdown = document.getElementById('team');
        teamDropdown.innerHTML = '<option value="">-- Select Team --</option>';

        if (teamsByLeague[selectedLeague]) {
            teamsByLeague[selectedLeague].forEach(team => {
                const option = document.createElement('option');
                option.value = team.id;
                option.textContent = team.name;
                teamDropdown.appendChild(option);
            });
        }
    });
</script>
