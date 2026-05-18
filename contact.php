<?php
include "includes/connection.php";

start_secure_session();

/* -------------------------
   Fetch restaurant settings (cached)
-------------------------- */
$settings = get_settings();

/* -------------------------
   Telegram helper function
-------------------------- */
function sendTelegramMessage($chat_id, $bot_token, $text)
{
    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";

    $data = [
        'chat_id'    => $chat_id,
        'text'       => $text,
        'parse_mode' => 'HTML'
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data),
            'timeout' => 10
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === false) {
        error_log("Telegram API Error: request failed");
        return false;
    }

    $response = json_decode($result, true);
    if (!isset($response['ok']) || !$response['ok']) {
        error_log("Telegram API Error: " . ($response['description'] ?? 'Unknown error'));
        return false;
    }

    return true;
}

/* -------------------------
   Handle form submission
-------------------------- */
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = '
        <div class="alert-custom alert-custom-error flex items-center gap-2" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            Invalid request token. Please refresh and try again.
            <button type="button" class="ml-auto bg-transparent border-none cursor-pointer text-lg leading-none opacity-50 hover:opacity-100" onclick="this.parentElement.remove()">&times;</button>
        </div>';
    } else {

        $name    = trim($_POST['name'] ?? '');
        $phonenumber = trim($_POST['phonenumber'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body    = trim($_POST['message'] ?? '');

        if ($name && $phonenumber && $subject && $body) {

            $sql = "INSERT INTO contact_submissions (name, phonenumber, subject, message)
                    VALUES (?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {

                $stmt->bind_param("ssss", $name, $phonenumber, $subject, $body);

                if ($stmt->execute()) {



                    $message = '
                    <div class="alert-custom alert-custom-success flex items-center gap-2" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        Thank you for your message! We will get back to you soon.
                        <button type="button" class="ml-auto bg-transparent border-none cursor-pointer text-lg leading-none opacity-50 hover:opacity-100" onclick="this.parentElement.remove()">&times;</button>
                    </div>';

                    $telegram_chat_id  = $settings['chat_id'] ?? '';
                    $telegram_bot_token = $settings['bot_token'] ?? '';

                    if ($telegram_chat_id && $telegram_bot_token) {

                        $telegram_text =
                              htmlspecialchars($name) . ":\n"
                            . "<b>Phone Number:</b> " . htmlspecialchars($phonenumber) . "\n"
                            . "<b>Subject:</b> " . htmlspecialchars($subject) . "\n"
                            . "<b>Message:</b>\n" . htmlspecialchars($body);

                        sendTelegramMessage(
                            $telegram_chat_id,
                            $telegram_bot_token,
                            $telegram_text
                        );
                    }

                } else {
                    $message = '
                    <div class="alert-custom alert-custom-error flex items-center gap-2" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Something went wrong. Please try again later.
                        <button type="button" class="ml-auto bg-transparent border-none cursor-pointer text-lg leading-none opacity-50 hover:opacity-100" onclick="this.parentElement.remove()">&times;</button>
                    </div>';
                }

                $stmt->close();

            } else {
                $message = '
                <div class="alert-custom alert-custom-error flex items-center gap-2" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Database error. Please try again later.
                    <button type="button" class="ml-auto bg-transparent border-none cursor-pointer text-lg leading-none opacity-50 hover:opacity-100" onclick="this.parentElement.remove()">&times;</button>
                </div>';
            }

        } else {
            $message = '
            <div class="alert-custom alert-custom-error flex items-center gap-2" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                Please fill in all fields.
                <button type="button" class="ml-auto bg-transparent border-none cursor-pointer text-lg leading-none opacity-50 hover:opacity-100" onclick="this.parentElement.remove()">&times;</button>
            </div>';
        }
    }
}

$csrfToken = ensure_csrf_token();
?>

<?php include 'includes/header.php'; ?>

<section class="contact-hero-section" style="background-image: url('<?php echo htmlspecialchars($settings['contact_bg'] ?? ''); ?>');">
    <div class="hero-content">
        <h1 class="hero-title font-poppins font-extrabold mb-4 text-white">Contact Us</h1>
        <p class="hero-subtitle text-white/80 text-lg"">We'd love to hear from you</p>
    </div>
</section>

<section class="py-20" style="background: var(--color-bg);">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-center">
            <div class="w-full max-w-5xl">

                <?php echo $message; ?>

                <div class="contact-card">
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-5/12">
                            <div class="contact-info-section">
                                <h3>Get in Touch</h3>

                                <div class="info-item">
                                    <div class="info-icon"><i class="fas fa-phone"></i></div>
                                    <div class="info-text">
                                        <h5>Phone</h5>
                                        <p><?php echo htmlspecialchars($settings['restaurant_phone'] ?? ''); ?></p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon"><i class="fas fa-envelope"></i></div>
                                    <div class="info-text">
                                        <h5>Email</h5>
                                        <p><?php echo htmlspecialchars($settings['restaurant_email'] ?? ''); ?></p>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                    <div class="info-text">
                                        <h5>Location</h5>
                                        <p><?php echo htmlspecialchars($settings['restaurant_address'] ?? 'Lebanon'); ?></p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="md:w-7/12">
                            <div class="contact-form-section">
                                <form method="POST">
                                    <?php echo csrf_input(); ?>
                                    <div class="mb-4">
                                        <label class="form-label-custom">Name</label>
                                        <input type="text" name="name" class="block w-full px-4 py-3 rounded-lg border text-base outline-none transition-all duration-300" style="border-color: #ced4da; background: #ffffff; color: #212529;" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label-custom">Phone Number</label>
                                        <input type="text" name="phonenumber" class="block w-full px-4 py-3 rounded-lg border text-base outline-none transition-all duration-300" style="border-color: #ced4da; background: #ffffff; color: #212529;" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label-custom">Subject</label>
                                        <input type="text" name="subject" class="block w-full px-4 py-3 rounded-lg border text-base outline-none transition-all duration-300" style="border-color: #ced4da; background: #ffffff; color: #212529;" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label-custom">Message</label>
                                        <textarea name="message" class="block w-full px-4 py-3 rounded-lg border text-base outline-none transition-all duration-300" style="border-color: #ced4da; background: #ffffff; color: #212529;" rows="4" required></textarea>
                                    </div>

                                    <button type="submit" class="btn-submit w-full py-3 px-8 rounded-lg font-bold border-none transition-all duration-300 cursor-pointer" style="background: var(--color-accent); color: white;">Send Message</button>
                                </form>

                                <script>
                                // Dark mode handling for contact form inputs
                                (function() {
                                    var inputs = document.querySelectorAll('.contact-form-section input, .contact-form-section textarea');
                                    var html = document.documentElement;
                                    function updateInputs() {
                                        var isDark = html.classList.contains('dark');
                                        inputs.forEach(function(el) {
                                            if (isDark) {
                                                el.style.borderColor = '#4b5563';
                                                el.style.background = '#1f2937';
                                                el.style.color = '#f3f4f6';
                                            } else {
                                                el.style.borderColor = '#ced4da';
                                                el.style.background = '#ffffff';
                                                el.style.color = '#212529';
                                            }
                                        });
                                    }
                                    updateInputs();
                                    var observer = new MutationObserver(updateInputs);
                                    observer.observe(html, { attributes: true, attributeFilter: ['class'] });
                                    document.querySelectorAll('input:focus, textarea:focus').forEach(function(el) {
                                        el.addEventListener('focus', function() {
                                            this.style.borderColor = '#42522B';
                                            this.style.boxShadow = '0 0 0 3px rgba(66, 82, 43, 0.25)';
                                        });
                                        el.addEventListener('blur', function() {
                                            this.style.borderColor = html.classList.contains('dark') ? '#4b5563' : '#ced4da';
                                            this.style.boxShadow = 'none';
                                        });
                                    });
                                })();
                                </script>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
