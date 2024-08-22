<?php
/*
Plugin Name: AI Chatbot
Description: OpenAI API kullanan özelleştirilebilir bir chatbot eklentisi
Version: 1.1
Author: Your Name
*/

if (!defined('ABSPATH')) exit;

// Eklenti yüklendiğinde çalışacak fonksiyon
function ai_chatbot_activate() {
    // Varsayılan ayarları kaydet
    $default_settings = array(
        'bubble_image' => '',
        'avatar_image' => '',
        'chatbot_name' => 'AI Chatbot',
        'chatbot_prompt' => 'You are a helpful assistant.',
        'openai_api_key' => '',
        'user_bubble_color' => '#0084ff',
        'bot_bubble_color' => '#f0f0f0',
    );
    add_option('ai_chatbot_settings', $default_settings);
}
register_activation_hook(__FILE__, 'ai_chatbot_activate');

// Admin menüsüne eklenti ayarları sayfası ekle
function ai_chatbot_menu() {
    add_menu_page('AI Chatbot Settings', 'AI Chatbot', 'manage_options', 'ai-chatbot-settings', 'ai_chatbot_settings_page', 'dashicons-format-chat');
}
add_action('admin_menu', 'ai_chatbot_menu');

// Ayarlar sayfası içeriği
function ai_chatbot_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    if (isset($_POST['ai_chatbot_settings_submit'])) {
        $settings = get_option('ai_chatbot_settings');
        
        // Handle file uploads
        if (!empty($_FILES['bubble_image']['tmp_name'])) {
            $bubble_image = media_handle_upload('bubble_image', 0);
            if (!is_wp_error($bubble_image)) {
                $settings['bubble_image'] = wp_get_attachment_url($bubble_image);
            }
        }
        if (!empty($_FILES['avatar_image']['tmp_name'])) {
            $avatar_image = media_handle_upload('avatar_image', 0);
            if (!is_wp_error($avatar_image)) {
                $settings['avatar_image'] = wp_get_attachment_url($avatar_image);
            }
        }
        
        $settings['chatbot_name'] = sanitize_text_field($_POST['chatbot_name']);
        $settings['chatbot_prompt'] = sanitize_textarea_field($_POST['chatbot_prompt']);
        $settings['openai_api_key'] = sanitize_text_field($_POST['openai_api_key']);
        $settings['user_bubble_color'] = sanitize_hex_color($_POST['user_bubble_color']);
        $settings['bot_bubble_color'] = sanitize_hex_color($_POST['bot_bubble_color']);
        
        update_option('ai_chatbot_settings', $settings);
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    $settings = get_option('ai_chatbot_settings');
    ?>
    <div class="wrap">
        <h1>AI Chatbot Settings</h1>
        <form method="post" action="" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <th><label for="bubble_image">Bubble Image</label></th>
                    <td>
                        <?php if (!empty($settings['bubble_image'])): ?>
                            <img src="<?php echo esc_url($settings['bubble_image']); ?>" style="max-width: 100px; max-height: 100px;">
                        <?php endif; ?>
                        <input type="file" id="bubble_image" name="bubble_image">
                    </td>
                </tr>
                <tr>
                    <th><label for="avatar_image">Avatar Image</label></th>
                    <td>
                        <?php if (!empty($settings['avatar_image'])): ?>
                            <img src="<?php echo esc_url($settings['avatar_image']); ?>" style="max-width: 100px; max-height: 100px;">
                        <?php endif; ?>
                        <input type="file" id="avatar_image" name="avatar_image">
                    </td>
                </tr>
                <tr>
                    <th><label for="chatbot_name">Chatbot Name</label></th>
                    <td><input type="text" id="chatbot_name" name="chatbot_name" value="<?php echo esc_attr($settings['chatbot_name']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="chatbot_prompt">Chatbot Prompt</label></th>
                    <td><textarea id="chatbot_prompt" name="chatbot_prompt" rows="5" class="large-text"><?php echo esc_textarea($settings['chatbot_prompt']); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="openai_api_key">OpenAI API Key</label></th>
                    <td><input type="text" id="openai_api_key" name="openai_api_key" value="<?php echo esc_attr($settings['openai_api_key']); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="user_bubble_color">User Bubble Color</label></th>
                    <td><input type="color" id="user_bubble_color" name="user_bubble_color" value="<?php echo esc_attr($settings['user_bubble_color']); ?>"></td>
                </tr>
                <tr>
                    <th><label for="bot_bubble_color">Bot Bubble Color</label></th>
                    <td><input type="color" id="bot_bubble_color" name="bot_bubble_color" value="<?php echo esc_attr($settings['bot_bubble_color']); ?>"></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="ai_chatbot_settings_submit" class="button-primary" value="Save Settings">
            </p>
        </form>
    </div>
    <?php
}

// Chatbot'u frontend'e ekle
function ai_chatbot_enqueue_scripts() {
    wp_enqueue_style('ai-chatbot-style', plugins_url('chatbot.css', __FILE__));
    wp_enqueue_script('ai-chatbot-script', plugins_url('chatbot.js', __FILE__), array('jquery'), null, true);
    
    $settings = get_option('ai_chatbot_settings');
    wp_localize_script('ai-chatbot-script', 'aiChatbotSettings', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'bubble_image' => $settings['bubble_image'],
        'avatar_image' => $settings['avatar_image'],
        'chatbot_name' => $settings['chatbot_name'],
        'user_bubble_color' => $settings['user_bubble_color'],
        'bot_bubble_color' => $settings['bot_bubble_color'],
    ));
}
add_action('wp_enqueue_scripts', 'ai_chatbot_enqueue_scripts');

// Chatbot HTML'ini footer'a ekle
function ai_chatbot_add_to_footer() {
    include(plugin_dir_path(__FILE__) . 'chatbot_html.php');
}
add_action('wp_footer', 'ai_chatbot_add_to_footer');

// AJAX isteğini işle
function ai_chatbot_process_message() {
    $message = sanitize_text_field($_POST['message']);
    $settings = get_option('ai_chatbot_settings');
    
    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $settings['openai_api_key'],
            'Content-Type' => 'application/json',
        ),
        'body' => json_encode(array(
            'model' => 'gpt-3.5-turbo',
            'messages' => array(
                array('role' => 'system', 'content' => $settings['chatbot_prompt']),
                array('role' => 'user', 'content' => $message),
            ),
        )),
    ));

    if (is_wp_error($response)) {
        wp_send_json_error('API request failed.');
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    $reply = $body['choices'][0]['message']['content'];

    wp_send_json_success($reply);
}
add_action('wp_ajax_ai_chatbot_message', 'ai_chatbot_process_message');
add_action('wp_ajax_nopriv_ai_chatbot_message', 'ai_chatbot_process_message');