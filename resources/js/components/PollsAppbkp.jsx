import React, { useEffect, useState } from 'react';

export default function PollApp() {
    const [polls, setPolls] = useState([]);
    const [pollIndex, setPollIndex] = useState(0);
    const [selectedOption, setSelectedOption] = useState(null);
    const [loading, setLoading] = useState(true);

    const token = localStorage.getItem('auth_token');
    const API = '/fanzone/api';

    const selectedPoll = polls[pollIndex] || null;
    const isVoted = selectedPoll?.hasVoted;

    const loadPolls = async () => {
        if (!token) return;

        setLoading(true);

        try {
            const res = await fetch(`${API}/polls`, {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await res.json();
            setPolls(data);
        } catch (error) {
            console.error("Error loading polls", error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadPolls();
    }, [token]);

    const submitVote = async () => {
        if (!selectedOption) return alert("Please select an option");

        try {
            const res = await fetch(`${API}/poll/vote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                },
                body: JSON.stringify({
                    content_id: selectedPoll.id,
                    option_id: selectedOption
                })
            });

            const data = await res.json();

            if (res.ok) {
                // Reload everything from backend (single source of truth)
                await loadPolls();
                setSelectedOption(null);
            } else {
                alert(data.message || "Failed to vote.");
            }
        } catch (error) {
            console.error("Error submitting vote", error);
            alert("There was an error submitting your vote.");
        }
    };

    if (loading) return <div>Loading poll...</div>;
    if (!selectedPoll) return <div>No polls available right now.</div>;

    return (
        <div className="card shadow mt-4">
            <div className="card-header bg-warning text-dark">
                <h5 className="m-0">{selectedPoll.title}</h5>
            </div>

            <div className="card-body">
                <p>{selectedPoll.description}</p>

                {!isVoted ? (
                    <>
                        {selectedPoll.options.map(option => (
                            <div className="form-check mb-2" key={option.id}>
                                <input
                                    className="form-check-input"
                                    type="radio"
                                    name="pollOption"
                                    value={option.id}
                                    id={`option-${option.id}`}
                                    onChange={() => setSelectedOption(option.id)}
                                />
                                <label className="form-check-label" htmlFor={`option-${option.id}`}>
                                    {option.option_text}
                                </label>
                            </div>
                        ))}

                        <button className="btn btn-primary mt-3" onClick={submitVote}>
                            Submit Vote
                        </button>
                    </>
                ) : (
                    <>
                        <h6>Thank you for voting! Here are the results:</h6>

                        {selectedPoll.results?.map(result => (
                            <div key={result.option_id} className="mb-2">
                                <div className="d-flex justify-content-between mb-1">
                                    <strong>{result.option_text}</strong>
                                    <small>
                                        {result.percentage}% ({result.votes} vote{result.votes !== 1 ? 's' : ''})
                                    </small>
                                </div>

                                <div className="progress">
                                    <div
                                        className="progress-bar bg-success"
                                        role="progressbar"
                                        style={{ width: `${result.percentage || 0}%` }}
                                    >
                                        {result.percentage || 0}%
                                    </div>
                                </div>
                            </div>
                        ))}
                    </>
                )}

                <div className="d-flex justify-content-between mt-3">
                    <button
                        className="btn btn-outline-secondary"
                        disabled={pollIndex === 0}
                        onClick={() => {
                            setPollIndex(pollIndex - 1);
                            setSelectedOption(null);
                        }}
                    >
                        ← Previous
                    </button>

                    <button
                        className="btn btn-outline-primary"
                        disabled={pollIndex === polls.length - 1}
                        onClick={() => {
                            setPollIndex(pollIndex + 1);
                            setSelectedOption(null);
                        }}
                    >
                        Next →
                    </button>
                </div>
            </div>
        </div>
    );
}