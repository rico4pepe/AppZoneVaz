import React, { useState, useEffect } from 'react';
import useFeedInteraction from '../hooks/useFeedInteraction';
import Swal from 'sweetalert2';

export default function TriviaCard({ trivia, token, onUpdate }) {
    const { interact } = useFeedInteraction(token);

    const [answer, setAnswer] = useState('');
    const [loading, setLoading] = useState(false);
    const [localTrivia, setLocalTrivia] = useState(trivia);

    useEffect(() => {
        setLocalTrivia(trivia);
        setAnswer('');
    }, [trivia]);

    const submitAnswer = async () => {
        if (!answer.trim() || loading) return;

        setLoading(true);

        try {
            const result = await interact({
                contentId: localTrivia.id,
                answer: answer.trim()
            });

            const updatedTrivia = {
                ...localTrivia,
                hasAnswered: true,
                user_answer: result.answer,
                correct_answer: result.correct_answer,
                isCorrect: result.is_correct
            };

            setLocalTrivia(updatedTrivia);

            if (onUpdate) {
                onUpdate(updatedTrivia);
            }

            // 🔥 feedback popup
            Swal.fire({
                icon: result.is_correct ? 'success' : 'error',
                title: result.is_correct ? 'Correct!' : 'Not quite!',
                timer: 1200,
                showConfirmButton: false
            });

        } catch (e) {
            console.error(e);
        } finally {
            setLoading(false);
        }
    };

    if (!localTrivia) return null;

    return (
        <div className="fz-card fz-trivia-card">

            {/* HEADER */}
            <div className="fz-card-header">
                <span className="fz-section-title">Trivia</span>
            </div>

            <div className="fz-card-body">

                {/* TITLE */}
                <div className="fz-trivia-title">
                    {localTrivia.title}
                </div>

                {/* INPUT STATE */}
                {!localTrivia.hasAnswered ? (
                    <div className="fz-trivia-input-wrap">

                        <input
                            type="text"
                            className="fz-trivia-input"
                            placeholder="Type your answer..."
                            value={answer}
                            onChange={(e) => setAnswer(e.target.value)}
                        />

                        <button
                            type="button"
                            className="fz-trivia-submit"
                            onClick={submitAnswer}
                            disabled={!answer.trim() || loading}
                        >
                            {loading ? 'Submitting...' : 'Submit'}
                        </button>

                    </div>
                ) : (

                    /* RESULT STATE */
                    <div className="fz-trivia-result">

                        <div className={`fz-trivia-status ${localTrivia.isCorrect ? 'success' : 'error'}`}>
                            {localTrivia.isCorrect ? '✅ Correct!' : '❌ Incorrect'}
                        </div>

                        <div className="fz-trivia-answer">
                            <span className="fz-text-meta">Your answer</span>
                            <span>{localTrivia.user_answer}</span>
                        </div>

                        {!localTrivia.isCorrect && (
                            <div className="fz-trivia-answer correct">
                                <span className="fz-text-meta">Correct answer</span>
                                <span>{localTrivia.correct_answer}</span>
                            </div>
                        )}

                    </div>
                )}

            </div>
        </div>
    );
}