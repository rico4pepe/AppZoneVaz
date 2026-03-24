import React, { useEffect, useState } from 'react';

const API_BASE = '/fanzone/api';

export default function QuizApp() {
    const [quizzes, setQuizzes] = useState([]);
    const [quizIndex, setQuizIndex] = useState(0);
    const [selectedOption, setSelectedOption] = useState(null);
    const [answeredMap, setAnsweredMap] = useState({});
    const [loading, setLoading] = useState(true);

    const token = localStorage.getItem('auth_token');
    const currentQuiz = quizzes[quizIndex] || null;
    const currentState = currentQuiz ? answeredMap[currentQuiz.id] : null;

    useEffect(() => {
        if (!token) return;

        const loadQuizzes = async () => {
            setLoading(true);

            try {
                const res = await fetch(`${API_BASE}/quizzes`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                const data = await res.json();
                setQuizzes(data);

                // preload answered state
                const stateMap = {};
                for (const quiz of data) {
                    const res = await fetch(`${API_BASE}/quiz/${quiz.id}/check-answered`, {
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                    const result = await res.json();

                    stateMap[quiz.id] = result.answered
                        ? {
                            answered: true,
                            correct: result.correct,
                            correct_option_id: result.correct_option_id,
                            selected_option_id: result.selected_option_id
                        }
                        : { answered: false };
                }

                setAnsweredMap(stateMap);

            } catch (error) {
                console.error("Error loading quizzes", error);
            } finally {
                setLoading(false);
            }
        };

        loadQuizzes();
    }, []);

    const submitAnswer = async () => {
        if (!selectedOption) return alert("Please select an option");

        try {
            const res = await fetch(`${API_BASE}/quiz/${currentQuiz.id}/answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({ option_id: selectedOption })
            });

            const data = await res.json();

            if (!res.ok) {
                alert(data.message || "Failed to submit answer.");
                return;
            }

            setAnsweredMap(prev => ({
                ...prev,
                [currentQuiz.id]: {
                    answered: true,
                    correct: data.correct,
                    correct_option_id: data.correct_option_id,
                    selected_option_id: selectedOption
                }
            }));

        } catch (error) {
            console.error("Error submitting answer", error);
            alert("An error occurred while submitting your answer.");
        }
    };

    const navigateQuiz = (direction) => {
        const newIndex = quizIndex + direction;
        if (newIndex >= 0 && newIndex < quizzes.length) {
            setQuizIndex(newIndex);
            setSelectedOption(null);
        }
    };

    const getOptionClassName = (optionId) => {
        if (!currentState?.answered) return "form-check mb-2";

        let className = "form-check mb-2";

        if (optionId === currentState.selected_option_id) {
            className += currentState.correct ? " text-success fw-bold" : " text-danger fw-bold";
        } else if (optionId === currentState.correct_option_id) {
            className += " text-success fw-bold";
        }

        return className;
    };

    if (loading) {
        return (
            <div className="d-flex justify-content-center p-4">
                <div className="spinner-border" role="status"></div>
            </div>
        );
    }

    if (!currentQuiz) {
        return <div className="alert alert-info">No quizzes available right now.</div>;
    }

    return (
        <div className="card shadow mt-4">
            <div className="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 className="m-0">{currentQuiz.title}</h5>
                <span className="badge bg-light text-primary">
                    Question {quizIndex + 1} of {quizzes.length}
                </span>
            </div>

            <div className="card-body">
                <p>{currentQuiz.description}</p>

                <div className="options-container mb-3">
                    {currentQuiz.options.map(option => (
                        <div className={getOptionClassName(option.id)} key={option.id}>
                            <input
                                className="form-check-input"
                                type="radio"
                                name="quizOption"
                                value={option.id}
                                id={`quiz-option-${option.id}`}
                                onChange={() => setSelectedOption(option.id)}
                                checked={
                                    currentState?.answered
                                        ? option.id === currentState.selected_option_id
                                        : option.id === selectedOption
                                }
                                disabled={currentState?.answered}
                            />
                            <label className="form-check-label" htmlFor={`quiz-option-${option.id}`}>
                                {option.option_text}
                                {currentState?.answered && option.id === currentState.correct_option_id && " ✓"}
                            </label>
                        </div>
                    ))}
                </div>

                {currentState?.answered ? (
                    <div className={`alert ${currentState.correct ? "alert-success" : "alert-danger"}`}>
                        {currentState.correct ? "✅ Correct!" : "❌ Incorrect."}
                    </div>
                ) : (
                    <button
                        className="btn btn-success"
                        onClick={submitAnswer}
                        disabled={!selectedOption}
                    >
                        Submit Answer
                    </button>
                )}

                <div className="d-flex justify-content-between mt-4">
                    <button
                        className="btn btn-outline-secondary"
                        disabled={quizIndex === 0}
                        onClick={() => navigateQuiz(-1)}
                    >
                        ← Previous
                    </button>

                    <button
                        className="btn btn-outline-primary"
                        disabled={quizIndex === quizzes.length - 1}
                        onClick={() => navigateQuiz(1)}
                    >
                        Next →
                    </button>
                </div>
            </div>
        </div>
    );
}