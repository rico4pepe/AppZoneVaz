import React from 'react';
import { createRoot } from 'react-dom/client';
import ChatApp from './components/ChatApp';

const container = document.getElementById('chat-app');
if (container) {
    const root = createRoot(container);
    root.render(   <ChatApp 
        userId={container.dataset.user} 
        username={container.dataset.username} 
    />);
}
