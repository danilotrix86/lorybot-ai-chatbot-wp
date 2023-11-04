const rootStyle = document.documentElement.style;

rootStyle.setProperty('--main-color', chatbot_vars.main_color);
rootStyle.setProperty('--background-color', chatbot_vars.background_color);


const chatbotToggler = document.querySelector(".chatbot-toggler");
const closeBtn = document.querySelector(".close-btn");
const chatbox = document.querySelector(".chatbox");
const chatInput = document.querySelector(".chat-input textarea");
const sendChatBtn = document.querySelector(".chat-input span");

let userMessage = null; // Variable to store user's message
const inputInitHeight = chatInput.scrollHeight;

const createChatLi = (message, className) => {
    // Create a chat <li> element with passed message and className
    const chatLi = document.createElement("li");
    chatLi.classList.add("chat", `${className}`);
    let chatContent = className === "outgoing" ? `<p></p>` : `<span class="material-symbols-outlined">smart_toy</span><p></p>`;
    chatLi.innerHTML = chatContent;
    chatLi.querySelector("p").textContent = message;
    return chatLi; // return chat <li> element
}

const generateResponse = (chatElement) => {
    const messageElement = chatElement.querySelector("p");

    var params = new URLSearchParams();
    params.append('action', 'process_chatbot_message');
    params.append('message', userMessage);
    params.append('client_id', chatbot_vars.client_id);



    fetch(chatbot_vars.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params
    })
    .then(response => response.json())
    .then(data => {

        messageElement.textContent = data;

        const typingElement = document.getElementById('chatbot-typing-indicator');
        if (typingElement) {
            typingElement.parentNode.removeChild(typingElement);
        }
        if (data.response) {
            messageElement.innerHTML = data.response.trim();
        } else {
            messageElement.textContent = "Something went wrong. Please try again.";
        }
        chatbox.scrollTo(0, chatbox.scrollHeight);
    })
    .catch(() => {
        messageElement.classList.add("error");
        messageElement.textContent = "Oooops! Something went wrong. Please try again.";
    });
}


const handleChat = () => {

    userMessage = chatInput.value.trim(); // Get user entered message and remove extra whitespace
    if(!userMessage) return;

    var params = new URLSearchParams();
    params.append('action', 'process_chatbot_message');
    params.append('message', userMessage);
    params.append('client_id', chatbot_vars.client_id);
    params.append('openai_key', chatbot_vars.openai_key);
    params.append('prompt', chatbot_vars.prompt);

    console.log(params.toString());

   

    // Clear the input textarea and set its height to default
    chatInput.value = "";
    chatInput.style.height = `${inputInitHeight}px`;

    // Append the user's message to the chatbox
    chatbox.appendChild(createChatLi(userMessage, "outgoing"));
    chatbox.scrollTo(0, chatbox.scrollHeight);
    
    setTimeout(() => {
        // Display "Thinking..." message while waiting for the response
        const incomingChatLi = createChatLi("Thinking...", "incoming");
        chatbox.appendChild(incomingChatLi);
        chatbox.scrollTo(0, chatbox.scrollHeight);
        generateResponse(incomingChatLi);
    }, 600);
}

chatInput.addEventListener("input", () => {
    // Adjust the height of the input textarea based on its content
    chatInput.style.height = `${inputInitHeight}px`;
    chatInput.style.height = `${chatInput.scrollHeight}px`;
});

chatInput.addEventListener("keydown", (e) => {
    // If Enter key is pressed without Shift key and the window 
    // width is greater than 800px, handle the chat
    if(e.key === "Enter" && !e.shiftKey && window.innerWidth > 800) {
        e.preventDefault();
        handleChat();
    }
});

sendChatBtn.addEventListener("click", handleChat);
closeBtn.addEventListener("click", () => document.body.classList.remove("show-chatbot"));
chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));