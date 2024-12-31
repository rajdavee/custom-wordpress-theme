<?php
// Add custom menu for user management
function quote_add_admin_menu() {
    add_menu_page(
        'User Management',        // Page title
        'User Management',        // Menu title
        'manage_options',         // Capability
        'user-management',        // Menu slug
        'quote_user_management',  // Callback function
        'dashicons-admin-users',  // Icon
        6                         // Position
    );
}
add_action('admin_menu', 'quote_add_admin_menu');


function enqueue_tailwind_styles() {
    wp_enqueue_style('tailwind-css', get_template_directory_uri() . '/assets/css/tailwind.min.css', array(), '3.4.0');
}
add_action('wp_enqueue_scripts', 'enqueue_tailwind_styles');
add_action('admin_enqueue_scripts', 'enqueue_tailwind_styles');


// Display content on the custom page
function quote_user_management() {
    // Show success message if user was added
    if (isset($_GET['user-added']) && $_GET['user-added'] == 1) {
        echo '<div class="notice notice-success"><p>User successfully created!</p></div>';
    }

    // Display the form
    ?>
    <div class="wrap">
        <h1>Add New User</h1>
        <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <table class="form-table">
                <tr>
                    <th><label for="username">Username</label></th>
                    <td><input type="text" name="username" id="username" required class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="email">Email</label></th>
                    <td><input type="email" name="email" id="email" required class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="password">Password</label></th>
                    <td><input type="password" name="password" id="password" required class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="role">Role</label></th>
                    <td>
                        <select name="role" id="role">
                            <option value="subscriber">Subscriber</option>
                            <option value="contributor">Contributor</option>
                            <option value="author">Author</option>
                            <option value="editor">Editor</option>
                            <option value="administrator">Administrator</option>
                        </select>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="quote_user_nonce" value="<?php echo wp_create_nonce('quote_user_add_nonce'); ?>">
            <input type="hidden" name="action" value="quote_add_user">
            <input type="hidden" name="submit_user" value="1">
            <?php submit_button('Add User'); ?>
        </form>
    </div>
    <?php
}

// Verify nonce and process form securely
function quote_handle_form_submission() {
    if (isset($_POST['submit_user'])) {
        // Verify nonce for security
        if (!isset($_POST['quote_user_nonce']) || !wp_verify_nonce($_POST['quote_user_nonce'], 'quote_user_add_nonce')) {
            wp_die('Security check failed.');
        }

        // Proceed with user creation
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);
        $role = sanitize_text_field($_POST['role']);

        if (username_exists($username) || email_exists($email)) {
            wp_die('Username or email already exists.');
        }

        $user_id = wp_create_user($username, $password, $email);

        if (!is_wp_error($user_id)) {
            $user = new WP_User($user_id);
            $user->set_role($role);
            wp_redirect(admin_url('admin.php?page=user-management&user-added=1'));
            exit;
        } else {
            wp_die('Error: ' . $user_id->get_error_message());
        }
    }
}
add_action('admin_post_quote_add_user', 'quote_handle_form_submission');
