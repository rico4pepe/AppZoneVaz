import React, { useState, useEffect } from 'react';
import useFeedInteraction from '../hooks/useFeedInteraction';
import Swal from 'sweetalert2';

export default function QuizCard({ quiz, token, onUpdate }) {
    const { interact } = useFeedInteraction(token);

    const [selectedOption, setSelectedOption] = useState(null);
    const [loading, setLoading] = useState(false);
    const [localQuiz, setLocalQuiz] = useState(quiz);

    useEffect(() => {
        setLocalQuiz(quiz);
        setSelectedOption(null);
    }, [quiz]);

    const submitAnswer = async () => {
        if (!selectedOption || loading) return;

        setLoading(true);

        try {
            const result = await interact({
                contentId: localQuiz.id,
                answer: selectedOption
            });

            const updatedQuiz = {
                ...localQuiz,
                hasAnswered: true,
                selected_option_id: result.answer,
                correct_option_id: result.correct_option_id,
                isCorrect: result.is_correct
            };

            setLocalQuiz(updatedQuiz);

            if (onUpdate) {
                onUpdate(updatedQuiz);
            }

            // 🔥 feedback
            Swal.fire({
                icon: result.is_correct ? 'success' : 'error',
                title: result.is_correct ? 'Correct!' : 'Wrong answer',
                timer: 1200,
                showConfirmButton: false
            });

        } catch (e) {
            console.error(e);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="fz-card fz-quiz-card">

            {/* HEADER */}
            <div className="fz-card-header">
                <span className="fz-section-title">Quiz</span>
            </div>

            <div className="fz-card-body">

                {/* TITLE */}
                <div className="fz-quiz-title">
                    {localQuiz.title}
                </div>

                {/* DESCRIPTION */}
                {localQuiz.description && (
                    <div className="fz-quiz-desc">
                        {localQuiz.description}
                    </div>
                )}

                {/* OPTIONS */}
                <div className="fz-quiz-options">
                    {localQuiz.options.map(option => {
                        const isSelected =
                            localQuiz.hasAnswered
                                ? localQuiz.selected_option_id === option.id
                                : selectedOption === option.id;

                        const isCorrect =
                            localQuiz.correct_option_id === option.id;

                        let stateClass = '';

                        if (localQuiz.hasAnswered) {
                            if (isCorrect) stateClass = 'correct';
                            else if (isSelected) stateClass = 'wrong';
                        } else if (isSelected) {
                            stateClass = 'active';
                        }

                        return (
                            <button
                                key={option.id}
                                type="button"
                                className={`fz-quiz-option ${stateClass}`}
                                onClick={() => setSelectedOption(option.id)}
                                disabled={localQuiz.hasAnswered || loading}
                            >
                                {option.option_text}
                            </button>
                        );
                    })}
                </div>

                {/* ACTION / RESULT */}
                {!localQuiz.hasAnswered ? (
                    <button
                        type="button"
                        className="fz-quiz-submit"
                        onClick={submitAnswer}
                        disabled={!selectedOption || loading}
                    >
                        {loading ? 'Submitting...' : 'Submit Answer'}
                    </button>
                ) : (
                    <div className={`fz-quiz-result ${localQuiz.isCorrect ? 'success' : 'error'}`}>
                        {localQuiz.isCorrect ? '✅ Correct!' : '❌ Incorrect'}
                    </div>
                )}

            </div>
        </div>
    );
}