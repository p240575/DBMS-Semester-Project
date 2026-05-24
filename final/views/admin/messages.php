<?php require 'views/layouts/admin_header.php'; ?>
<style>
.chat-container { display: flex; gap: 1.5rem; height: calc(100vh - 180px); }
.customer-list { width: 280px; flex-shrink: 0; background: #1e293b; border-radius: 12px; border: 1px solid #334155; overflow-y: auto; }
.customer-item { padding: 1rem 1.2rem; border-bottom: 1px solid #334155; cursor: pointer; transition: background 0.2s; text-decoration: none; display: block; }
.customer-item:hover, .customer-item.active { background: #334155; }
.customer-item .name { color: white; font-weight: 600; font-size: 0.95rem; }
.customer-item .preview { color: #64748b; font-size: 0.8rem; margin-top: 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.customer-item .unread-dot { display: inline-block; width: 8px; height: 8px; background: #f59e0b; border-radius: 50%; margin-left: 0.5rem; }
.chat-window { flex: 1; background: #1e293b; border-radius: 12px; border: 1px solid #334155; display: flex; flex-direction: column; overflow: hidden; }
.chat-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid #334155; }
.chat-header h3 { color: white; margin: 0; font-size: 1.1rem; }
.chat-header small { color: #64748b; }
.chat-messages { flex: 1; overflow-y: auto; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
.msg-bubble { max-width: 70%; padding: 0.7rem 1rem; border-radius: 12px; font-size: 0.9rem; line-height: 1.5; }
.msg-from-customer { background: #0f172a; color: #cbd5e1; border: 1px solid #334155; align-self: flex-start; border-bottom-left-radius: 3px; }
.msg-from-admin { background: #f59e0b; color: #0f172a; align-self: flex-end; border-bottom-right-radius: 3px; font-weight: 500; }
.msg-time { font-size: 0.7rem; opacity: 0.6; margin-top: 0.3rem; }
.chat-input { padding: 1rem 1.5rem; border-top: 1px solid #334155; display: flex; gap: 0.8rem; }
.chat-input input { flex: 1; padding: 0.7rem 1rem; border-radius: 8px; border: 1px solid #334155; background: #0f172a; color: white; font-size: 0.9rem; }
.chat-input button { padding: 0.7rem 1.5rem; background: #f59e0b; border: none; border-radius: 8px; color: #0f172a; font-weight: bold; cursor: pointer; }
.no-chat { display: flex; align-items: center; justify-content: center; height: 100%; color: #475569; flex-direction: column; gap: 1rem; font-size: 1.1rem; }
.list-header { padding: 1rem 1.2rem; border-bottom: 1px solid #334155; color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
</style>

<?php
$selectedUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

require_once 'config/database.php';
$db = (new Database())->getConnection();

// Get all customers who have any message exchange (or all customers)
$stmt = $db->query("
    SELECT u.user_id, u.user_name, u.email,
           (SELECT message FROM Messages WHERE user_id = u.user_id ORDER BY created_at DESC LIMIT 1) as last_msg,
           (SELECT created_at FROM Messages WHERE user_id = u.user_id ORDER BY created_at DESC LIMIT 1) as last_msg_time,
           (SELECT COUNT(*) FROM Messages WHERE user_id = u.user_id AND sender_role = 'customer' AND is_read = 0) as unread_count
    FROM Users u WHERE u.role = 'customer' ORDER BY last_msg_time DESC, u.user_name ASC
");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get messages for selected customer
$messages = [];
$selectedUser = null;
if ($selectedUserId) {
    $stmtU = $db->prepare("SELECT * FROM Users WHERE user_id = ?");
    $stmtU->execute([$selectedUserId]);
    $selectedUser = $stmtU->fetch(PDO::FETCH_ASSOC);
    
    $stmtM = $db->prepare("SELECT * FROM Messages WHERE user_id = ? ORDER BY created_at ASC");
    $stmtM->execute([$selectedUserId]);
    $messages = $stmtM->fetchAll(PDO::FETCH_ASSOC);
    
    // Mark customer messages as read
    $db->prepare("UPDATE Messages SET is_read = 1 WHERE user_id = ? AND sender_role = 'customer'")->execute([$selectedUserId]);
}
?>

<div class="admin-dashboard" style="height: calc(100vh - 100px);">
    <div style="margin-bottom: 1rem;">
        <h2>💬 Messages</h2>
        <p style="color:#64748b; font-size:0.85rem;">Chat with your customers directly.</p>
    </div>

    <div class="chat-container">
        <!-- Customer List -->
        <div class="customer-list">
            <div class="list-header">Customers</div>
            <?php foreach($customers as $c): ?>
                <a href="/admin/messages?user_id=<?= $c['user_id'] ?>" 
                   class="customer-item <?= ($selectedUserId == $c['user_id']) ? 'active' : '' ?>">
                    <div class="name">
                        <?= htmlspecialchars($c['user_name']) ?>
                        <?php if($c['unread_count'] > 0): ?>
                            <span class="unread-dot"></span>
                            <span style="font-size:0.7rem; color:#f59e0b;"><?= $c['unread_count'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="preview"><?= $c['last_msg'] ? htmlspecialchars(substr($c['last_msg'], 0, 40)) . '...' : 'No messages yet' ?></div>
                    <?php if($c['last_msg_time']): ?>
                        <div style="font-size:0.7rem; color:#475569; margin-top:0.2rem;"><?= date('M d, h:i A', strtotime($c['last_msg_time'])) ?></div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Chat Window -->
        <div class="chat-window">
            <?php if($selectedUser): ?>
                <div class="chat-header">
                    <h3><?= htmlspecialchars($selectedUser['user_name']) ?></h3>
                    <small><?= htmlspecialchars($selectedUser['email']) ?></small>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <?php if(empty($messages)): ?>
                        <div style="text-align:center; color:#475569; margin:auto;">No messages yet. Start the conversation!</div>
                    <?php endif; ?>
                    <?php foreach($messages as $msg): ?>
                        <div style="display:flex; flex-direction:column; align-items:<?= $msg['sender_role'] === 'admin' ? 'flex-end' : 'flex-start' ?>;">
                            <div style="font-size:0.75rem; color:#475569; margin-bottom:0.2rem;">
                                <?= $msg['sender_role'] === 'admin' ? 'You (Admin)' : htmlspecialchars($selectedUser['user_name']) ?>
                            </div>
                            <div class="msg-bubble msg-from-<?= $msg['sender_role'] === 'admin' ? 'admin' : 'customer' ?>">
                                <?= nl2br(htmlspecialchars($msg['message'])) ?>
                            </div>
                            <div class="msg-time"><?= date('M d, h:i A', strtotime($msg['created_at'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <form method="POST" action="/admin/send_message/<?= $selectedUserId ?>" class="chat-input">
                    <input type="text" name="message" placeholder="Type your message..." required autocomplete="off" id="msgInput">
                    <button type="submit">Send ➤</button>
                </form>
            <?php else: ?>
                <div class="no-chat">
                    💬
                    <span>Select a customer to view the conversation</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Auto-scroll chat to bottom
var msgs = document.getElementById('chatMessages');
if(msgs) msgs.scrollTop = msgs.scrollHeight;

// Focus input
var inp = document.getElementById('msgInput');
if(inp) inp.focus();
</script>

<?php require 'views/layouts/admin_footer.php'; ?>
