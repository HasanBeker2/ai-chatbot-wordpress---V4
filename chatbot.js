jQuery(document).ready(function ($) {
    const bubble = $('#ai-chatbot-bubble');
    const chat = $('#ai-chatbot-chat');
    const closeBtn = $('#ai-chatbot-close');
    const messages = $('#ai-chatbot-messages');
    const input = $('#ai-chatbot-input');
    const sendBtn = $('#ai-chatbot-send');

    bubble.on('click', function () {
        chat.toggle();
        bubble.toggle();
    });

    closeBtn.on('click', function () {
        chat.hide();
        bubble.show();
    });

    function addMessage(content, isUser = false) {
        const messageClass = isUser ? 'user-message' : 'bot-message';
        const bubbleColor = isUser ? aiChatbotSettings.user_bubble_color : aiChatbotSettings.bot_bubble_color;
        messages.append(`<div class="ai-chatbot-message ${messageClass}" style="background-color: ${bubbleColor};">${content}</div>`);
        messages.scrollTop(messages[0].scrollHeight);
    }

    function sendMessage() {
        const message = input.val().trim();
        if (message) {
            addMessage(message, true);
            input.val('');

            $.ajax({
                url: aiChatbotSettings.ajax_url,
                method: 'POST',
                data: {
                    action: 'ai_chatbot_message',
                    message: message
                },
                success: function (response) {
                    if (response.success) {
                        addMessage(response.data);
                    } else {
                        addMessage('Sorry, an error occurred. Please try again later.');
                    }
                },
                error: function () {
                    addMessage('Sorry, an error occurred. Please try again later.');
                }
            });
        }
    }

    sendBtn.on('click', sendMessage);
    input.on('keypress', function (e) {
        if (e.which === 13) {
            sendMessage();
        }
    });
});