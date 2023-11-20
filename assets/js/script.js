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
    const chatLi = createChatLi(message, className, isBot);
    chatbox.appendChild(chatLi);
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

    // Regular expression for bold markdown text
    const boldRegex = /\*\*(.*?)\*\*/g;

    // Replace markdown-style bold text with HTML strong tags
    pElement.innerHTML = pElement.innerHTML.replace(boldRegex, "<strong>$1</strong>");

    console.log("Final message after processing:", pElement.innerHTML);
}



const handleErrorResponse = (chatElement) => {
    const messageElement = chatElement.querySelector("p");
    messageElement.classList.add("error");
    // Use innerHTML for consistency, but the content here is controlled and does not need sanitization
    messageElement.innerHTML = "Oooops! Something went wrong. Please try again.";
};




const generateResponse = async (chatElement, userMessage) => {
    try {
        const serverURL = chatbot_vars.server_url;
        console.log("Sending request to: " + serverURL);
        const response = await fetch(serverURL + 'chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: chatbot_vars.user_id,
                message: userMessage,
                custom_id: chatbot_vars.custom_id,
                project_domain: chatbot_vars.client_id,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const decoder = new TextDecoder();
        const reader = response.body.getReader();

        console.log("Starting to read the stream...");
        let chunks = "";

        while (true) {
            const { done, value } = await reader.read();
            if (done) {
                console.log("Stream reading completed.");

                const lastChatLi = chatbox.querySelector("li.chat:last-child");
                if (lastChatLi) {
                    const lastP = lastChatLi.querySelector("p:last-child");
                    if (lastP) {
                        lastP.innerHTML = chunks; // Update the existing <p> element
                        processFinalMessage(lastP); // Process for formatting
                    }
                }
                chatbox.scrollTo(0, chatbox.scrollHeight);
                break;
            }
            chunks += decoder.decode(value, { stream: true });

            const lastChatLi = chatbox.querySelector("li.chat:last-child");
            if (lastChatLi) {
                let lastP = lastChatLi.querySelector("p:last-child");
                if (!lastP) {
                    lastP = document.createElement('p'); // Create new <p> if not exists
                    lastChatLi.appendChild(lastP);
                }
                lastP.textContent = chunks; // Update text content with new chunks
            }
            chatbox.scrollTo(0, chatbox.scrollHeight);
        }
    } catch (error) {
        console.error('Fetch error:', error);
        handleErrorResponse(chatElement);
    }
};





const handleChat = () => {
    const userMessage = chatInput.value.trim();
    if (!userMessage) return;

    chatInput.value = "";
    chatInput.style.height = `${inputInitHeight}px`;

    appendMessageToChatbox(userMessage, "outgoing");

    setTimeout(() => appendMessageToChatbox("Thinking...", "incoming", true), 600);
    setTimeout(() => generateResponse(chatbox.lastChild, userMessage), 1200);
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
