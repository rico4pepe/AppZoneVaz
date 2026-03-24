import React, { useEffect, useState } from 'react';
import PollCard from './PollCard';
import QuizCard from './QuizCard';
import TriviaCard from './TriviaCard';

const API_BASE = '/fanzone/api';

export default function FeedApp() {
    const [feed, setFeed] = useState([]);
    const [loading, setLoading] = useState(true);

    const token = localStorage.getItem('auth_token');

    useEffect(() => {
        const loadFeed = async () => {
            try {
                const res = await fetch(`${API_BASE}/feed`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const data = await res.json();
                setFeed(data);
            } catch (e) {
                console.error("Error loading feed", e);
            } finally {
                setLoading(false);
            }
        };

        loadFeed();
    }, []);

    if (loading) {
        return (
            <div className="d-flex justify-content-center p-4">
                <div className="spinner-border" />
            </div>
        );
    }

    if (!feed.length) {
        return <div className="alert alert-info">No content available</div>;
    }

    return (
        <div className="container mt-4">
            {feed.map(item => (
                 <FeedItem key={item.id} item={item} token={token} /> 
            ))}
        </div>
    );
}

function FeedItem({ item, token }) {
    if (item.type === 'poll') {
        return <PollCard poll={item} token={token} />;
    }

    if (item.type === 'quiz') {
        return <QuizCard quiz={item} token={token} />;
    }

    if (item.type === 'trivia') {
        return <TriviaCard trivia={item} token={token} />;
    }

    return null;
}




