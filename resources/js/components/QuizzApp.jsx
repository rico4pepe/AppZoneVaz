import React, { useEffect, useState } from 'react';

export default function QuizApp({ userId }) {
    const [quizzes, setQuizzes] = useState([]);
    const [quizIndex, setQuizIndex] = useState(0);
    const [selectedOption, setSelectedOption] = useState(null);
    const [answered, setAnswered] = useState(false);
    const [loading, setLoading] = useState(true);
    const [isCorrect, setIsCorrect] = useState(null);
    const [correctOptionId, setCorrectOptionId] = useState(null);
    const [selectedOptionId, setSelectedOptionId] = useState(null);

    const token = localStorage.getItem('auth_token');
    const currentQuiz = quizzes[quizIndex] || null;

    useEffect(() => {
        if (!token) return;

        const loadQuizzes = async () => {
            setLoading(true);

            try {
                const res = await fetch('/api/quizzes', {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                const data = await res.json();
                setQuizzes(data);

                if (data.length > 0) {
                    await checkIfAnswered(data[0].id);
                }
            } catch (error) {
                console.error("Error loading quizzes", error);
            } finally {
                setLoading(false);
            }
        };

        loadQuizzes();
    }, []);

    const checkIfAnswered = async (quizId) => {
        try {
            const res = await fetch(`/api/quiz/${quizId}/check-answered`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            
            const data = await res.json();
            
            setAnswered(data.answered);
            
            if (data.answered) {
                setIsCorrect(data.correct);
                setCorrectOptionId(data.correct_option_id);
                setSelectedOptionId(data.selected_option_id);
            } else {
                setIsCorrect(null);
                setCorrectOptionId(null);
                setSelectedOptionId(null);
            }
        } catch (error) {
            console.error("Error checking if quiz was answered", error);
        }
    };

    const submitAnswer = async () => {
        if (!selectedOption) return alert("Please select an option");

        try {
            const res = await fetch(`/api/quiz/${currentQuiz.id}/answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({ option_id: selectedOption })
            });

            const data = await res.json();

            if (res.ok) {
                setAnswered(true);
                setIsCorrect(data.correct);
                setCorrectOptionId(data.correct_option_id);
                setSelectedOptionId(selectedOption);
            } else {
                alert(data.message || "Failed to submit answer.");
            }
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
            checkIfAnswered(quizzes[newIndex].id);
        }
    };

    const getOptionClassName = (optionId) => {
        if (!answered) return "form-check mb-2";
        
        let className = "form-check mb-2";
        
        if (optionId === selectedOptionId) {
            className += isCorrect ? " text-success fw-bold" : " text-danger fw-bold";
        } else if (optionId === correctOptionId) {
            className += " text-success fw-bold";
        }
        
        return className;
    };

    if (loading) return <div className="d-flex justify-content-center p-4"><div className="spinner-border" role="status"></div></div>;
    if (!currentQuiz) return <div className="alert alert-info">No quizzes available right now.</div>;

    return (
        <div className="card shadow mt-4">
            <div className="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 className="m-0">{currentQuiz.title}</h5>
                <span className="badge bg-light text-primary">Question {quizIndex + 1} of {quizzes.length}</span>
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
                                checked={answered ? option.id === selectedOptionId : option.id === selectedOption}
                                disabled={answered}
                            />
                            <label className="form-check-label" htmlFor={`quiz-option-${option.id}`}>
                                {option.option_text}
                                {answered && option.id === correctOptionId && " ✓"}
                            </label>
                        </div>
                    ))}
                </div>

                {answered ? (
                    <div className={`alert ${isCorrect ? "alert-success" : "alert-danger"} mt-3`}>
                        <h6 className="mb-0">
                            {isCorrect ? "✅ Correct!" : "❌ Incorrect."}
                        </h6>
                    </div>
                ) : (
                    <button className="btn btn-success mt-2" onClick={submitAnswer} disabled={!selectedOption}>
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