import React, { useState, useEffect } from 'react';
import useFeedInteraction from '../hooks/useFeedInteraction';
import Swal from 'sweetalert2';

export default function PollCard({ poll, token, onUpdate }) {
    const { interact } = useFeedInteraction(token);

    const [selectedOption, setSelectedOption] = useState(null);
    const [loading, setLoading] = useState(false);

    const [localPoll, setLocalPoll] = useState({
        ...poll,
        hasVoted: poll.hasVoted ?? false,
        results: poll.results || []
    });

    useEffect(() => {
        setLocalPoll({
            ...poll,
            hasVoted: poll.hasVoted ?? false,
            results: poll.results || []
        });
        setSelectedOption(null);
    }, [poll]);

    const submitVote = async () => {
        if (!selectedOption || loading) return;

        setLoading(true);

        try {
            const result = await interact({
                contentId: localPoll.id,
                answer: selectedOption
            });

            const updatedPoll = {
                ...localPoll,
                hasVoted: true,
                selected_option_id: result.answer,
                results: result.results
            };

            setLocalPoll(updatedPoll);
            setSelectedOption(null);

            if (onUpdate) {
                onUpdate(updatedPoll);
            }

            Swal.fire({
                icon: 'success',
                title: 'Vote submitted!',
                timer: 1200,
                showConfirmButton: false
            });

        } catch (e) {
            console.error('Vote error:', e);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="fz-card fz-poll-card">

            {/* HEADER */}
            <div className="fz-card-header">
                <span className="fz-section-title">Poll</span>
            </div>

            <div className="fz-card-body">

                {/* TITLE */}
                <div className="fz-poll-title">
                    {localPoll.title}
                </div>

                {/* BEFORE VOTE */}
                {!localPoll.hasVoted ? (
                    <div className="fz-poll-options">
                        {localPoll.options.map(option => (
                            <button
                                key={option.id}
                                type="button"
                                className={`fz-poll-option ${
                                    selectedOption === option.id ? 'active' : ''
                                }`}
                                onClick={() => setSelectedOption(option.id)}
                                disabled={loading}
                            >
                                {option.option_text}
                            </button>
                        ))}

                        <button
                            type="button"
                            className="fz-poll-submit"
                            onClick={submitVote}
                            disabled={!selectedOption || loading}
                        >
                            {loading ? 'Submitting...' : 'Vote'}
                        </button>
                    </div>
                ) : (

                    /* AFTER VOTE (RESULTS) */
                    <div className="fz-poll-results">
                        {localPoll.options.map(option => {
                            const result = localPoll.results.find(
                                r => Number(r.option_id) === Number(option.id)
                            );

                            const percentage = result?.percentage || 0;
                            const votes = result?.votes || 0;

                            const isSelected =
                                Number(localPoll.selected_option_id) === Number(option.id);

                            return (
                                <div key={option.id} className="fz-poll-result-item">

                                    <div className="fz-poll-result-head">
                                        <span className={`fz-poll-option-text ${
                                            isSelected ? 'selected' : ''
                                        }`}>
                                            {option.option_text}
                                        </span>
                                        <span className="fz-text-meta">
                                            {percentage}% ({votes})
                                        </span>
                                    </div>

                                    <div className="fz-poll-bar">
                                        <div
                                            className={`fz-poll-bar-fill ${
                                                isSelected ? 'selected' : ''
                                            }`}
                                            style={{ width: `${percentage}%` }}
                                        />
                                    </div>

                                </div>
                            );
                        })}
                    </div>
                )}

            </div>
        </div>
    );
}