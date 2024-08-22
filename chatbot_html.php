<div id="ai-chatbot-container" class="ai-chatbot-container">
    <div id="ai-chatbot-bubble" class="ai-chatbot-bubble">
        <?php if (!empty(get_option('ai_chatbot_settings')['bubble_image'])): ?>
            <img src="<?php echo esc_url(get_option('ai_chatbot_settings')['bubble_image']); ?>" alt="Chat">
        <?php else: ?>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"/><path d="M10 3h4a8 8 0 1 1 0 16v3.5c-5-2-12-5-12-11.5a8 8 0 0 1 8-8zm2 14h2a6 6 0 1 0 0-12h-4a6 6 0 0 0-6 6c0 3.61 2.462 5.966 8 8.48V17z" fill="#ffffff"/></svg>
        <?php endif; ?>
    </div>
    <div id="ai-chatbot-chat" class="ai-chatbot-chat ai-chatbot-window" style="display: none;">
        <div class="ai-chatbot-header">
            <?php if (!empty(get_option('ai_chatbot_settings')['avatar_image'])): ?>
                <img src="<?php echo esc_url(get_option('ai_chatbot_settings')['avatar_image']); ?>" alt="Avatar" class="ai-chatbot-avatar">
            <?php endif; ?>
            <h3 class="ai-chatbot-title"><?php echo esc_html(get_option('ai_chatbot_settings')['chatbot_name']); ?></h3>
            <button id="ai-chatbot-refresh" class="ai-chatbot-refresh">&#8635;</button>
            <button id="ai-chatbot-close" class="ai-chatbot-close">&times;</button>
        </div>
        <div id="ai-chatbot-messages" class="ai-chatbot-messages">
            <!-- Mesajlar buraya dinamik olarak eklenecek -->
        </div>
        <div class="ai-chatbot-input">
            <input type="text" id="ai-chatbot-input" placeholder="Type your message...">
            <button id="ai-chatbot-send" class="ai-chatbot-send">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"/><path d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z" fill="currentColor"/></svg>
            </button>
        </div>
        <div class="ai-chatbot-footer">
            Designed by Hasan Beker 
        </div>
    </div>
</div>