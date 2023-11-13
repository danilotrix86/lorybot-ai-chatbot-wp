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

const handleChatResponse = (chatElement, data) => {
    const messageElement = chatElement.querySelector("p");
    let message = data.response ? data.response.trim() : "Something went wrong. Please try again.";

    // Replace '**text**' with '<strong>text</strong>'
    message = message.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");

    // Set innerHTML to render HTML content, including the <strong> tags
    messageElement.innerHTML = message;
    chatbox.scrollTo(0, chatbox.scrollHeight);
};


const handleErrorResponse = (chatElement) => {
    const messageElement = chatElement.querySelector("p");
    messageElement.classList.add("error");
    // Use innerHTML for consistency, but the content here is controlled and does not need sanitization
    messageElement.innerHTML = "Oooops! Something went wrong. Please try again.";
};

const generateResponse = (chatElement, userMessage) => {
    const params = createAndSendParams(userMessage);

    fetch(chatbot_vars.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params
    })
    .then(response => response.json())
    .then(data => handleChatResponse(chatElement, data))
    .catch(() => handleErrorResponse(chatElement));
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
