<?php
/*
Template Name: User Login
*/

get_header();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_text_field($_POST['username']);
    $password = sanitize_text_field($_POST['password']);

    $credentials = array(
        'user_login' => $username,
        'user_password' => $password,
        'remember' => isset($_POST['remember']),
    );

    // Try to log the user in
    $user = wp_signon($credentials, is_ssl());

    // Check if the login was successful or failed
    if (is_wp_error($user)) {
        $login_error_message = $user->get_error_message();  // Store error message
    } else {
        // Successful login: set a flag to show a success message
        $login_success = true;

        // Directly use the homepage URL for redirection
        wp_redirect(home_url('/home'));  // Redirect to 'http://localhost/mywebsite/home/'
        exit;
    }
}
?>

<div class="login-form max-w-lg mx-auto p-6 bg-white rounded-lg shadow-md">
    <!-- Show success message after login -->
    <?php
    if (isset($login_success) && $login_success) {
        echo '<p class="text-green-500 text-center mb-4">You have successfully logged in!</p>';
    }
    ?>

    <!-- Show error message if login fails -->
    <?php
    if (isset($login_error_message)) {
        echo '<p class="text-red-500 text-center mb-4">Error: ' . $login_error_message . '</p>';
    }
    ?>

    <h2 class="text-center text-2xl font-bold mb-6">Login</h2>
    <form method="POST" action="" class="space-y-4">
        <div>
            <label for="username" class="block text-lg font-semibold mb-2">Username or Email:</label>
            <input type="text" name="username" id="username" required
                class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="password" class="block text-lg font-semibold mb-2">Password:</label>
            <input type="password" name="password" id="password" required
                class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="remember" class="form-checkbox text-indigo-600">
                <span class="ml-2 text-gray-700">Remember Me</span>
            </label>
        </div>
        <div class="text-center">
            <input type="submit" value="Log In"
                class="bg-indigo-600 text-white py-3 px-6 rounded-md cursor-pointer hover:bg-indigo-700 focus:outline-none">
        </div>
    </form>
    <div class="text-center mt-4">
        <p>Don't have an account? <a href="<?php echo esc_url(home_url('/register/')); ?>"
                class="text-indigo-600 hover:underline">Register here</a>.</p>
    </div>
</div>

<?php
?>