(function() {
    // Load external libraries dynamically
    function loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    // Load marked and DOMPurify
    async function loadLibraries() {
        try {
            await loadScript('https://cdn.jsdelivr.net/npm/marked/marked.min.js');
            await loadScript('https://cdn.jsdelivr.net/npm/dompurify@3.0.6/dist/purify.min.js');
            return true;
        } catch (error) {
            console.error('Failed to load libraries:', error);
            return false;
        }
    }

    const API_KEY = '{{ $apiKey }}';
    const API_URL = '{{ url('/') }}';
    const API_BASE = '{{ url('/api/saas/v1') }}';

    // Prevent multiple instances
    if (window.AIChatWidget) return;

    // Widget state
    let isOpen = false;
    let sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    let lastMessageId = 0;
    let pollInterval = null;
    let isLoading = false;
    let librariesLoaded = false;

    // DOM elements
    let triggerButton = null;
    let chatWindow = null;
    let messagesContainer = null;
    let inputField = null;
    let sendButton = null;

    // Pre-chat form data
    let preChatData = null;

    // Bot settings
    const settings = {
        title: {!! $title !!},
        welcomeMsg: {!! $welcomeMsg !!},
        placeholder: {!! $placeholder !!},
        btnText: {!! $btnText !!},
        color: {!! $color !!},
        bgColor: {!! $bg !!},
        textColor: {!! $textColor !!},
        position: {
            bottom: {!! $posBottom !!},
            right: {!! $posRight !!},
            left: {!! $posLeft !!}
        },
        triggerIcon: `{!! $triggerIcon !!}`,
        triggerBgCss: `{!! $triggerBgCss !!}`,
        triggerRadius: {!! $triggerRadius !!},
        clearOnClose: {!! $clearOnClose !!},
        preChatEnabled: {!! $preChatEnabled !!},
        preChatMsg: {!! $preChatMsg !!},
        preChatNameLabel: {!! $preChatNameLabel !!},
        preChatPhoneLabel: {!! $preChatPhoneLabel !!},
        preChatBtnText: {!! $preChatBtnText !!},
        preChatErrorMsg: {!! $preChatErrorMsg !!}
    };

    // Create widget styles
    const styles = `
        .ai-chat-widget * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .ai-chat-trigger {
            position: fixed;
            bottom: ${settings.position.bottom};
            ${settings.position.left !== 'auto' ? `left: ${settings.position.left};` : `right: ${settings.position.right};`}
            width: 60px;
            height: 60px;
            border-radius: ${settings.triggerRadius};
            ${settings.triggerBgCss}
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            transition: transform 0.2s ease;
            z-index: 999999;
            border: none;
            outline: none;
        }

        .ai-chat-trigger img {
            width: 100%;
            height: 100%;
            border-radius: ${settings.triggerRadius};
            object-fit: cover;
            pointer-events: none;
        }

        .ai-chat-trigger:hover {
            transform: scale(1.05);
        }

        .ai-chat-window {
            position: fixed;
            max-height: calc(100vh - 100px) !important;
            bottom: calc(${settings.position.bottom} + 70px);
            ${settings.position.left !== 'auto' ? `left: ${settings.position.left};` : `right: ${settings.position.right};`}
            width: 380px;
            height: 600px;
            background: ${settings.bgColor};
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            display: flex;
            flex-direction: column;
            z-index: 999998;
            transition: all 0.3s ease;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow: hidden;
        }

        .ai-chat-window input, .ai-chat-window input:focus {
            color: ${settings.textColor} !important;
        }

        .ai-chat-window.closed {
            display: none;
        }

        .ai-chat-header {
            background: ${settings.color};
            color: white;
            padding: 16px;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ai-chat-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .ai-chat-close {
            cursor: pointer;
            font-size: 20px;
            background: none;
            border: none;
            color: white;
            font-weight: bold;
        }

        .ai-chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: ${settings.bgColor};
        }

        .ai-chat-message {
            display: flex;
            gap: 8px;
            animation: slideIn 0.3s ease;
        }

        .ai-chat-message.user {
            justify-content: flex-end;
        }

        .ai-chat-message.bot {
            justify-content: flex-start;
        }

        .ai-chat-bubble {
            max-width: 70%;
            padding: 10px 14px;
            border-radius: 18px;
            word-wrap: break-word;
            font-size: 14px;
            line-height: 1.5;
        }

        .ai-chat-message.user .ai-chat-bubble {
            background: ${settings.color};
            color: white;
            border-bottom-right-radius: 4px;
        }

        .ai-chat-message.bot .ai-chat-bubble {
            background: #f0f0f0;
            color: ${settings.textColor};
            border-bottom-left-radius: 4px;
        }

        .ai-chat-message.bot .ai-chat-bubble a {
            color: ${settings.color};
            text-decoration: underline;
        }

        .ai-chat-message.bot .ai-chat-bubble code {
            background: #e5e7eb;
            padding: 2px 4px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
        }

        .ai-chat-message.bot .ai-chat-bubble pre {
            background: #1f2937;
            color: #f3f4f6;
            padding: 12px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 8px 0;
        }

        .ai-chat-input-area {
            padding: 16px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 8px;
            background: ${settings.bgColor};
        }

        .ai-chat-input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            outline: none;
            font-size: 14px;
            background: white;
        }

        .ai-chat-input:focus {
            border-color: ${settings.color};
            box-shadow: 0 0 0 2px rgba(0,0,0,0.05);
        }

        .ai-chat-send {
            background: ${settings.color};
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 24px;
            cursor: pointer;
            font-weight: 500;
            transition: opacity 0.2s;
        }

        .ai-chat-send:hover {
            opacity: 0.9;
        }

        .ai-chat-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .ai-chat-prechat {
            padding: 24px;
            text-align: center;
        }

        .ai-chat-prechat h4 {
            margin-bottom: 20px;
            color: ${settings.textColor};
            font-size: 16px;
            line-height: 1.5;
        }

        .ai-chat-prechat input {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
        }

        .ai-chat-prechat input:focus {
            border-color: ${settings.color};
        }

        .ai-chat-prechat button {
            width: 100%;
            background: ${settings.color};
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            margin-top: 8px;
        }

        .ai-chat-error {
            background: #fee2e2;
            color: #dc2626;
            padding: 10px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 12px;
            display: none;
        }

        .ai-chat-typing {
            display: flex;
            gap: 4px;
            padding: 10px 14px;
            background: #f0f0f0;
            border-radius: 18px;
            width: fit-content;
        }

        .ai-chat-typing span {
            width: 8px;
            height: 8px;
            background: #999;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .ai-chat-typing span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .ai-chat-typing span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.4;
            }
            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .ai-chat-window {
                width: 100%;
                height: 100%;
                bottom: 0;
                ${settings.position.left !== 'auto' ? `left: 0;` : `right: 0;`}
                border-radius: 0;
            }
        }
    `;

    // Parse markdown with DOMPurify
    function parseMarkdown(text) {
        if (typeof marked !== 'undefined' && typeof DOMPurify !== 'undefined') {
            try {
                // Configure marked for safe rendering
                marked.setOptions({
                    breaks: true,
                    gfm: true,
                    headerIds: false,
                    mangle: false
                });
                const rawHtml = marked.parse(text);
                return DOMPurify.sanitize(rawHtml, {
                    ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 'code', 'pre', 'ul', 'ol', 'li', 'a', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    ALLOWED_ATTR: ['href', 'target', 'rel']
                });
            } catch (e) {
                console.error('Markdown parse error:', e);
                return escapeHtml(text);
            }
        }
        return escapeHtml(text);
    }

    // Helper function to send message to API
    async function sendMessage(message) {
        if (isLoading) return null;
        isLoading = true;

        try {
            const response = await fetch(`${API_BASE}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    api_key: API_KEY,
                    session_id: sessionId,
                    message: message
                })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            return data.answer || 'Sorry, I encountered an error.';
        } catch (error) {
            console.error('Error sending message:', error);
            return 'Sorry, I encountered an error. Please try again later.';
        } finally {
            isLoading = false;
        }
    }

    // Poll for new messages
    async function pollMessages() {
        if (!isOpen) return;

        try {
            const response = await fetch(`${API_BASE}/poll?api_key=${API_KEY}&session_id=${sessionId}&last_id=${lastMessageId}`);
            const data = await response.json();

            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(message => {
                    if (message.id > lastMessageId) {
                        addMessageToChat(message.content, message.role === 'user');
                        lastMessageId = message.id;
                    }
                });
            }
        } catch (error) {
            console.error('Error polling messages:', error);
        }
    }

    // Add message to chat
    function addMessageToChat(text, isUser) {
        if (!messagesContainer) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `ai-chat-message ${isUser ? 'user' : 'bot'}`;

        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'ai-chat-bubble';

        if (isUser) {
            bubbleDiv.textContent = text;
        } else {
            bubbleDiv.innerHTML = parseMarkdown(text);
        }

        messageDiv.appendChild(bubbleDiv);
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Show typing indicator
    function showTyping() {
        if (!messagesContainer) return;

        const typingDiv = document.createElement('div');
        typingDiv.className = 'ai-chat-message bot';
        typingDiv.id = 'ai-typing-indicator';
        typingDiv.innerHTML = `
            <div class="ai-chat-typing">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
        messagesContainer.appendChild(typingDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Remove typing indicator
    function removeTyping() {
        const typing = document.getElementById('ai-typing-indicator');
        if (typing) typing.remove();
    }

    // Handle sending message
    async function handleSendMessage() {
        if (!inputField) return;

        const message = inputField.value.trim();
        if (!message || isLoading) return;

        inputField.value = '';
        inputField.disabled = true;
        sendButton.disabled = true;

        addMessageToChat(message, true);
        showTyping();

        const response = await sendMessage(message);
        removeTyping();
        addMessageToChat(response, false);

        inputField.disabled = false;
        sendButton.disabled = false;
        inputField.focus();
    }

    // Initialize chat
    async function initChat() {
        if (!chatWindow) return;

        messagesContainer = chatWindow.querySelector('.ai-chat-messages');
        inputField = chatWindow.querySelector('.ai-chat-input');
        sendButton = chatWindow.querySelector('.ai-chat-send');

        // Clear messages if needed
        if (settings.clearOnClose) {
            messagesContainer.innerHTML = '';
        }

        // Add welcome message if chat is empty
        if (messagesContainer.children.length === 0) {
            addMessageToChat(settings.welcomeMsg, false);
        }

        // Setup event listeners
        inputField.removeEventListener('keypress', handleKeyPress);
        inputField.addEventListener('keypress', handleKeyPress);
        sendButton.removeEventListener('click', handleSendMessage);
        sendButton.addEventListener('click', handleSendMessage);

        // Start polling
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(pollMessages, 3000);
    }

    function handleKeyPress(e) {
        if (e.key === 'Enter') handleSendMessage();
    }

    // Show pre-chat form
    function showPreChatForm() {
        if (!chatWindow) return;

        messagesContainer = chatWindow.querySelector('.ai-chat-messages');
        messagesContainer.innerHTML = '';

        const preChatDiv = document.createElement('div');
        preChatDiv.className = 'ai-chat-prechat';
        preChatDiv.innerHTML = `
            <div id="ai-prechat-error" class="ai-chat-error">${escapeHtml(settings.preChatErrorMsg)}</div>
            <input type="text" id="ai-prechat-name" placeholder="${escapeHtml(settings.preChatNameLabel)}" />
            <input type="tel" id="ai-prechat-phone" placeholder="${escapeHtml(settings.preChatPhoneLabel)}" />
            <button id="ai-prechat-submit">${escapeHtml(settings.preChatBtnText)}</button>
        `;

        messagesContainer.appendChild(preChatDiv);

        const submitBtn = preChatDiv.querySelector('#ai-prechat-submit');
        const nameInput = preChatDiv.querySelector('#ai-prechat-name');
        const phoneInput = preChatDiv.querySelector('#ai-prechat-phone');
        const errorDiv = preChatDiv.querySelector('#ai-prechat-error');

        errorDiv.style.display = 'none';

        submitBtn.addEventListener('click', async () => {
            const name = nameInput.value.trim();
            const phone = phoneInput.value.trim();

            if (!name || !phone) {
                errorDiv.style.display = 'block';
                return;
            }

            errorDiv.style.display = 'none';
            preChatData = { name, phone };

            // Send lead data to server
            try {
                await fetch(`${API_BASE}/capture-lead`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        api_key: API_KEY,
                        session_id: sessionId,
                        name: name,
                        phone: phone
                    })
                });
            } catch (error) {
                console.error('Error saving lead:', error);
            }

            messagesContainer.innerHTML = '';
            addMessageToChat(settings.welcomeMsg, false);
            await initChat();
        });

        nameInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') submitBtn.click();
        });

        phoneInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') submitBtn.click();
        });
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Create widget
    async function createWidget() {
        // Load libraries first
        librariesLoaded = await loadLibraries();

        if (!librariesLoaded) {
            console.warn('Markdown libraries failed to load, using plain text');
        }

        // Add styles
        const styleSheet = document.createElement('style');
        styleSheet.textContent = styles;
        document.head.appendChild(styleSheet);

        // Create trigger button
        triggerButton = document.createElement('button');
        triggerButton.className = 'ai-chat-trigger';
        triggerButton.innerHTML = settings.triggerIcon;
        triggerButton.setAttribute('aria-label', 'Open chat');
        document.body.appendChild(triggerButton);

        // Create chat window
        chatWindow = document.createElement('div');
        chatWindow.className = 'ai-chat-window closed';
        chatWindow.innerHTML = `
            <div class="ai-chat-header">
                <h3>${escapeHtml(settings.title)}</h3>
                <button class="ai-chat-close">✕</button>
            </div>
            <div class="ai-chat-messages"></div>
            <div class="ai-chat-input-area">
                <input type="text" class="ai-chat-input" placeholder="${escapeHtml(settings.placeholder)}" />
                <button class="ai-chat-send">${escapeHtml(settings.btnText)}</button>
            </div>
        `;
        document.body.appendChild(chatWindow);

        // Toggle chat
        triggerButton.addEventListener('click', () => {
            isOpen = !isOpen;
            if (isOpen) {
                chatWindow.classList.remove('closed');
                if (settings.preChatEnabled && !preChatData) {
                    showPreChatForm();
                } else {
                    initChat();
                }
            } else {
                chatWindow.classList.add('closed');
                if (settings.clearOnClose && pollInterval) {
                    clearInterval(pollInterval);
                    pollInterval = null;
                }
            }
        });

        // Close button
        const closeBtn = chatWindow.querySelector('.ai-chat-close');
        closeBtn.addEventListener('click', () => {
            isOpen = false;
            chatWindow.classList.add('closed');
            if (settings.clearOnClose && pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
        });
    }

    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createWidget);
    } else {
        createWidget();
    }

    // Expose API
    window.AIChatWidget = {
        open: () => {
            if (triggerButton && !isOpen) triggerButton.click();
        },
        close: () => {
            if (triggerButton && isOpen) triggerButton.click();
        },
        send: async (message) => {
            if (chatWindow && isOpen && !isLoading) {
                return await sendMessage(message);
            }
        },
        isOpen: () => isOpen
    };
})();
