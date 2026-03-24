import React, { useEffect, useState } from 'react';
import PollCard from './PollCard';
import QuizCard from './QuizCard';
import TriviaCard from './TriviaCard';
import { apiFetch } from '../utils/api';

export default function FeedApp() {
    const [feed, setFeed] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const token = localStorage.getItem('auth_token');

    useEffect(() => {
        const loadFeed = async () => {
            try {
                const data = await apiFetch('/feed');
                setFeed(data);
            } catch (e) {
                console.error("Error loading feed", e);
                setError("Failed to load feed");
            } finally {
                setLoading(false);
            }
        };

        loadFeed();
    }, []);

    /* =========================
       LOADING STATE (Skeleton)
    ========================= */
    if (loading) {
        return (
            <div className="fz-feed-list">
                {[1,2,3].map(i => (
                    <div key={i} className="fz-feed-skeleton" />
                ))}
            </div>
        );
    }

    /* =========================
       ERROR STATE
    ========================= */
    if (error) {
        return (
            <div className="fz-empty-state">
                <i className="fas fa-exclamation-circle"></i>
                <span>{error}</span>
            </div>
        );
    }

    /* =========================
       EMPTY STATE
    ========================= */
    if (!feed.length) {
        return (
            <div className="fz-empty-state">
                <i className="fas fa-layer-group"></i>
                <span>No content available</span>
            </div>
        );
    }

    return (
        <div className="fz-feed-list">
            {feed.map(item => (
                <FeedItem
                    key={item.id}
                    item={item}
                    token={token}
                    onUpdate={(updatedItem) => {
                        setFeed(prev =>
                            prev.map(f =>
                                f.id === updatedItem.id ? updatedItem : f
                            )
                        );
                    }}
                />
            ))}
        </div>
    );
}

const componentMap = {
    poll: PollCard,
    quiz: QuizCard,
    trivia: TriviaCard,
};

function FeedItem({ item, token, onUpdate }) {
    const Component = componentMap[item.type];
    if (!Component) return null;

    return (
        <div className="fz-feed-item">
            <Component
                {...{ [item.type]: item }}
                token={token}
                onUpdate={onUpdate}
            />
        </div>
    );
}