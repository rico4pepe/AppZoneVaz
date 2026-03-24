// resources/js/poll.jsx
import React from 'react';
import { createRoot } from 'react-dom/client';
import TriviaApp from './components/TriviaApp';

const container = document.getElementById('trivia-app');
if (container) {
    const root = createRoot(container);
    root.render(
        <TriviaApp />
    );
}
