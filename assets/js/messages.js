// ============================================================
// messages.js — Real-time-style message polling
// ============================================================

let lastMessageId = 0;
let pollInterval  = null;

// Initialise: find the last message id already rendered
document.querySelectorAll('.msg').forEach(el => {
    const id = parseInt(el.dataset.id || 0);
    if (id > lastMessageId) lastMessageId = id;
});

if (typeof ACTIVE_ID !== 'undefined' && ACTIVE_ID) {
    // Start polling every 3 seconds
    pollInterval = setInterval(() => pollMessages(ACTIVE_ID), 3000);
}

function pollMessages(otherId) {
    fetch(`${BASE_URL}/api/get_messages.php?user_id=${otherId}&last_id=${lastMessageId}`)
    .then(r => r.json())
    .then(data => {
        if (!data.success || !data.messages.length) return;
        const container = document.getElementById('chat-messages');
        if (!container) return;
        data.messages.forEach(m => {
            if (m.id <= lastMessageId) return;
            lastMessageId = m.id;
            const div = document.createElement('div');
            div.className = 'msg ' + (m.sender_id == data.my_id ? 'msg-out' : 'msg-in');
            div.dataset.id = m.id;
            div.innerHTML = `<div class="msg-bubble">${escMsg(m.content)}</div>
                             <span class="msg-time">${formatTime(m.created_at)}</span>`;
            container.appendChild(div);
        });
        container.scrollTop = container.scrollHeight;
    })
    .catch(() => {});
}

function sendMessage(event, receiverId) {
    event.preventDefault();
    const input   = document.getElementById('msg-input');
    const content = input.value.trim();
    if (!content) return;

    const btn = event.target.querySelector('button');
    btn.disabled = true;

    fetch(`${BASE_URL}/api/send_message.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ receiver_id: receiverId, content }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            const container = document.getElementById('chat-messages');
            if (container) {
                const m   = data.message;
                lastMessageId = Math.max(lastMessageId, m.id);
                const div = document.createElement('div');
                div.className = 'msg msg-out';
                div.dataset.id = m.id;
                div.innerHTML = `<div class="msg-bubble">${escMsg(m.content)}</div>
                                 <span class="msg-time">${formatTime(m.created_at)}</span>`;
                container.appendChild(div);
                container.scrollTop = container.scrollHeight;
            }
        } else {
            alert(data.error || 'Error sending message.');
        }
    })
    .catch(() => alert('Network error.'))
    .finally(() => btn.disabled = false);
}

function escMsg(str) {
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/\n/g,'<br>');
}

function formatTime(iso) {
    const d = new Date(iso);
    return d.getHours().toString().padStart(2,'0') + ':' +
           d.getMinutes().toString().padStart(2,'0');
}
