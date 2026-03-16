import React, { useEffect, useState, useCallback, useRef } from 'react';

// Safe mention parser — no dangerouslySetInnerHTML
const MessageText = ({ text }) => {
    const parts = text.split(/(@\w+)/g);
    return (
        <span>
            {parts.map((part, i) =>
                part.startsWith('@')
                    ? <strong key={i} className="text-primary">{part}</strong>
                    : part
            )}
        </span>
    );
};

export default function ChatApp() {
    const [messages, setMessages]   = useState([]);
    const [message, setMessage]     = useState('');
    const [matchId, setMatchId]     = useState(null); // renamed from eventId
    const [rooms, setRooms]         = useState([]);
    const [minimized, setMinimized] = useState(false);
    const [toast, setToast]         = useState(null);
    const lastSeenIdRef             = useRef(null);
    const messagesEndRef            = useRef(null);
    const [fans, setFans] = useState(0);

    const token       = localStorage.getItem('auth_token');
    const chatElement = document.getElementById('chat-app');
    const username    = chatElement?.dataset?.username;
    const defaultRoom = chatElement?.dataset?.defaultRoom; // pass match_id from Blade

    // Auto-join match room if on match page
    useEffect(() => {
        if (defaultRoom) setMatchId(parseInt(defaultRoom));
    }, [defaultRoom]);

    // Fetch rooms
    useEffect(() => {
        if (!token) return;
        fetch('/fanzone/api/chat/rooms', {
            headers: { Authorization: `Bearer ${token}` }
        })
        .then(r => r.json())
        .then(setRooms)
        .catch(console.error);
    }, [token]);

    // Scroll to bottom on new messages
    useEffect(() => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    // Fetch messages — incremental after first load
    const fetchMessages = useCallback(async () => {
        try {
            const url = lastSeenIdRef.current
                ? `/fanzone/api/chat?match_id=${matchId || ''}&after=${lastSeenIdRef.current}`
                : `/fanzone/api/chat?match_id=${matchId || ''}`;

            const res  = await fetch(url, {
                headers: { Authorization: `Bearer ${token}` }
            });
            const data = await res.json();

            const { messages: newMessages, fans: activeFans } = data;
            setFans(activeFans);

            if (!Array.isArray(newMessages) || newMessages.length === 0) return;
            lastSeenIdRef.current = newMessages[newMessages.length - 1].id;
            setMessages(prev => {
                if (!lastSeenIdRef.current || prev.length === 0) return newMessages;
                return [...prev, ...newMessages];
            });
            if (username) {
                const mentioned = newMessages.find(m => m.message.includes(`@${username}`));
                if (mentioned) {
                    setToast(`You were mentioned by ${mentioned.user?.display_name || mentioned.user?.name}`);
                    setTimeout(() => setToast(null), 4000);
                }
            }

        } catch (err) {
            console.error("Error fetching messages", err);
        }
    }, [matchId, token, username]);

    // Reset + reload when room changes
    useEffect(() => {
        if (!token) return;
        lastSeenIdRef.current = null;
        setMessages([]);
        fetchMessages();
    }, [matchId, token]); // intentionally excludes fetchMessages

    // Poll every 5 seconds
    useEffect(() => {
        if (!token) return;
        const interval = setInterval(fetchMessages, 5000);
        return () => clearInterval(interval);
    }, [fetchMessages, token]);

    const sendMessage = async () => {
        if (!message.trim()) return;
        try {
            const res = await fetch('/fanzone/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`
                },
                body: JSON.stringify({ message, match_id: matchId }) // fixed
            });

            if (res.ok) {
                const data = await res.json();
                setMessages(prev => [...prev, data.data]);
                lastSeenIdRef.current = data.data.id;
                setMessage('');
            } else {
                const err = await res.json();
                alert(err.message || "Failed to send message.");
            }
        } catch (err) {
            console.error("Error sending message", err);
        }
    };

    const handleKeyDown = (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    };

    return (
        <div className="chat-wrapper" style={{ position: 'fixed', bottom: 20, right: 20, zIndex: 9999 }}>
            <div className="card shadow" style={{ width: '100%', maxWidth: '500px' }}>
                <div className="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                   <span>💬 Fan Zone Chat {fans > 0 && <small className="ms-2 opacity-75">👥 {fans} online</small>}</span>

                    <select
                        className="form-select mx-2"
                        value={matchId || ''}
                        onChange={e => {
                            lastSeenIdRef.current = null;
                            setMatchId(e.target.value ? parseInt(e.target.value) : null);
                        }}
                    >
                        {rooms.map(room => (
                            <option key={room.id ?? 'global'} value={room.id || ''}>
                                {room.name}
                            </option>
                        ))}
                    </select>

                    <button className="btn btn-sm btn-light" onClick={() => setMinimized(!minimized)}>
                        {minimized ? '🔼' : '🔽'}
                    </button>
                </div>

                {!minimized && (
                    <>
                        <div className="card-body" style={{ height: 300, overflowY: 'auto', background: '#fffbea' }}>
                           {messages.map((msg) => (

                            msg.type === 'system' ?

                            <div key={msg.id} className="text-center text-muted my-2">
                                {msg.message}
                            </div>

                            :

                            <div key={msg.id} className="mb-1">
                                <strong>{msg.user?.display_name || msg.user?.name}</strong>: <MessageText text={msg.message} />
                            </div>

                        ))}
                            <div ref={messagesEndRef} />
                        </div>

                        <div className="card-footer d-flex">
                            <input
                                className="form-control me-2"
                                value={message}
                                onChange={e => setMessage(e.target.value)}
                                onKeyDown={handleKeyDown}
                                placeholder="Type a message... use @name to mention"
                            />
                            <button className="btn btn-success" onClick={sendMessage}>Send</button>
                        </div>
                    </>
                )}
            </div>

            {toast && (
                <div className="position-fixed bottom-0 end-0 p-3" style={{ zIndex: 1050 }}>
                    <div className="toast show align-items-center text-bg-primary border-0">
                        <div className="d-flex">
                            <div className="toast-body">{toast}</div>
                            <button type="button" className="btn-close btn-close-white me-2 m-auto" onClick={() => setToast(null)} />
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}