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
    const intervalRef = useRef(null);
    const delayRef = useRef(3000);
    const [typingUsers, setTypingUsers] = useState([]);
    const typingTimeoutRef = useRef(null);

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
    const container = messagesEndRef.current?.parentElement;
    if (!container) return;

    const isAtBottom =
        container.scrollHeight - container.scrollTop <= container.clientHeight + 50;

    if (isAtBottom) {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }
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

            const { messages: newMessages, fans: activeFans, typing  } = data;
            setFans(activeFans);
            setTypingUsers(typing || []);

            if (!Array.isArray(newMessages) || newMessages.length === 0) return;
            lastSeenIdRef.current = newMessages[newMessages.length - 1].id;
            setMessages(prev => {
                const existingIds = new Set(prev.map(m => m.id));
                const filtered = newMessages.filter(m => !existingIds.has(m.id));

                if (prev.length === 0) return newMessages;

                return [...prev, ...filtered];
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

    const sendTyping = async () => {
    try {
        await fetch('/fanzone/api/chat/typing', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token}`
            },
            body: JSON.stringify({ match_id: matchId })
        });
    } catch (err) {
        console.error('Typing error', err);
    }
};

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

    const stopPolling = () => {
        if (intervalRef.current) {
            clearInterval(intervalRef.current);
            intervalRef.current = null;
        }
    };

    const startPolling = () => {
        stopPolling();

        intervalRef.current = setInterval(async () => {
            const before = lastSeenIdRef.current;

            await fetchMessages();

            const after = lastSeenIdRef.current;

            // 🔥 Adaptive polling
            if (before === after) {
                delayRef.current = Math.min(delayRef.current + 1000, 10000);
            } else {
                delayRef.current = 3000;
            }

            startPolling(); // restart with new delay
        }, delayRef.current);
    };

    const handleVisibility = () => {
        if (document.hidden) {
            stopPolling();
        } else {
            delayRef.current = 3000;
            startPolling();
        }
    };

    document.addEventListener('visibilitychange', handleVisibility);

    startPolling();

    return () => {
        stopPolling();
        document.removeEventListener('visibilitychange', handleVisibility);
    };

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
    <div className="fz-chat">

        {/* HEADER */}
        <div className="fz-chat-header">
            <div className="fz-chat-title">
                💬 Fan Chat
                {fans > 0 && <span className="fz-chat-fans">👥 {fans}</span>}
            </div>

            <select
                className="fz-chat-room"
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
        </div>

        {/* MESSAGES */}
        <div className="fz-chat-body">

            {messages.map(msg => {

                if (msg.type === 'system') {
                    return (
                        <div key={msg.id} className="fz-chat-system">
                            {msg.message}
                        </div>
                    );
                }

                const isMe = msg.user?.name === username;

                return (
                    <div
                        key={msg.id}
                        className={`fz-chat-row ${isMe ? 'me' : ''}`}
                    >
                        <div className="fz-chat-bubble">

                            {!isMe && (
                                <div className="fz-chat-user">
                                    {msg.user?.display_name || msg.user?.name}
                                </div>
                            )}

                            <div className="fz-chat-text">
                                <MessageText text={msg.message} />
                            </div>

                        </div>
                    </div>
                );
            })}

            {/* Typing */}
            {typingUsers.length > 0 && (
                <div className="fz-chat-typing">
                    {typingUsers.length === 1
                        ? `${typingUsers[0]} is typing...`
                        : 'People are typing...'}
                </div>
            )}

            <div ref={messagesEndRef} />
        </div>

        {/* INPUT */}
        <div className="fz-chat-input">

            <input
                className="fz-chat-textbox"
                value={message}
                onChange={e => {
                    setMessage(e.target.value);

                    if (!typingTimeoutRef.current) {
                        sendTyping();
                        typingTimeoutRef.current = setTimeout(() => {
                            typingTimeoutRef.current = null;
                        }, 2000);
                    }
                }}
                onKeyDown={handleKeyDown}
                placeholder="Message..."
            />

            <button
                className="fz-chat-send"
                onClick={sendMessage}
            >
                ➤
            </button>

        </div>

        {/* TOAST */}
        {toast && (
            <div className="fz-chat-toast">
                {toast}
            </div>
        )}
    </div>
);
}