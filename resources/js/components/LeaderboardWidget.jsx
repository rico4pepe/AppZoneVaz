import React, { useEffect, useState } from 'react';

export default function LeaderboardWidget({ token, userId }) {
    const [leaders, setLeaders] = useState([]);
    const [myRank, setMyRank] = useState(null);
    const [loading, setLoading] = useState(true);
    const getBadge = (rank) => {
    if (rank === 1) return "🥇";
    if (rank === 2) return "🥈";
    if (rank === 3) return "🥉";
    return `#${rank}`;
};
    const authToken = token || localStorage.getItem('auth_token');
    useEffect(() => {
        const fetchData = async () => {
            try {
                const headers = {
                    'Authorization': `Bearer ${authToken}`
                };

                // 🔹 Fetch leaderboard
                const res1 = await fetch('/fanzone/api/leaderboard', { headers });
                const data1 = await res1.json();

                // 🔹 Fetch my rank
                const res2 = await fetch('/fanzone/api/leaderboard/me', { headers });
                const data2 = await res2.json();

                setLeaders(data1.leaderboard || []);
                setMyRank(data2);

            } catch (e) {
                console.error("Leaderboard error", e);
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    if (loading) {
        return <p className="text-muted">Loading leaderboard...</p>;
    }
    if (!leaders.length) {
    return <p className="text-muted">No rankings yet.</p>;
}
    return (
        <div>

            {/* 🔥 My Rank */}
           {myRank && myRank.rank ? (
            <div className="alert alert-info py-2">
                You are ranked <strong>#{myRank.rank}</strong> with {myRank.points} pts
            </div>
        ) : (
            <div className="alert alert-secondary py-2">
                You are not ranked yet. Start playing!
            </div>
        )}

            {/* 🔥 Leaderboard List */}
            {leaders.map(user => (
                <div
                    key={user.user_id}
                    className={`d-flex justify-content-between mb-2 ${
                        user.user_id == Number(userId) ? 'fw-bold text-primary' : ''
                    }`}
                >
                    <div>
                        {getBadge(user.rank)} {user.name}
                    </div>
                    <div>
                        {user.points} pts
                    </div>
                    {myRank && myRank.rank > leaders.length && (
                    <div className="mt-2 text-muted small">
                        You are outside top {leaders.length}
                    </div>
                )}
                </div>
            ))}

        </div>
    );
}