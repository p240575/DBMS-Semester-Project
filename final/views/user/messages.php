<?php require 'views/layouts/header.php'; ?>
<style>
.messages-container { max-width: 800px; margin: 110px auto 3rem; padding: 0 1.5rem; }
.chat-card { background: var(--bg-card); border: 1px solid var(--glass-border); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; height: 70vh; }
.chat-header-bar { padding: 1.2rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.07); display: flex; align-items: center; gap: 1rem; }
.chat-header-bar .avatar { width: 42px; height: 42px; background: linear-gradient(135deg, #f59e0b, #ef4444); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold; color: #0f172a; flex-shrink: 0; }
.chat-messages { flex: 1; overflow-y: auto; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
.msg-bubble { max-width: 72%; padding: 0.75rem 1rem; border-radius: 14px; font-size: 0.9rem; line-height: 1.5; }
.msg-from-admin { background: linear-gradient(135deg, #f59e0b22, #ef444422); color: #cbd5e1; border: 1px solid rgba(245,158,11,0.3); align-self: flex-start; border-bottom-left-radius: 3px; }
.msg-from-customer { background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; align-self: flex-end; border-bottom-right-radius: 3px; }
.msg-time { font-size: 0.7rem; color: #475569; margin-top: 0.3rem; }
.chat-input-bar { padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.07); display: flex; gap: 0.8rem; background: rgba(0,0,0,0.2); }
.chat-input-bar input { flex: 1; padding: 0.75rem 1rem; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: white; font-size: 0.9rem; }
.chat-input-bar input::placeholder { color: #475569; }
.chat-input-bar button { padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #f59e0b, #ef4444); border: none; border-radius: 10px; color: white; font-weight: bold; cursor: pointer; transition: opacity 0.2s; }
.chat-input-bar button:hover { opacity: 0.9; }
</style>

<?php
if (!isset($_SESSION['user_id'])) { header("Location: /auth/login"); exit; }

require_once 'config/database.php';
$db = (new Database())->getConnection();

$userId = $_SESSION['user_id'];

// Get all messages for this user
$stmt = $db->prepare("SELECT * FROM Messages WHERE user_id = ? ORDER BY created_at ASC");
$stmt->execute([$userId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark admin messages as read
$db->prepare("UPDATE Messages SET is_read = 1 WHERE user_id = ? AND sender_role = 'admin'")->execute([$userId]);
?>

<div class="messages-container">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2>💬 Messages</h2>
        <div class="accent-line"></div>
        <p style="color: var(--text-secondary); margin-top: 0.5rem; font-size: 0.9rem;">Chat with NexShop support team</p>
    </div>

    <div class="chat-card">
        <div class="chat-header-bar">
            <div class="avatar">N</div>
            <div>
                <div style="color: white; font-weight: 600;">NexShop Support</div>
                <div style="color: #64748b; font-size: 0.8rem;">Admin Team · Usually responds within 24 hours</div>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <?php if(empty($messages)): ?>
                <div style="text-align: center; color: #475569; margin: auto; padding: 2rem;">
                    <div style="font-size: 2rem; margin-bottom: 1rem;">👋</div>
                    <p>Start a conversation with our support team!</p>
                    <p style="font-size: 0.85rem; margin-top: 0.5rem;">We're here to help with any questions about your orders, products, or account.</p>
                </div>
            <?php endif; ?>
            <?php foreach($messages as $msg): ?>
                <div style="display:flex; flex-direction:column; align-items:<?= $msg['sender_role'] === 'customer' ? 'flex-end' : 'flex-start' ?>;">
                    <div style="font-size: 0.75rem; color: #475569; margin-bottom: 0.2rem;">
                        <?= $msg['sender_role'] === 'admin' ? '🛡 NexShop Support' : '👤 You' ?>
                    </div>
                    <div class="msg-bubble msg-from-<?= $msg['sender_role'] ?>">
                        <?= nl2br(htmlspecialchars($msg['message'])) ?>
                    </div>
                    <div class="msg-time"><?= date('M d, Y · h:i A', strtotime($msg['created_at'])) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" action="/user/send_message" class="chat-input-bar">
            <input type="text" name="message" placeholder="Type your message to support..." required autocomplete="off" id="msgInput">
            <button type="submit">Send ➤</button>
        </form>
    </div>
</div>

<script>
var msgs = document.getElementById('chatMessages');
if(msgs) msgs.scrollTop = msgs.scrollHeight;
document.getElementById('msgInput').focus();
</script>

<?php require 'views/layouts/footer.php'; ?>
