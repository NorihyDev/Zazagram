// ============================================================
// posts.js — Likes, Comments, Post interactions
// ============================================================

const BASE = document.querySelector('meta[name="base-url"]')?.content
    || window.location.origin + '/Zazagram_Website';

// ── Like / Unlike ─────────────────────────────────────────
function toggleLike(postId, btn) {
    btn.disabled = true;
    fetch(BASE + '/api/toggle_like.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ post_id: postId }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = (data.liked ? '❤️' : '🤍') +
                `<span class="like-count">${data.count}</span>`;
            btn.classList.toggle('liked', data.liked);
        } else {
            alert(data.error || 'Error liking post.');
        }
    })
    .catch(() => alert('Network error.'))
    .finally(() => btn.disabled = false);
}

// ── Toggle Comments Section ───────────────────────────────
function toggleComments(postId) {
    const section = document.getElementById('comments-' + postId);
    if (!section) return;
    const isHidden = section.style.display === 'none';
    section.style.display = isHidden ? 'block' : 'none';
    if (isHidden) loadComments(postId);
}

// ── Load Comments ─────────────────────────────────────────
function loadComments(postId) {
    const list = document.getElementById('comments-list-' + postId);
    if (!list) return;
    fetch(BASE + '/api/get_comments.php?post_id=' + postId)
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        list.innerHTML = '';
        data.comments.forEach(c => appendComment(list, c));
    });
}

function appendComment(container, c) {
    const div = document.createElement('div');
    div.className = 'comment';
    div.innerHTML = `<a href="${BASE}/pages/profile.php?username=${encodeURIComponent(c.username)}">
        <strong>${escHtml(c.username)}</strong></a> ${escHtml(c.content)}`;
    container.appendChild(div);
}

// ── Submit Comment ────────────────────────────────────────
function submitComment(event, postId) {
    event.preventDefault();
    const form  = event.target;
    const input = form.querySelector('.comment-input');
    const content = input.value.trim();
    if (!content) return;

    const btn = form.querySelector('button');
    btn.disabled = true;

    fetch(BASE + '/api/add_comment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ post_id: postId, content }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const list = document.getElementById('comments-list-' + postId);
            if (list) appendComment(list, data.comment);
            input.value = '';
            // Update comment count button
            const toggleBtn = document.querySelector(`#post-${postId} .comment-toggle-btn span`);
            if (toggleBtn) toggleBtn.textContent = parseInt(toggleBtn.textContent || 0) + 1;
        } else {
            alert(data.error || 'Error posting comment.');
        }
    })
    .catch(() => alert('Network error.'))
    .finally(() => btn.disabled = false);
}

// ── Escape HTML helper ────────────────────────────────────
function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}
