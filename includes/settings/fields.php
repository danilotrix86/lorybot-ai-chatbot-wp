<?php

// OPENAI ENABLED/ON/OFF Field
    add_settings_field(
        'my_chatbot_enabled_field',
        'Activate Chatbot',
        'my_chatbot_enable_callback',
        'lorybot-settings',
        'my_chatbot_main_section'
    );


    // LoryBot API KEY Field
    add_settings_field(
        'my_chatbot_lorybot_api_field',
        'LoryBot API Key',
        'my_chatbot_lorybot_api_callback',
        'lorybot-settings',
        'my_chatbot_main_section'
    );

    // PROMPT field
    add_settings_field(
        'my_chatbot_prompt_field',
        'Prompt',
        'my_chatbot_prompt_callback',
        'lorybot-settings',
        'my_chatbot_main_section'
    );

    // Embedding Field
    add_settings_field(
        'my_chatbot_embedding_field',
        'Information Source',
        'my_chatbot_embedding_callback',
        'lorybot-settings',
        'my_chatbot_main_section'
    );


    // CHAT Display Field
    add_settings_field(
        'my_chatbot_chat_display_field',
        'Chat Display',
        'my_chatbot_chat_display_callback',
        'lorybot-settings',
        'my_chatbot_main_section'
    );

    // Main Color Field
    add_settings_field(
        'my_chatbot_main_color_field',
        'Main Color',
        'my_chatbot_main_color_callback',
        'lorybot-settings',
        'my_chatbot_main_section'
    );

    // Background Color Field
    add_settings_field(
        'my_chatbot_background_color_field',
        'Background Color',
        'my_chatbot_background_color_callback',
        'lorybot-settings',
        'my_chatbot_main_section'
    );