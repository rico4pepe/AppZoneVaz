import React from 'react';
import { createRoot } from 'react-dom/client';
import FeedApp from './components/FeedApp';

const container = document.getElementById('feed-app');

if (container) {
    const root = createRoot(container);
    root.render(
        <React.StrictMode>
            <FeedApp />
        </React.StrictMode>
    );
}