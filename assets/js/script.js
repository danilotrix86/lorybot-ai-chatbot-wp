document.addEventListener("DOMContentLoaded", function() {

    // Function to set the user_id cookie for 1 day
    function setUserIdCookie() {
        if (!document.cookie.split(';').some((item) => item.trim().startsWith('user_id='))) {
            var d = new Date();
            d.setTime(d.getTime() + (86400 * 1000)); // Set the cookie to expire in 1 day
            var expires = "expires=" + d.toUTCString();
            var userId = generateUserId();
            document.cookie = "user_id=" + userId + ";" + expires + ";path=/";
        }
    }

    function getCookie(name) {
        let value = `; ${document.cookie}`;
        let parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }
    // Function to generate a unique user ID (UUID)
    function generateUserId() {
        // Simple version of UUID generation logic
        // Consider using a more robust method or a library if needed
        return 'uid_' + Math.random().toString(36).slice(2, 11);
    }

    // Set the user_id cookie if not already set
    setUserIdCookie();

    function sendWarmupRequest() {
        const userId = getCookie('user_id');
        if (userId) {
            const customId = chatbot_vars.custom_id; // Ensure chatbot_vars.custom_id is defined
            const serverURL = chatbot_vars.server_url; // Ensure serverURL is defined
            const url = serverURL + "warmup/";
            const data = { custom_id: customId };
    
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'LORYBOT-API-KEY': customId // Adding custom_id to the request headers
                },
                body: JSON.stringify(data),
            })
            .then(response => response.json())
            .then(data => {
                console.log('Warmup request successful:', data);
                return data; // Make sure to return data or some response
            });
        }
    }


    function initializeChatbot() {
        const userId = getCookie('user_id');
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

            
            const handleErrorResponse = (chatElement) => {
                if (chatElement) {
                    const messageElement = chatElement.querySelector(".error-message"); // Ensure this selector matches an element in your chatbox
                    if (messageElement) {
                        messageElement.classList.add("error");
                        messageElement.innerHTML = "Oooops! Something went wrong. Please try again.";
                    }
                }
            };


           


            const generateResponse = (userMessage, userId) => {
                const customId = chatbot_vars.custom_id;
                const encodedMessage = encodeURIComponent(userMessage);
                const serverURL = chatbot_vars.server_url;
                const url = `${serverURL}chat?custom_id=${customId}&message=${encodedMessage}&user_id=${userId}`;
            
                const eventSource = new EventSource(url);
                const converter = new showdown.Converter();
                let messageBuffer = '';


                eventSource.onmessage = function(event) {
                    console.log("Received chunk", event.data);
                    const htmlContent = processChunk(event.data);
                    appendMessageToChatbox(htmlContent, "incoming", true);
                    htmlBuffer = ''; // Clear the buffer after appending to the chatbox
                };
                
            
                eventSource.onerror = function(error) {
                    eventSource.close();
                    handleErrorResponse(chatbox);
                };
            };
            
            
            let htmlBuffer = '';
            let markdownBuffer = '';
            let isBold = false;

            function processChunk(chunk) {
                for (let i = 0; i < chunk.length; i++) {
                    markdownBuffer += chunk[i];

                    // Check for bold markdown syntax '**'
                    if (markdownBuffer.endsWith('**')) {
                        if (isBold) {
                            // Closing bold tag
                            htmlBuffer += '<strong>' + markdownBuffer.slice(0, -2) + '</strong>';
                            markdownBuffer = '';
                            isBold = false;
                        } else {
                            // Opening bold tag, flush previous text
                            htmlBuffer += markdownBuffer.slice(0, -2);
                            markdownBuffer = '';
                            isBold = true;
                        }
                    }
                }

                // If bold is ongoing, don't close it yet
                if (isBold) {
                    return htmlBuffer; // Return the HTML content so far
                }

                // Flush remaining markdownBuffer if not in a bold state
                htmlBuffer += markdownBuffer;
                markdownBuffer = '';
                return htmlBuffer;
            }

           
            const handleChat = () => {
                const userMessage = chatInput.value.trim();
                if (!userMessage) return;
                const userId = getCookie('user_id');
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
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    handleChat();
                } else if (e.key === "Enter" && e.shiftKey) {
                    // Allow Shift+Enter to insert a newline in the textarea
                }
            });

            sendChatBtn.addEventListener("click", handleChat);
            closeBtn.addEventListener("click", () => document.body.classList.remove("show-chatbot"));
            
            chatbotToggler.addEventListener("click", () => {
                console.log("Toggler clicked"); // For debugging
                document.body.classList.toggle("show-chatbot");
                sendWarmupRequest();
            });        } else {
            setTimeout(initializeChatbot, 100); // Retry after 100 milliseconds
        }
    }

    initializeChatbot();
});
