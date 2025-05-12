import React, { useEffect, useState } from 'react';

export default function ChatApp() {
    const [messages, setMessages] = useState([]);
    const [message, setMessage] = useState('');
    const [eventId, setEventId] = useState(null);
    const [minimized, setMinimized] = useState(false);
    const [toast, setToast] = useState(null);

    const token = localStorage.getItem('auth_token');
    const username = document.getElementById('chat-app')?.dataset?.username;
    const userId = document.getElementById('chat-app')?.dataset?.user;

    const fetchMessages = async () => {
        try {
            const res = await fetch(`/api/chat?event_id=${eventId || ''}`, {
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            const newMessages = await res.json();
            setMessages(newMessages);

            // Detect mention
            const recent = newMessages[newMessages.length - 1];
            if (recent && recent.message.includes(`@${username}`)) {
                setToast(`You were mentioned by ${recent.user.name}`);
                setTimeout(() => setToast(null), 4000);
            }
        } catch (err) {
            console.error("Error fetching messages", err);
        }
    };

    useEffect(() => {
        if (token) fetchMessages();
    }, [eventId]);

    const sendMessage = async () => {
        if (!message.trim()) return;

        const res = await fetch('/api/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({ message, event_id: eventId })
        });

        if (res.ok) {
            const data = await res.json();
            setMessages([...messages, data.data]);
            setMessage('');
        } else {
            const err = await res.json();
            alert(err.message || "Failed to send message.");
        }
    };

    const parseMentions = (text) => {
        return text.replace(/@(\w+)/g, '<span class="text-primary fw-bold">@$1</span>');
    };

    return (
        <div className="chat-wrapper" style={{ position: 'fixed', bottom: 20, right: 20, zIndex: 9999 }}>
            <div className="card shadow" style={{ width: '100%', maxWidth: '500px' }}>
                <div className="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>💬 Fan Zone Chat</span>
                    <select
                        className="form-select mx-2"
                        value={eventId || ''}
                        onChange={e => setEventId(e.target.value || null)}
                    >
                        <option value="">🌍 Global Chat</option>
                        <option value="1">Event: Arsenal vs Chelsea</option>
                        <option value="2">Event: Nigeria vs Ghana</option>
                        {/* Replace with real dynamic list from backend */}
                    </select>
                    <button
                        className="btn btn-sm btn-light"
                        onClick={() => setMinimized(!minimized)}
                        title="Toggle"
                    >
                        {minimized ? '🔼' : '🔽'}
                    </button>
                </div>

                {!minimized && (
                    <>
                        <div className="card-body" style={{ height: 300, overflowY: 'auto', background: '#fffbea' }}>
                            {messages.map((msg, i) => (
                                <div key={i} className="mb-1">
                                    <strong>{msg.user.name}</strong>:&nbsp;
                                    <span dangerouslySetInnerHTML={{ __html: parseMentions(msg.message) }} />
                                </div>
                            ))}
                        </div>

                        <div className="card-footer d-flex">
                            <input
                                className="form-control me-2"
                                value={message}
                                onChange={e => setMessage(e.target.value)}
                                placeholder="Type your message..."
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
                            <button
                                type="button"
                                className="btn-close btn-close-white me-2 m-auto"
                                onClick={() => setToast(null)}
                            ></button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
