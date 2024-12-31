<?php
/*
Template Name: User Registration
*/

get_header();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['quote_user_nonce']) || !wp_verify_nonce($_POST['quote_user_nonce'], 'quote_user_registration_nonce')) {
        echo '<p class="text-red-500">Security check failed.</p>';
    } else {
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);
        $role = sanitize_text_field($_POST['role']);

        // Validate inputs
        if (empty($username) || empty($email) || empty($password)) {
            echo '<p class="text-red-500">All fields are required.</p>';
        } elseif (username_exists($username) || email_exists($email)) {
            echo '<p class="text-red-500">Username or email already exists.</p>';
        } else {
            // Create new user
            $user_id = wp_create_user($username, $password, $email);

            if (!is_wp_error($user_id)) {
                $user = new WP_User($user_id);
                $user->set_role($role);

                echo '<p class="text-green-500">Registration successful! You can now log in.</p>';
            } else {
                echo '<p class="text-red-500">Error: ' . $user_id->get_error_message() . '</p>';
            }
        }
    }
}
?>

<div class="registration-form max-w-lg mx-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-center text-2xl font-bold mb-6">Register</h2>
    <form method="POST" action="" class="space-y-4">
        <div>
            <label for="username" class="block text-lg font-semibold mb-2">Username:</label>
            <input type="text" name="username" id="username" required
                class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="email" class="block text-lg font-semibold mb-2">Email:</label>
            <input type="email" name="email" id="email" required
                class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="password" class="block text-lg font-semibold mb-2">Password:</label>
            <input type="password" name="password" id="password" required
                class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="role" class="block text-lg font-semibold mb-2">Role:</label>
            <select name="role" id="role"
                class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="subscriber">Subscriber</option>
                <option value="contributor">Contributor</option>
                <option value="author">Author</option>
            </select>
        </div>
        <input type="hidden" name="quote_user_nonce"
            value="<?php echo wp_create_nonce('quote_user_registration_nonce'); ?>">
        <div class="text-center">
            <input type="submit" value="Register"
                class="bg-indigo-600 text-white py-3 px-6 rounded-md cursor-pointer hover:bg-indigo-700 focus:outline-none">
        </div>
    </form>

    <div class="text-center mt-4">
        <p>Already have an account? <a href="<?php echo esc_url(home_url('/login/')); ?>"
                class="text-indigo-600 hover:underline">Log in here</a>.</p>
    </div>

</div>

<?php
?>