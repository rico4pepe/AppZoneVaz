import React, { useEffect, useState } from 'react';

export default function PollApp({ userId }) {
    const [polls, setPolls] = useState([]);
    const [pollIndex, setPollIndex] = useState(0);
    const [selectedOption, setSelectedOption] = useState(null);
    const [votedPolls, setVotedPolls] = useState({}); // Track voted status by poll ID
    const [loading, setLoading] = useState(true);

    const token = localStorage.getItem('auth_token');
    const selectedPoll = polls[pollIndex] || null;
    const isVoted = selectedPoll ? votedPolls[selectedPoll.id] : false;

    useEffect(() => {
        if (!token) return;

        const loadPolls = async () => {
            setLoading(true);

            try {
                const res = await fetch('/api/polls', {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });
                const data = await res.json();
                setPolls(data);

                // Check vote status for all polls
                if (data.length > 0) {
                    const votedStatus = {};
                    for (const poll of data) {
                        const status = await checkVoteStatus(poll.id);
                        votedStatus[poll.id] = status;
                    }
                    setVotedPolls(votedStatus);
                }
            } catch (error) {
                console.error("Error loading polls", error);
            } finally {
                setLoading(false);
            }
        };

        loadPolls();
    }, []);

    const checkVoteStatus = async (pollId) => {
        try {
            const voteRes = await fetch(`/api/poll/${pollId}/check-vote`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const voteData = await voteRes.json();
            
            if (voteData.hasVoted) {
                const resultRes = await fetch(`/api/poll/${pollId}/results`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const resultData = await resultRes.json();

                setPolls(prev => {
                    return prev.map(poll => {
                        if (poll.id === pollId) {
                            return { ...poll, results: resultData.results };
                        }
                        return poll;
                    });
                });
            }
            
            return voteData.hasVoted;
        } catch (error) {
            console.error(`Error checking vote status for poll ${pollId}`, error);
            return false;
        }
    };

    const submitVote = async () => {
        if (!selectedOption) return alert("Please select an option");

        try {
            const res = await fetch(`/api/poll/vote`, {
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
                // Update voted status for this poll
                setVotedPolls(prev => ({
                    ...prev,
                    [selectedPoll.id]: true
                }));

                const resultRes = await fetch(`/api/poll/${selectedPoll.id}/results`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const resultData = await resultRes.json();

                setPolls(prev => {
                    return prev.map(poll => {
                        if (poll.id === selectedPoll.id) {
                            return { ...poll, results: resultData.results };
                        }
                        return poll;
                    });
                });
            } else {
                alert(data.message || "Failed to vote.");
            }
        } catch (error) {
            console.error("Error submitting vote", error);
            alert("There was an error submitting your vote. Please try again.");
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

                        <button className="btn btn-primary mt-3" onClick={submitVote}>Submit Vote</button>
                    </>
                ) : (
                    <>
                        <h6>Thank you for voting! Here are the results:</h6>
                        {selectedPoll.results?.map(result => (
                            <div key={result.option_id} className="mb-2">
                                 <div className="d-flex justify-content-between mb-1">
                                    <strong>{result.option_text}</strong>
                                    <small>{result.percentage}% ({result.votes} vote{result.votes !== 1 ? 's' : ''})</small>
                                </div>
                                <div className="progress">
                                    <div
                                        className="progress-bar bg-success"
                                        role="progressbar"
                                        style={{ width: `${result.percentage || 0}%` }}
                                        aria-valuenow={result.percentage || 0}
                                        aria-valuemin="0"
                                        aria-valuemax="100"
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