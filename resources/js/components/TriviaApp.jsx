import React, { useEffect, useState } from 'react';

const API_BASE = '/fanzone/api';

export default function TriviaApp() {
    const [trivia, setTrivia] = useState(null);
    const [answer, setAnswer] = useState('');
    const [result, setResult] = useState(null);
    const [loading, setLoading] = useState(true);

    const token = localStorage.getItem('auth_token');

    useEffect(() => {
        const loadTrivia = async () => {
            try {
                const res = await fetch(`${API_BASE}/trivia`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const data = await res.json();
                setTrivia(data[0] || null);
            } catch (e) {
                console.error(e);
            } finally {
                setLoading(false);
            }
        };

        loadTrivia();
    }, []);

    const submitAnswer = async () => {
        try {
            const res = await fetch(`${API_BASE}/trivia/${trivia.id}/answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({ answer })
            });

            const data = await res.json();

            if (!res.ok) {
                alert(data.message);
                return;
            }

            setResult(data);
        } catch (e) {
            console.error(e);
        }
    };

    if (loading) return <div>Loading trivia...</div>;
    if (!trivia) return <div>No trivia available</div>;

    return (
        <div className="card mt-4 shadow">
            <div className="card-header bg-dark text-white">
                {trivia.title}
            </div>

            <div className="card-body">
                <p>{trivia.description}</p>

                {!result ? (
                    <>
                        <input
                            type="text"
                            className="form-control"
                            value={answer}
                            onChange={(e) => setAnswer(e.target.value)}
                            placeholder="Type your answer..."
                        />

                        <button className="btn btn-primary mt-3" onClick={submitAnswer}>
                            Submit
                        </button>
                    </>
                ) : (
                    <div className={`alert ${result.correct ? 'alert-success' : 'alert-danger'}`}>
                        {result.correct
                            ? '✅ Correct!'
                            : `❌ Incorrect. Correct answer: ${result.correct_answer}`}
                    </div>
                )}
            </div>
        </div>
    );
}