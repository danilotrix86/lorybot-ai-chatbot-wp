function getCookie(name) {
    let cookieValue = null;
    if (document.cookie && document.cookie !== '') {
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i].trim();
            if (cookie.startsWith(name + '=')) {
                cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                break;
            }
        }
    }
    return cookieValue;
}



document.addEventListener("DOMContentLoaded", function() {
    function initializeChatbot() {
        const userId = getCookie('user_id');
        console.log("user_id: " + userId);
        if (userId) {
            const rootStyle = document.documentElement.style;
            rootStyle.setProperty('--main-color', chatbot_vars.main_color);
            rootStyle.setProperty('--background-color', chatbot_vars.background_color);
            rootStyle.setProperty('--title-color', chatbot_vars.title_color);

            const chatbotToggler = document.querySelector(".chatbot-toggler");
            const closeBtn = document.querySelector(".close-btn");
            const chatbox = document.querySelector(".chatbox");
            const chatInput = document.querySelector(".chat-input textarea");
            const sendChatBtn = document.querySelector(".chat-input span");
            const inputInitHeight = chatInput.scrollHeight;

            const createChatLi = (message, className, isBot = false) => {
                const chatLi = document.createElement("li");
                chatLi.className = `chat ${className}`;
                chatLi.innerHTML = isBot ? `<span class="material-symbols-outlined">smart_toy</span><p>${message}</p>` : `<p>${message}</p>`;
                return chatLi;
            };

            const appendMessageToChatbox = (message, className, isBot = false) => {
                let chatLi;
                let messageP;
            
                // Check if the last message in the chatbox is from the server
                if (isBot && chatbox.lastChild && chatbox.lastChild.classList.contains('incoming')) {
                    chatLi = chatbox.lastChild;
                    messageP = chatLi.querySelector("p");
                    
                    // Check if the current content is "Thinking..." and replace it, otherwise append
                    if (messageP.innerHTML === "Thinking...") {
                        messageP.innerHTML = message;
                    } else {
                        messageP.innerHTML += message;
                    }
                } else {
                    chatLi = createChatLi(message, className, isBot);
                    chatbox.appendChild(chatLi);
                }
            
                chatbox.scrollTo(0, chatbox.scrollHeight);
            };

            const createAndSendParams = (message) => {
                var params = new URLSearchParams();
                params.append('action', 'process_chatbot_message');
                params.append('message', message);
                params.append('client_id', chatbot_vars.client_id);
                params.append('openai_key', chatbot_vars.openai_key);
                params.append('prompt', chatbot_vars.prompt);
                return params;
            };

            function processFinalMessage(pElement) {
                console.log("Final message before processing:", pElement.innerHTML);


                // Transform markdown-style links to HTML hyperlinks
                const markdownLinkRegex = /\[([^\]]+)\]\((http[^)]+)\)/g;
                pElement.innerHTML = pElement.innerHTML.replace(markdownLinkRegex, "<a href='$2'>$1</a>");

                // Transform plain text URLs into clickable links
                const urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#/%=~_|$?!:,.*()[]]+)/ig;
                pElement.innerHTML = pElement.innerHTML.replace(urlRegex, "<a href='$1'>$1</a>");
                // Transform bold text
                const boldRegex = /\*\*(.*?)\*\*/g;
                pElement.innerHTML = pElement.innerHTML.replace(boldRegex, "<strong>$1</strong>");


                console.log("Final message after processing:", pElement.innerHTML);
            }

            const handleErrorResponse = (chatElement) => {
                const messageElement = chatElement.querySelector("p");
                messageElement.classList.add("error");
                messageElement.innerHTML = "Oooops! Something went wrong. Please try again.";
            };

            const generateResponse = (userMessage, userId) => {
                const customId = chatbot_vars.custom_id; // Assuming this is how you get customId
                const encodedMessage = encodeURIComponent(userMessage);
                const serverURL = chatbot_vars.server_url;
                console.log ("serverURL: " + serverURL, "customId: " + customId, "encodedMessage: " + encodedMessage, "userId: " + userId);
                const url = `${serverURL}chat?custom_id=${customId}&message=${encodedMessage}&user_id=${userId}`;

                const eventSource = new EventSource(url);

                eventSource.onmessage = function(event) {
                    // Append each new message received from the server
                    appendMessageToChatbox(event.data, "incoming", true);
                };

                eventSource.onerror = function(error) {
                    console.error("EventSource failed:", error);
                    eventSource.close();
                    handleErrorResponse(); // Existing error handling function
                };
            };

            

            const handleChat = () => {
                const userMessage = chatInput.value.trim();
                if (!userMessage) return;
                const userId = getCookie('user_id');
                console.log("user_id: " + userId);
                chatInput.value = "";
                chatInput.style.height = `${inputInitHeight}px`;
            
                appendMessageToChatbox(userMessage, "outgoing");
            
                setTimeout(() => appendMessageToChatbox("Thinking...", "incoming", true), 600);
                setTimeout(() => generateResponse(userMessage, userId), 1200); // Corrected this line
            };

            const adjustTextareaHeight = () => {
                chatInput.style.height = `${inputInitHeight}px`;
                chatInput.style.height = `${chatInput.scrollHeight}px`;
            };

            chatInput.addEventListener("input", adjustTextareaHeight);

            chatInput.addEventListener("keydown", (e) => {
                if (e.key === "Enter" && !e.shiftKey && window.innerWidth > 800) {
                    e.preventDefault();
                    handleChat();
                }
            });

            sendChatBtn.addEventListener("click", handleChat);
            closeBtn.addEventListener("click", () => document.body.classList.remove("show-chatbot"));
            chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));
        } else {
            setTimeout(initializeChatbot, 100); // Retry after 100 milliseconds
        }
    }

    initializeChatbot();
});
