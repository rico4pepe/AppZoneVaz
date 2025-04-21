import React, { useState, useEffect } from 'react';

export default function ChatApp({ userId }) {
    const [messages, setMessages] = useState([]);
    const [message, setMessage] = useState('');
    const [eventId, setEventId] = useState(null);
    const [minimized, setMinimized] = useState(false);
    const [toast, setToast] = useState(null);

    const token = localStorage.getItem('auth_token');

    useEffect(() => {
        if (!token) return;
        fetch(`/api/chat?event_id=${eventId || ''}`, {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        })
            .then(res => res.json())
            .then(newMessages => {
                setMessages(newMessages);

                const recent = newMessages[newMessages.length - 1];
                if (recent && recent.message.includes(`@${username}`)) {
                    setToast(`You were mentioned by ${recent.user.name}`);
                    setTimeout(() => setToast(null), 4000);
                }
            });
    }, [eventId]);

    const sendMessage = async () => {
        const res = await fetch('/api/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message, event_id: eventId }),
        });

        if (res.ok) {
            const newMsg = await res.json();
            setMessages([...messages, newMsg]);
            setMessage('');
        }
    };

    const parseMentions = (text) => {
        return text.replace(/@(\w+)/g, '<span class="text-primary fw-bold">@$1</span>');
    };

    return (
        <div className="chat-wrapper" style={{ position: 'fixed', bottom: 20, right: 20, zIndex: 9999 }}>
            <div className="card shadow" style={{ width: '100%', maxWidth: '500px' }}>
                <div className="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>Fan Zone Chat</span>
                    <select className="form-select" title="select event to chat" value={eventId || ''} onChange={e => setEventId(e.target.value || null)}>
                        <option value="">🌍 Global Chat</option>
                        <option value="1">Event: Arsenal vs Chelsea</option>
                        <option value="2">Event: Nigeria vs Ghana</option>
                        {/* You can dynamically load from `/api/events` later */}
                    </select>
                    <button className="btn btn-sm btn-light" title="hide" onClick={() => setMinimized(!minimized)}>
                        {minimized ? '🔼' : '🔽'}
                    </button>
                </div>

                {!minimized && (
                    <>
                        <div className="card-body" style={{ height: 300, overflowY: 'auto', background: '#fffbea' }}>
                            {messages.map((msg, i) => (
                                <div key={i} className="mb-1">
                                    <strong>{msg.user.name}</strong>: <span dangerouslySetInnerHTML={{ __html: parseMentions(msg.message) }} />
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

            {/* Simple Toast Notification */}
            {toast && (
                <div className="position-fixed bottom-0 end-0 p-3" style={{ zIndex: 1050 }}>
                    <div className="toast show align-items-center text-bg-primary border-0">
                        <div className="d-flex">
                            <div className="toast-body">
                                {toast}
                            </div>
                            <button type="button" className="btn-close btn-close-white me-2 m-auto" onClick={() => setToast(null)}></button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
