// resources/js/poll.jsx
import React from 'react';
import { createRoot } from 'react-dom/client';
import LeaderboardWidget from './components/LeaderboardWidget';

const container = document.getElementById('leaderboard-widget');
//console.log("Container:", container);
if (container) {
     const userId = container.dataset.user; // ✅ get from Blade
    const root = createRoot(container);
    root.render(
        <LeaderboardWidget 
            userId={container.dataset.user}
        />
    );
}
