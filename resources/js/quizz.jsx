// resources/js/quizz.jsx
import React from 'react';
import { createRoot } from 'react-dom/client';
import QuizApp from './components/QuizzApp';

const container = document.getElementById('quiz-app');
if (container) {
    const root = createRoot(container);
    root.render(
        <QuizApp
            userId={container.dataset.user}
            token={container.dataset.token}
        />
    );
}
