<?php
session_start();
// faq.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - CV Maker</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css"> <script>
        // Initial theme setup to prevent FOUC
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300 font-sans">

    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-4 py-8 mt-16 md:mt-20 min-h-screen">
        <header class="text-center mb-16 animate-fade-in-up">
            <h1 class="text-4xl md:text-5xl font-bold text-indigo-600 dark:text-indigo-400">Frequently Asked Questions</h1>
            <p class="mt-4 text-lg md:text-xl text-gray-700 dark:text-gray-300">
                Find answers to common questions about creating CVs, managing your account, and using our platform.
            </p>
        </header>

        <section class="mb-12 animate-fade-in-up bg-white dark:bg-gray-800 p-6 md:p-10 rounded-xl shadow-2xl" style="animation-delay: 0.2s;">
            <h2 class="text-2xl md:text-3xl font-semibold mb-8 text-indigo-700 dark:text-indigo-300 flex items-center">
                <i class="fas fa-question-circle mr-3 text-2xl"></i> General Questions
            </h2>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">What is CV Maker?</h3>
                    <p class="text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                        CV Maker is an online platform designed to help you create professional, modern, and effective Curriculum Vitae (CVs) or resumes quickly and easily. We provide a range of templates and tools to make the process seamless.
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">Is CV Maker free to use?</h3>
                    <p class="text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                        CV Maker offers core features for free, allowing you to build and download a basic CV. Some advanced templates, features, or download options might be part of a premium plan in the future. We aim to provide excellent value at all levels.
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">Do I need to create an account to use CV Maker?</h3>
                    <p class="text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                        Yes, creating an account is necessary to save your progress, access your CVs from different devices, manage multiple versions, and utilize all the features of our platform.
                    </p>
                </div>
            </div>
        </section>

        <section class="mb-12 animate-fade-in-up bg-white dark:bg-gray-800 p-6 md:p-10 rounded-xl shadow-2xl" style="animation-delay: 0.4s;">
            <h2 class="text-2xl md:text-3xl font-semibold mb-8 text-indigo-700 dark:text-indigo-300 flex items-center">
                <i class="fas fa-file-alt mr-3 text-2xl"></i> CV Creation & Templates
            </h2>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">What kind of templates do you offer?</h3>
                    <p class="text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                        We offer a diverse range of professionally designed templates, including modern, classic, creative, and minimalist styles. Our templates are ATS-friendly (Applicant Tracking System) to help your CV get noticed.
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">Can I customize the templates?</h3>
                    <p class="text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                        Yes, you can customize various aspects of the templates, such as fonts, colors, and section order, to match your personal brand and the requirements of the job you're applying for.
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">In what formats can I download my CV?</h3>
                    <p class="text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                        You can download your completed CV primarily in PDF format, which is the industry standard for job applications. We may offer other formats in the future.
                    </p>
                </div>
                 <div>
                    <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">Can I edit my CV after saving it?</h3>
                    <p class="text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                        Absolutely! All your CVs are saved to your account. You can log in anytime to make edits, updates, or download new copies.
                    </p>
                </div>
            </div>
        </section>
        
        <section class="mb-12 animate-fade-in-up bg-white dark:bg-gray-800 p-6 md:p-10 rounded-xl shadow-2xl" style="animation-delay: 0.6s;">
            <h2 class="text-2xl md:text-3xl font-semibold mb-8 text-indigo-700 dark:text-indigo-300 flex items-center">
                <i class="fas fa-user-cog mr-3 text-2xl"></i> Account & Support
            </h2>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">How do I reset my password?</h3>
                    <p class="text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                        If you've forgotten your password, you can click the "Forgot Password?" link on the <a href="login.php" class="text-indigo-600 dark:text-indigo-400 hover:underline">login page</a>. Follow the instructions sent to your registered email address to reset it.
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">Is my personal data safe?</h3>
                    <p class="text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                        We take your privacy and data security very seriously. Please review our <a href="privacy_policy.php" class="text-indigo-600 dark:text-indigo-400 hover:underline">Privacy Policy</a> for detailed information on how we collect, use, and protect your data.
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-medium text-gray-800 dark:text-gray-200">Who can I contact if I need help?</h3>
                    <p class="text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                        If you have any questions not covered in this FAQ or encounter any issues, please don't hesitate to reach out to us via our <a href="contact.php" class="text-indigo-600 dark:text-indigo-400 hover:underline">Contact Us</a> page. We're happy to assist you!
                    </p>
                </div>
            </div>
        </section>

        <section class="text-center mt-16 mb-8 animate-fade-in-up" style="animation-delay: 0.8s;">
            <p class="text-lg text-gray-700 dark:text-gray-300">Can't find the answer you're looking for?</p>
            <a href="contact.php" 
               class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                Contact Support <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </section>

    </main>

    <?php include 'footer.php'; ?>

    <script src="script.js" defer></script>
</body>
</html>
