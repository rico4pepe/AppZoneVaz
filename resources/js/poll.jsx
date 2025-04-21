// resources/js/poll.jsx
import React from 'react';
import { createRoot } from 'react-dom/client';
import PollApp from './components/PollsApp';

const container = document.getElementById('poll-app');
if (container) {
    const root = createRoot(container);
    root.render(
        <PollApp
            userId={container.dataset.user}
            token={container.dataset.token}
        />
    );
}
