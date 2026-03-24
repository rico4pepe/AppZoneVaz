import React, { useState, useEffect } from 'react';

export default function PollCard({ poll, token }) {
    const API = '/fanzone/api';

    const [selectedOption, setSelectedOption] = useState(null);
    const [localPoll, setLocalPoll] = useState({
        ...poll,
        hasVoted: poll.hasVoted ?? poll.has_voted ?? false,
        results: poll.results || []
    });

    useEffect(() => {
        setLocalPoll({
            ...poll,
            hasVoted: poll.hasVoted ?? poll.has_voted ?? false,
            results: poll.results || []
        });
        setSelectedOption(null);
    }, [poll]);

    const submitVote = async () => {
        if (!selectedOption) return;

        const res = await fetch(`${API}/poll/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
            body: JSON.stringify({
                content_id: localPoll.id,
                option_id: selectedOption
            })
        });

        if (res.ok) {
            const resultRes = await fetch(`${API}/poll/${localPoll.id}/results`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            const resultData = await resultRes.json();

            setLocalPoll(prev => ({
                ...prev,
                hasVoted: true,
                results: resultData.results || [],
                selected_option_id: selectedOption
            }));

            setSelectedOption(null);
        }
    };

    console.log("OPTIONS:", localPoll.options);
console.log("RESULTS:", localPoll.results);

    return (
        <div className="card mb-3">
            <div className="card-header bg-warning">
                {localPoll.title}
            </div>

            <div className="card-body">

                {!localPoll.hasVoted ? (
                    <>
                        {localPoll.options.map(option => (
                            <div key={option.id} className="form-check mb-2">
                                <input
                                    className="form-check-input"
                                    type="radio"
                                    checked={
                                        selectedOption === option.id
                                    }
                                    onChange={() => setSelectedOption(option.id)}
                                />
                                <label className="form-check-label">
                                    {option.option_text}
                                </label>
                            </div>
                        ))}

                        <button
                            className="btn btn-primary mt-2"
                            onClick={submitVote}
                            disabled={!selectedOption}
                        >
                            Vote
                        </button>
                    </>
                ) : (
                    <>
                        {localPoll.options.map(option => {
                            const result = localPoll.results.find(
                                r => Number(r.option_id) === Number(option.id)
                            );

                            return (
                                <div key={option.id} className="mb-2">
                                    <div className="d-flex justify-content-between mb-1">
                                        <strong>{option.option_text}</strong>
                                        <small>
                                            {result
                                                ? `${result.percentage}% (${result.votes})`
                                                : '0% (0)'}
                                        </small>
                                    </div>

                                    <div className="progress">
                                        <div
                                            className="progress-bar bg-success"
                                            style={{
                                                width: `${result?.percentage || 0}%`
                                            }}
                                        />
                                    </div>
                                </div>
                            );
                        })}
                    </>
                )}

            </div>
        </div>
    );
}