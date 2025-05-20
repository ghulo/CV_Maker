// script.js - Enhanced UI/UX for CV Maker

document.addEventListener('DOMContentLoaded', () => {
    // --- Global Variables ---
    let cvItems = []; // Cache for CV list items on view_cvs.php
    const header = document.querySelector('header'); // Changed from '.header' to 'header' for consistency
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileNavMenu = document.getElementById('mobile-nav-menu-id'); // Corrected mobile nav menu ID
    const backgroundCvsContainer = document.querySelector('.background-cvs-container'); // Get the background container

    // --- Theme Management Variables ---
    const themeToggleButton = document.getElementById('theme-toggle-button'); // This is the main theme toggle button
    const body = document.body;
    const themeTransitionOverlay = document.createElement('div');
    themeTransitionOverlay.id = 'theme-transition-overlay';
    document.body.appendChild(themeTransitionOverlay);

    // Profile page specific theme radio buttons (may not exist on all pages)
    const lightThemeRadio = document.getElementById('theme-light');
    const darkThemeRadio = document.getElementById('theme-dark');

    /**
     * Initializes the theme based on user preference or system settings.
     * Prioritizes localStorage, then prefers-color-scheme.
     */
    function initTheme() {
        const savedTheme = localStorage.getItem('theme'); // 'light-theme' or 'dark-theme'

        if (savedTheme) {
            // Apply saved theme
            body.classList.add(savedTheme);
            updateThemeToggleButton(savedTheme);
            // Update profile page radio buttons if they exist
            if (savedTheme === 'dark-theme' && darkThemeRadio) darkThemeRadio.checked = true;
            else if (savedTheme === 'light-theme' && lightThemeRadio) lightThemeRadio.checked = true;
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            // Default to system dark theme if no preference saved
            body.classList.add('dark-theme');
            updateThemeToggleButton('dark-theme');
            localStorage.setItem('theme', 'dark-theme'); // Save system preference
            if (darkThemeRadio) darkThemeRadio.checked = true;
        } else {
            // Default to light theme
            body.classList.add('light-theme'); // Ensure light-theme class is present
            updateThemeToggleButton('light-theme');
            localStorage.setItem('theme', 'light-theme'); // Save default preference
            if (lightThemeRadio) lightThemeRadio.checked = true;
        }
    }

    /**
     * Toggles the theme between light and dark.
     * Applies a smooth transition effect.
     */
    function toggleTheme() {
        const isDark = body.classList.contains('dark-theme');
        const newTheme = isDark ? 'light-theme' : 'dark-theme';

        // Apply transition effect
        applyThemeTransition(newTheme === 'dark-theme' ? 'dark' : 'light', () => {
            // Callback after transition starts (or immediately if no transition)
            body.classList.remove('light-theme', 'dark-theme');
            body.classList.add(newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeToggleButton(newTheme);

            // Update profile page radio buttons if they exist
            if (newTheme === 'dark-theme' && darkThemeRadio) darkThemeRadio.checked = true;
            else if (newTheme === 'light-theme' && lightThemeRadio) lightThemeRadio.checked = true;
        });
    }

    /**
     * Updates the theme toggle button icon and aria-label.
     * @param {string} currentTheme - The currently active theme ('light-theme' or 'dark-theme').
     */
    function updateThemeToggleButton(currentTheme) {
        if (themeToggleButton) {
            if (currentTheme === 'dark-theme') {
                themeToggleButton.innerHTML = '<i class="fas fa-sun"></i>'; // Sun icon for dark mode
                themeToggleButton.setAttribute('aria-label', 'Switch to light theme');
            } else {
                themeToggleButton.innerHTML = '<i class="fas fa-moon"></i>'; // Moon icon for light mode
                themeToggleButton.setAttribute('aria-label', 'Switch to dark theme');
            }
        }
    }

    /**
     * Applies a visual transition effect during theme switching.
     * @param {string} targetMode - 'light' or 'dark', indicating the target theme.
     * @param {Function} callback - Function to execute when the transition is ready to apply the new theme.
     */
    function applyThemeTransition(targetMode, callback) {
        const rootStyles = getComputedStyle(document.documentElement);
        const primaryColor = targetMode === 'dark' ? rootStyles.getPropertyValue('--dark-primary') : rootStyles.getPropertyValue('--primary');

        themeTransitionOverlay.style.backgroundColor = primaryColor;
        themeTransitionOverlay.classList.add('active'); // Activate transition

        // Use a timeout to allow the transition to start visually
        setTimeout(() => {
            callback(); // Apply the new theme classes
            // After applying new theme, fade out the overlay
            themeTransitionOverlay.classList.add('fade-out');
            themeTransitionOverlay.classList.remove('active');
        }, 300); // This delay should match the CSS transition duration for 'active' state

        // Clean up after fade-out
        themeTransitionOverlay.addEventListener('transitionend', function handler() {
            if (!themeTransitionOverlay.classList.contains('active')) {
                themeTransitionOverlay.classList.remove('fade-out');
                themeTransitionOverlay.style.backgroundColor = ''; // Reset background
                themeTransitionOverlay.removeEventListener('transitionend', handler);
            }
        });
    }

    // Event listener for theme toggle button
    if (themeToggleButton) {
        themeToggleButton.addEventListener('click', toggleTheme);
    }

    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
        const newSystemTheme = event.matches ? 'dark-theme' : 'light-theme';
        // Only update if the user hasn't explicitly set a theme in localStorage
        if (!localStorage.getItem('theme')) {
            body.classList.remove('light-theme', 'dark-theme');
            body.classList.add(newSystemTheme);
            updateThemeToggleButton(newSystemTheme);
        }
    });

    // --- Parallax / Tilt Effect for Cards and CTAs ---
    const tiltableElements = document.querySelectorAll(
        '.feature-item, .step-item, .template-option-card, .cv-item, .login-form-container, .signup-form-container, .contact-form-container, .cv-form, .template-selection-page-container, .cv-list-container, .cv-preview-page-wrapper, .page-container, .profile-container'
    );

    tiltableElements.forEach(element => {
        let timeoutId;

        element.addEventListener('mousemove', (e) => {
            clearTimeout(timeoutId); // Clear any existing timeout

            const rect = element.getBoundingClientRect();
            const x = e.clientX - rect.left; // x position within the element
            const y = e.clientY - rect.top;  // y position within the element

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            // Calculate rotation based on mouse position relative to center
            // Invert Y-axis for natural tilt (mouse up = tilt up)
            const rotateX = ((y - centerY) / centerY) * -1 * parseFloat(getComputedStyle(element).getPropertyValue('--card-tilt-max'));
            const rotateY = ((x - centerX) / centerX) * parseFloat(getComputedStyle(element).getPropertyValue('--card-tilt-max'));

            element.style.setProperty('--rotate-x', `${rotateX}deg`);
            element.style.setProperty('--rotate-y', `${rotateY}deg`);
            element.style.setProperty('--scale', '1.01'); // Slight scale on hover
        });

        element.addEventListener('mouseleave', () => {
            // Add a slight delay before resetting to smooth out the transition
            timeoutId = setTimeout(() => {
                element.style.setProperty('--rotate-x', '0deg');
                element.style.setProperty('--rotate-y', '0deg');
                element.style.setProperty('--scale', '1');
            }, 100); // Adjust delay as needed
        });
    });

    // --- Ripple Effect for Buttons ---
    const buttonsWithRipple = document.querySelectorAll('.btn, a.btn, button');

    buttonsWithRipple.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - (size / 2);
            const y = e.clientY - rect.top - (size / 2);

            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            ripple.classList.add('ripple');

            // Ensure only one ripple is active at a time to prevent visual clutter
            const existingRipple = this.querySelector('.ripple');
            if (existingRipple) {
                existingRipple.remove();
            }

            this.appendChild(ripple);

            // Remove the ripple element after animation
            ripple.addEventListener('animationend', () => {
                ripple.remove();
            });
        });
    });

    // --- Utility Function to Display Form Messages ---
    function displayFormMessage(areaId, message, type = 'error') {
        const messageArea = document.getElementById(areaId);
        if (messageArea) {
            // Clear previous messages but keep the area visible if it was already showing a message
            // This prevents a flicker if a new message appears immediately after an old one fades.
            const existingMessagePElem = messageArea.querySelector('.message');
            if (existingMessagePElem) {
                existingMessagePElem.remove(); // Remove the old message element
            }

            const messagePElem = document.createElement('p');
            messagePElem.textContent = message;
            // Add base 'message' class and type class
            messagePElem.className = 'message ' + type;
            messageArea.appendChild(messagePElem);
            // Add ARIA roles for accessibility
            if (type === 'error') {
                messagePElem.setAttribute('role', 'alert');
            } else if (type === 'info' || type === 'success') {
                messagePElem.setAttribute('role', 'status');
            }
            messageArea.style.display = 'block'; // Ensure the message area container is visible

            // Automatically fade out messages after a delay (integrated animation)
            const fadeDelay = 5000; // 5 seconds
            // Use a small timeout before adding fade-out to ensure the element is rendered and transition works
            setTimeout(() => {
                messagePElem.classList.add('fade-out');
            }, fadeDelay);

            // Remove the element from the DOM after the fade transition finishes
            messagePElem.addEventListener('transitionend', function handler() {
                // Check if the transition that ended is the opacity transition for 'fade-out'
                if (event.propertyName === 'opacity' && messagePElem.classList.contains('fade-out')) {
                    messagePElem.removeEventListener('transitionend', handler); // Remove listener
                    messagePElem.remove(); // Remove the element completely from the DOM
                    // Optional: Hide the messageArea container if it's empty after removing the message
                    if (messageArea.children.length === 0) {
                        messageArea.style.display = 'none';
                    }
                }
            });

        } else {
            console.error("Message area not found: #" + areaId + " - Message: " + message);
        }
    }

    // --- Handles Signup form submission ---
    async function signupUser(event) {
        event.preventDefault();
        const messageAreaId = 'signup-message-area';
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const form = event.target; // Get the form element

        // Clear previous messages and remove error classes
        displayFormMessage(messageAreaId, '', 'info'); // Clear message area
        // Remove error classes from fields
        [emailInput, passwordInput, confirmPasswordInput].forEach(input => {
            if (input && input.parentElement && input.parentElement.classList.contains('form-group')) {
                input.parentElement.classList.remove('has-error');
            }
        });


        if (!emailInput || !passwordInput || !confirmPasswordInput) {
            displayFormMessage(messageAreaId, "Gabim: Elementet e formularit të regjistrimit mungojnë.");
            return;
        }

        const email = emailInput.value.trim();
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        let hasError = false; // Flag to track validation errors

        // Client-side validation with visual feedback
        if (email === '') {
            displayFormMessage(messageAreaId, 'Ju lutemi plotësoni të gjitha fushat e kërkuara.', 'error');
            if (emailInput && emailInput.parentElement && emailInput.parentElement.classList.contains('form-group')) emailInput.parentElement.classList.add('has-error');
            hasError = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            displayFormMessage(messageAreaId, 'Formati i emailit është i pavlefshëm.', 'error');
            if (emailInput && emailInput.parentElement && emailInput.parentElement.classList.contains('form-group')) emailInput.parentElement.classList.add('has-error');
            hasError = true;
        }
        if (password === '') {
            displayFormMessage(messageAreaId, 'Ju lutemi plotësoni të gjitha fushat e kërkuara.', 'error');
            if (passwordInput && passwordInput.parentElement && passwordInput.parentElement.classList.contains('form-group')) passwordInput.parentElement.classList.add('has-error');
            hasError = true;
        } else if (password.length < 6) {
            displayFormMessage(messageAreaId, 'Fjalëkalimi duhet të ketë të paktën 6 karaktere.', 'error');
            if (passwordInput && passwordInput.parentElement && passwordInput.parentElement.classList.contains('form-group')) passwordInput.parentElement.classList.add('has-error');
            hasError = true;
        }
        if (confirmPassword === '') {
            displayFormMessage(messageAreaId, 'Ju lutemi plotësoni të gjitha fushat e kërkuara.', 'error');
            if (confirmPasswordInput && confirmPasswordInput.parentElement && confirmPasswordInput.parentElement.classList.contains('form-group')) confirmPasswordInput.parentElement.classList.add('has-error');
            hasError = true;
        } else if (password !== confirmPassword) {
            displayFormMessage(messageAreaId, 'Fjalëkalimet nuk përputhen.', 'error');
            if (confirmPasswordInput && confirmPasswordInput.parentElement && confirmPasswordInput.parentElement.classList.contains('form-group')) confirmPasswordInput.parentElement.classList.add('has-error');
            if (confirmPasswordInput) confirmPasswordInput.value = ''; // Clear confirm password field
            hasError = true;
        }

        if (hasError) {
            // Focus the first input with an error
            const firstErrorInput = form.querySelector('.form-group.has-error input, .form-group.has-error textarea, .form-group.has-error select');
            if (firstErrorInput) firstErrorInput.focus();
            return; // Stop submission if client-side validation fails
        }

        // Show a loading indicator (optional, depends on your HTML structure)
        const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('loading'); // Add a loading class for styling
        }


        try {
            // Send data to process_signup.php
            const response = await fetch('process_signup.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email: email, password: password, confirm_password: confirmPassword })
            });

            // Handle HTTP errors
            if (!response.ok) {
                console.error('Signup HTTP Error:', response.status, response.statusText);
                const errorText = await response.text(); // Get error details from response body
                displayFormMessage(messageAreaId, `Gabim serveri (${response.status}). Detaje: ${errorText.substring(0, 100)}... Provoni më vonë.`, 'error');
                return;
            }

            const result = await response.json(); // Parse JSON response

            if (result.success) {
                // Clear form fields on success
                if (emailInput) emailInput.value = '';
                if (passwordInput) passwordInput.value = '';
                if (confirmPasswordInput) confirmPasswordInput.value = '';
                // Trigger page transition to login page with success message
                triggerPageTransition('login.html?success=' + encodeURIComponent(result.message));
            } else {
                // Display error message from the server
                displayFormMessage(messageAreaId, result.message || 'Gabim gjatë regjistrimit. Provoni përsëri.', 'error');
                if (passwordInput) passwordInput.value = ''; // Clear password fields on error
                if (confirmPasswordInput) confirmPasswordInput.value = '';
                // Add error classes based on server response if available (e.g., result.fieldErrors)
                if (result.fieldErrors) {
                    if (result.fieldErrors.email && emailInput && emailInput.parentElement && emailInput.parentElement.classList.contains('form-group')) emailInput.parentElement.classList.add('has-error');
                    if (result.fieldErrors.password && passwordInput && passwordInput.parentElement && passwordInput.parentElement.classList.contains('form-group')) passwordInput.parentElement.classList.add('has-error');
                    if (result.fieldErrors.confirm_password && confirmPasswordInput && confirmPasswordInput.parentElement && confirmPasswordInput.parentElement.classList.contains('form-group')) confirmPasswordInput.parentElement.classList.add('has-error');
                }
                if (passwordInput) passwordInput.focus(); // Focus password field for correction
            }
        } catch (error) {
            console.error('Error during signup fetch/JSON parse:', error);
            displayFormMessage(messageAreaId, 'Gabim rrjeti ou përgjigje e pavlefshme nga serveri gjatë regjistrimit. Provoni përsëri.', 'error');
        } finally {
            // Re-enable submit button and remove loading class
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.classList.remove('loading');
            }
        }
    }

    // --- Handles Login form submission ---
    async function loginUser(event) {
        event.preventDefault();
        const messageAreaId = 'login-message-area';
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const form = event.target; // Get the form element

        // Clear previous messages and remove error classes
        displayFormMessage(messageAreaId, '', 'info'); // Clear message area
        [emailInput, passwordInput].forEach(input => {
            if (input && input.parentElement && input.parentElement.classList.contains('form-group')) {
                input.parentElement.classList.remove('has-error');
            }
        });

        if (!emailInput || !passwordInput) {
            displayFormMessage(messageAreaId, "Gabim: Elementet e formularit të kyçjes mungojnë.", 'error');
            return;
        }

        const email = emailInput.value.trim();
        const password = passwordInput.value;

        let hasError = false; // Flag to track validation errors

        // Client-side validation with visual feedback
        if (email === '') {
            displayFormMessage(messageAreaId, 'Ju lutem plotësoni emailin dhe fjalëkalimin.', 'error');
            if (emailInput && emailInput.parentElement && emailInput.parentElement.classList.contains('form-group')) emailInput.parentElement.classList.add('has-error');
            hasError = true;
        } else if (!filterVarEmail(email)) { // Use the email validation utility
            displayFormMessage(messageAreaId, 'Formati i emailit është i pavlefshëm.', 'error');
            if (emailInput && emailInput.parentElement && emailInput.parentElement.classList.contains('form-group')) emailInput.parentElement.classList.add('has-error');
            hasError = true;
        }
        if (password === '') {
            displayFormMessage(messageAreaId, 'Ju lutem plotësoni emailin dhe fjalëkalimin.', 'error');
            if (passwordInput && passwordInput.parentElement && passwordInput.parentElement.classList.contains('form-group')) passwordInput.parentElement.classList.add('has-error');
            hasError = true;
        }

        if (hasError) {
            const firstErrorInput = form.querySelector('.form-group.has-error input, .form-group.has-error textarea, .form-group.has-error select');
            if (firstErrorInput) firstErrorInput.focus();
            return; // Stop submission if client-side validation fails
        }

        // Show a loading indicator
        const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('loading');
        }


        try {
            // Send data to process_login.php
            const response = await fetch('process_login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email: email, password: password })
            });

            // Handle HTTP errors
            if (!response.ok) {
                console.error('Login HTTP Error:', response.status, response.statusText);
                const errorText = await response.text();
                displayFormMessage(messageAreaId, `Gabim serveri (${response.status}). Detaje: ${errorText.substring(0, 100)}... Provoni më vonë.`, 'error');
                return;
            }

            const result = await response.json(); // Parse JSON response

            if (result.success) {
                const redirectUrl = result.redirect || 'view_cvs.php'; // Default redirect
                triggerPageTransition(redirectUrl); // Trigger page transition
            } else {
                // Display error message from the server
                displayFormMessage(messageAreaId, result.message || 'Email ose fjalëkalimi gabim.', 'error');
                if (passwordInput) passwordInput.value = ''; // Clear password field on error
                // Add error classes based on server response if available
                if (result.fieldErrors) {
                    if (result.fieldErrors.email && emailInput && emailInput.parentElement && emailInput.parentElement.classList.contains('form-group')) emailInput.parentElement.classList.add('has-error');
                    if (result.fieldErrors.password && passwordInput && passwordInput.parentElement && passwordInput.parentElement.classList.contains('form-group')) passwordInput.parentElement.classList.add('has-error');
                }
                if (passwordInput) passwordInput.focus(); // Focus password field for correction
            }
        } catch (error) {
            console.error('Error during login fetch/JSON parse:', error);
            displayFormMessage(messageAreaId, 'Gabim rrjeti ou përgjigje e pavlefshme nga serveri gjatë kyçjes. Provoni përsëri.', 'error');
        } finally {
            // Re-enable submit button and remove loading class
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.classList.remove('loading');
            }
        }
    }

    // Simple client-side email format validation utility
    function filterVarEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // --- Update Copyright Year ---
    function updateCopyrightYear() {
        const yearSpan = document.getElementById('current-year');
        if (yearSpan) {
            yearSpan.textContent = new Date().getFullYear();
        }
    }

    // --- Entrance Animation Logic ---
    function triggerEntranceAnimation() {
        // Select all main content containers that should animate in
        const mainContainers = document.querySelectorAll(
            '.login-form-container, ' +
            '.signup-form-container, ' +
            '.cv-form, ' +
            '.cv-list-container, ' +
            '.cv-preview-page-wrapper, ' +
            '.template-selection-page-container,' +
            '.contact-form-container,' +
            '.page-container,' + // General page container
            '.homepage-content-wrapper,' + // Homepage wrapper
            '.profile-container' // Profile page container
        );

        mainContainers.forEach(container => {
            if (container) {
                // Add 'animate-in' class to trigger CSS animation
                // Avoid re-adding if animation is already done (optional, depending on CSS)
                if (!container.classList.contains('animate-in-done')) {
                    container.classList.add('animate-in');
                }
            } else {
                // console.log('Container element not found for entrance animation.');
            }
        });
    }

    // --- Mouse Following Glow Effect ---
    function setupMouseFollowingGlow() {
        // Select elements that should have the mouse glow effect
        const containers = document.querySelectorAll('.glow-container');

        containers.forEach(container => {
            // Add mousemove listener to update CSS variables for glow position
            container.addEventListener('mousemove', (e) => {
                const rect = container.getBoundingClientRect();
                // Calculate position relative to the container
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                container.style.setProperty('--glow-translate-x', `${x}px`);
                container.style.setProperty('--glow-translate-y', `${y}px`);
            });
            // Add mouseenter/mouseleave to control glow opacity
            container.addEventListener('mouseenter', () => {
                container.style.setProperty('--glow-opacity', '1'); // Show glow on hover
            });
            container.addEventListener('mouseleave', () => {
                container.style.setProperty('--glow-opacity', '0'); // Hide glow on mouse leave
            });
        });
    }

    // --- Header Scroll Effect ---
    function setupHeaderScrollEffect() {
        if (!header) {
            return;
        }
        const scrollThreshold = 50; // Scroll distance threshold to trigger effect

        const handleScroll = () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > scrollThreshold) {
                header.classList.add('scrolled'); // Add 'scrolled' class
            } else {
                header.classList.remove('scrolled'); // Remove 'scrolled' class
            }
        };
        window.addEventListener('scroll', handleScroll, { passive: true }); // Use passive listener for performance
        handleScroll(); // Call once on load to set initial state
    }

    // --- Page Transition Overlay Logic ---
    function setupPageTransitions() {
        const logoLink = document.querySelector('.logo-link'); // Select the logo link
        // Use the global themeTransitionOverlay for page transitions
        const overlay = themeTransitionOverlay;

        if (logoLink && overlay) {
            // Add click listener to the logo link
            logoLink.addEventListener('click', function(e) {
                const targetUrl = logoLink.getAttribute('href');
                // Prevent transition if linking to the same page or external URL
                if (window.location.pathname.endsWith(targetUrl) || targetUrl.startsWith('http')) {
                    return;
                }
                e.preventDefault(); // Prevent default link behavior

                // Calculate the origin point for the transition (center of the logo)
                const rect = logoLink.getBoundingClientRect();
                const originX = rect.left + rect.width / 2;
                const originY = rect.top + rect.height / 2;

                // Set CSS variables for the transition origin
                document.body.style.setProperty('--transition-origin-x', `${originX}px`);
                document.body.style.setProperty('--transition-origin-y', `${originY}px`);

                // Set overlay color based on current theme for smooth transition
                const isDark = body.classList.contains('dark-theme');
                const rootStyles = getComputedStyle(document.documentElement);
                const primaryColor = isDark ? rootStyles.getPropertyValue('--dark-primary') : rootStyles.getPropertyValue('--primary');
                overlay.style.backgroundColor = primaryColor;

                overlay.classList.add('active'); // Activate the transition overlay

                // Function to perform the actual redirect
                const performRedirect = () => {
                    window.location.href = targetUrl;
                };

                // Listen for the end of the transition animation
                overlay.addEventListener('transitionend', function handler(event) {
                    // Ensure the correct property (transform) has finished transitioning
                    if (event.propertyName === 'transform' || event.propertyName === 'opacity') { // Listen for either transform or opacity
                        overlay.removeEventListener('transitionend', handler); // Remove listener
                        performRedirect(); // Perform redirect after transition
                    }
                });
                // Fallback timeout in case transitionend doesn't fire
                setTimeout(() => {
                    if (overlay.classList.contains('active')) {
                        performRedirect();
                    }
                }, 1000); // Should match or be slightly longer than CSS transition duration
            });
        }
    }

    // Function to trigger a page transition programmatically
    function triggerPageTransition(url) {
        const overlay = themeTransitionOverlay; // Use the global themeTransitionOverlay
        if (overlay) {
            // Set transition origin to the center of the screen for programmatic transitions
            document.body.style.setProperty('--transition-origin-x', '50%');
            document.body.style.setProperty('--transition-origin-y', '50%');

            // Set overlay color based on current theme for smooth transition
            const isDark = body.classList.contains('dark-theme');
            const rootStyles = getComputedStyle(document.documentElement);
            const primaryColor = isDark ? rootStyles.getPropertyValue('--dark-primary') : rootStyles.getPropertyValue('--primary');
            overlay.style.backgroundColor = primaryColor;

            overlay.classList.add('active'); // Activate the overlay
            const performRedirect = () => {
                window.location.href = url; // Perform redirect
            };

            // Listen for transition end or use a fallback timeout
            overlay.addEventListener('transitionend', function handler(event) {
                if (event.propertyName === 'transform' || event.propertyName === 'opacity') {
                    overlay.removeEventListener('transitionend', handler);
                    performRedirect();
                }
            });

            setTimeout(() => {
                if (overlay.classList.contains('active')) {
                    performRedirect();
                }
            }, 1000); // Should match or be slightly longer than CSS transition duration

        } else {
            // If overlay not found, just redirect
            window.location.href = url;
        }
    }

    // --- Utility function for htmlspecialchars (client-side basic escaping) ---
    function htmlspecialchars(str) {
        if (typeof str !== 'string') return String(str);
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return str.replace(/[&<>"']/g, m => map[m]);
    }

    // --- Client-side Filtering/Sorting/Search Logic (CV List Page) ---
    function filterAndSortCVs() {
        const searchInput = document.getElementById('search-cvs');
        const sortSelect = document.getElementById('sort-cvs');
        const cvListElement = document.getElementById('cv-list');
        const loadingSpinner = document.getElementById('cv-list-loading');
        const noSearchResultsMessageElement = document.getElementById('no-search-results-message');

        if (!cvListElement) return; // Exit if CV list element is not found

        // Cache CV items if not already cached
        if (cvItems.length === 0 && cvListElement.querySelectorAll('.cv-item').length > 0) {
            cvItems = Array.from(cvListElement.querySelectorAll('.cv-item'));
        }
        // Get the original "No CVs" message element
        const originalNoCvsMessage = document.querySelector('.empty-state-container');

        // If no CVs exist at all, show the empty state and hide other elements
        if (cvItems.length === 0) {
            if (originalNoCvsMessage) originalNoCvsMessage.style.display = 'block';
            if (noSearchResultsMessageElement) noSearchResultsMessageElement.style.display = 'none';
            if (loadingSpinner) loadingSpinner.style.display = 'none';
            return;
        }
        // If CVs exist, hide the original empty state
        if (originalNoCvsMessage) originalNoCvsMessage.style.display = 'none';

        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const sortValue = sortSelect ? sortSelect.value : 'date_desc'; // Default sort

        // Show loading spinner while processing (optional, for longer lists)
        // Only show if there's a search term or a sort change might take time
        if (loadingSpinner && (searchTerm || sortSelect)) {
            loadingSpinner.style.display = 'block';
        }


        let itemsToProcess = [...cvItems]; // Create a mutable copy for filtering and sorting

        // Filter items based on search term
        let filteredItems = itemsToProcess.filter(item => {
            const title = item.getAttribute('data-title') ? item.getAttribute('data-title').toLowerCase() : '';
            const name = item.getAttribute('data-name') ? item.getAttribute('data-name').toLowerCase() : '';
            const template = item.getAttribute('data-template') ? item.getAttribute('data-template').toLowerCase() : '';
            // Include summary keywords in search
            const summary = item.getAttribute('data-summary-keywords') ? item.getAttribute('data-summary-keywords').toLowerCase() : '';
            return title.includes(searchTerm) || name.includes(searchTerm) || template.includes(searchTerm) || summary.includes(searchTerm);
        });

        // Sort filtered items
        filteredItems.sort((a, b) => {
            const titleA = a.getAttribute('data-title') ? a.getAttribute('data-title').toLowerCase() : '';
            const titleB = b.getAttribute('data-title') ? b.getAttribute('data-title').toLowerCase() : '';
            const nameA = a.getAttribute('data-name') ? a.getAttribute('data-name').toLowerCase() : '';
            const nameB = b.getAttribute('data-name') ? b.getAttribute('data-name').toLowerCase() : '';

            const dateA_str = a.getAttribute('data-date') || '';
            const dateB_str = b.getAttribute('data-date') || '';
            // Parse dates, fallback to 0 if invalid
            let dateA_timestamp = dateA_str ? new Date(dateA_str).getTime() : 0;
            let dateB_timestamp = dateB_str ? new Date(dateB_str).getTime() : 0;
            if (isNaN(dateA_timestamp)) dateA_timestamp = 0;
            if (isNaN(dateB_timestamp)) dateB_timestamp = 0;

            switch (sortValue) {
                case 'title_asc': return titleA.localeCompare(titleB);
                case 'title_desc': return titleB.localeCompare(titleA);
                case 'name_asc': return nameA.localeCompare(nameB);
                case 'name_desc': return nameB.localeCompare(nameA);
                case 'date_asc': return dateA_timestamp - dateB_timestamp;
                case 'date_desc':
                default: return dateB_timestamp - dateA_timestamp; // Default to newest first
            }
        });

        // Update the DOM with filtered and sorted items
        const fragment = document.createDocumentFragment();
        filteredItems.forEach(item => fragment.appendChild(item.cloneNode(true))); // Append cloned items

        cvListElement.innerHTML = ''; // Clear current list
        cvListElement.appendChild(fragment); // Add filtered/sorted items

        // Re-apply scroll animations to the newly added/cloned items
        // Use a small timeout to ensure elements are in the DOM before observing
        setTimeout(() => {
            if (typeof setupScrollAnimations === 'function') {
                setupScrollAnimations(cvListElement); // Pass the container of the new items
            }
        }, 50);


        // Hide loading spinner
        if (loadingSpinner) loadingSpinner.style.display = 'none';

        // Show "no search results" message if no items match the search term
        if (noSearchResultsMessageElement) {
            noSearchResultsMessageElement.style.display = filteredItems.length === 0 && searchTerm ? 'block' : 'none';
        }
    }

    // --- Local Storage Clearing Functions (for form data persistence) ---
    function clearPersonalFormLocalStorage() {
        let clearedCount = 0;
        // Iterate through local storage and remove items with the personal form prefix
        for (let i = localStorage.length - 1; i >= 0; i--) {
            const key = localStorage.key(i);
            if (key && key.startsWith('cv_form_personal_')) {
                localStorage.removeItem(key);
                clearedCount++;
            }
        }
        if (clearedCount > 0) console.log('Cleared', clearedCount, 'personal form items from localStorage.');
    }

    function clearExperienceFormLocalStorage() {
        let clearedCount = 0;
        // Iterate through local storage and remove items with the experience form prefix
        for (let i = localStorage.length - 1; i >= 0; i--) {
            const key = localStorage.key(i);
            if (key && key.startsWith('cv_form_experience_')) {
                localStorage.removeItem(key);
                clearedCount++;
            }
        }
        if (clearedCount > 0) console.log('Cleared', clearedCount, 'experience form items from localStorage.');
    }

    // --- Mobile Menu Toggle ---
    function setupMobileMenu() {
        if (mobileMenuToggle && mobileNavMenu) {
            mobileMenuToggle.addEventListener('click', () => {
                const isExpanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true' || false;
                mobileMenuToggle.setAttribute('aria-expanded', !isExpanded); // Toggle aria-expanded attribute
                mobileNavMenu.classList.toggle('open'); // Toggle 'open' class for CSS styling

                // Animate max-height for smooth open/close
                if (!isExpanded) {
                    // Set max-height to the scroll height to allow CSS transition
                    mobileNavMenu.style.maxHeight = mobileNavMenu.scrollHeight + "px";
                } else {
                    mobileNavMenu.style.maxHeight = null; // Remove max-height to collapse
                }
            });

            // Close mobile menu when a link is clicked (optional, but good UX)
            mobileNavMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenuToggle.setAttribute('aria-expanded', 'false');
                    mobileNavMenu.classList.remove('open');
                    mobileNavMenu.style.maxHeight = null;
                });
            });
        }
    }


    // --- Text Unveil on Scroll (reveal-on-scroll) ---
    // Reverted to original behavior: elements start hidden/blurred and unveil when entering viewport.
    function setupScrollAnimations(container = document) {
        const revealElements = container.querySelectorAll('.reveal-on-scroll');
        if (!revealElements.length) return; // Exit if no elements found

        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                // Trigger 'is-visible' when the element enters the viewport
                if (entry.isIntersecting) {
                    // Use the data-reveal-delay attribute for staggered unveil if desired
                    const delay = parseInt(entry.target.dataset.revealDelay) || 0;

                    setTimeout(() => {
                        entry.target.classList.add('is-visible'); // Add 'is-visible' to trigger CSS transition
                    }, delay);
                    // Unobserve after triggering unveil, as the unveil is a one-time event per element
                    observer.unobserve(entry.target);
                }
            });
        }, {
            root: null, // Use the viewport as the root
            threshold: 0.1 // Trigger when 10% of the element is visible
        });

        // Observe each element with the 'reveal-on-scroll' class
        revealElements.forEach(el => {
            // Check if the element is already in the viewport on page load
            const rect = el.getBoundingClientRect();
            const isAlreadyVisible = (rect.top < window.innerHeight && rect.bottom >= 0) &&
                (rect.left < window.innerWidth && rect.right >= 0);

            if (isAlreadyVisible) {
                // If already visible, apply the 'is-visible' class immediately (or with a small delay)
                setTimeout(() => {
                    el.classList.add('is-visible');
                }, 50); // Small delay to allow DOM to settle and CSS transition to start
                // No need to observe if already visible and handled
            } else {
                // If not visible, observe it for the initial unveil when it scrolls into view
                revealObserver.observe(el);
            }
        });
    }


    // --- Handles Contact Form Submission ---
    async function handleContactFormSubmit(event) {
        event.preventDefault(); // Prevent default form submission
        const messageAreaId = 'contact-message-area';
        const form = event.target;
        const formData = new FormData(form);
        const jsonData = {};

        // Convert form data to JSON object
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        // Clear previous messages and show a loading/info message
        displayFormMessage(messageAreaId, 'Dërgohet mesazhi...', 'info'); // Show sending message

        // Show a loading indicator
        const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('loading');
        }

        try {
            // Send form data as JSON to process_feedback.php
            const response = await fetch(form.action, {
                method: form.method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(jsonData)
            });

            const result = await response.json(); // Parse JSON response

            if (result.success) {
                displayFormMessage(messageAreaId, result.message, 'success'); // Show success message
                form.reset(); // Reset the form on success
            } else {
                displayFormMessage(messageAreaId, result.message || 'Gabim gjatë dërgimit të mesazhit. Provoni përsëri.', 'error'); // Show error message
            }

        } catch (error) {
            console.error('Error submitting contact form:', error);
            displayFormMessage(messageAreaId, 'Gabim rrjeti ou përgjigje e pavlefshme nga serveri gjatë dërgimit të mesazhit. Provoni përsëri.', 'error');
        } finally {
            // Re-enable submit button and remove loading class
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.classList.remove('loading');
            }
        }
    }

    // --- Auto-hide messages on pages like homepage ---
    function setupAutoHidePageMessages() {
        const messageElement = document.getElementById('autoHidePageMessage'); // Select the message element

        // If the message element exists and has content
        if (messageElement && messageElement.textContent.trim() !== '') {
            // Set a timeout to start the fade-out animation
            setTimeout(() => {
                messageElement.classList.add('fade-out');

                // After the fade-out animation, hide the message area completely
                setTimeout(() => {
                    if (messageElement.parentNode && messageElement.parentNode.classList.contains('message-area')) {
                        messageElement.parentNode.style.display = 'none';
                    }
                    // Remove the element from the DOM after hiding
                    messageElement.remove();
                }, 500); // Should match the CSS transition duration for opacity
            }, 2500); // Message visible for 2.5 seconds before fading
        }
    }

    // --- Background CV Generation and Animation ---
    function createBackgroundCV() {
        const cv = document.createElement('div');
        cv.classList.add('background-cv');

        // Create simplified internal structure based on template previews
        const header = document.createElement('div');
        header.classList.add('header-placeholder');
        cv.appendChild(header);

        // Add lines and blocks based on a simple pattern
        const numLines = Math.floor(Math.random() * 5) + 5; // 5 to 9 lines
        for (let i = 0; i < numLines; i++) {
            const line = document.createElement('div');
            line.classList.add('line-placeholder');
            // Randomize line width slightly for variety
            line.style.width = `${Math.floor(Math.random() * 30) + 50}%`; // 50% to 80% width
            cv.appendChild(line);
        }

        const numBlocks = Math.floor(Math.random() * 2) + 1; // 1 or 2 blocks
        for (let i = 0; i < numBlocks; i++) {
            const block = document.createElement('div');
            block.classList.add('block-placeholder');
            cv.appendChild(block);
        }


        // Randomize initial position and rotation
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        // Position them mostly outside or near the edges initially
        // Adjusted positioning to be slightly more centered but still scattered
        const randomX = Math.random() * viewportWidth * 1.8 - viewportWidth * 0.4; // Wider range
        const randomY = Math.random() * viewportHeight * 1.8 - viewportHeight * 0.4; // Wider range


        cv.style.left = `${randomX}px`;
        cv.style.top = `${randomY}px`;
        cv.style.transform = `rotate(${Math.random() * 40 - 20}deg)`; // Increased random rotation (-20deg to 20deg)

        // Randomize animation delay and duration slightly
        cv.style.animationDelay = `${Math.random() * 10}s`; // 0 to 10 seconds delay
        cv.style.animationDuration = `${Math.random() * 30 + 40}s`; // 40 to 70 seconds duration

        // Assign a random animation name from the defined keyframes
        const animationNames = ['moveAndRotateSmooth1', 'moveAndRotateSmooth2', 'moveAndRotateSmooth3'];
        const randomAnimation = animationNames[Math.floor(Math.random() * animationNames.length)];
        cv.style.animationName = randomAnimation;
        cv.style.animationTimingFunction = 'ease-in-out';
        cv.style.animationIterationCount = 'infinite';
        cv.style.animationDirection = 'alternate';

        // Set random end position and rotation for the animation using CSS variables
        const endX = Math.random() * viewportWidth * 1.2 - viewportWidth * 0.6; // Move within a wider range
        const endY = Math.random() * viewportHeight * 1.2 - viewportHeight * 0.6;
        const endRotate = Math.random() * 60 - 30; // Rotate more
        const endScale = Math.random() * 0.4 + 0.7; // Scale more significantly

        cv.style.setProperty('--move-x', `${endX}px`);
        cv.style.setProperty('--move-y', `${endY}px`);
        cv.style.setProperty('--rotate-deg', `${endRotate}deg`);
        cv.style.setProperty('--scale', `${endScale}`);


        return cv;
    }

    function setupBackgroundCVs() {
        const container = document.querySelector('.background-cvs-container');
        if (!container) {
            console.warn("Background CVs container not found. Skipping background effect.");
            return;
        }

        const numberOfCVs = 35; // Increased number of background CVs

        // Clear any existing background CVs
        container.innerHTML = '';

        // Create and append background CV elements
        for (let i = 0; i < numberOfCVs; i++) {
            const cv = createBackgroundCV();
            container.appendChild(cv);
        }

        // Re-generate on window resize to adjust positions
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(setupBackgroundCVs, 200); // Debounce resize
        });
    }


    // --- DOMContentLoaded Event Listener ---
    // This is the main entry point for all JavaScript logic once the DOM is fully loaded.
    // It ensures all elements are available before script attempts to interact with them.
    // console.log('DOMContentLoaded fired (script.js)');

    updateCopyrightYear(); // Update the copyright year

    // Initialize theme based on user preference or system settings
    initTheme();

    // Event listeners for profile page radio buttons (integrated)
    if (lightThemeRadio) {
        lightThemeRadio.addEventListener('change', function() {
            if (this.checked) {
                // Directly call toggleTheme to ensure transition and localStorage update
                if (body.classList.contains('dark-theme')) { // Only toggle if currently dark
                    toggleTheme();
                }
            }
        });
    }

    if (darkThemeRadio) {
        darkThemeRadio.addEventListener('change', function() {
            if (this.checked) {
                // Directly call toggleTheme to ensure transition and localStorage update
                if (body.classList.contains('light-theme')) { // Only toggle if currently light
                    toggleTheme();
                }
            }
        });
    }

    // Ensure mobile menu toggle targets the correct element
    setupMobileMenu(); // Setup mobile menu functionality

    setupScrollAnimations(); // Initial call for elements present on page load (e.g. header, page title, hero section)
    setupAutoHidePageMessages(); // Setup auto-hide for specific page messages
    setupBackgroundCVs(); // Setup background CV generation and animation

    // --- Smooth Scrolling for Anchor Links ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default jump behavior

            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                // Scroll smoothly to the target element
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });


    // Add click animation to buttons (Enhanced)
    document.addEventListener('click', function(event) {
        const target = event.target;
        // Check if the clicked element or its closest ancestor is a button or link styled as a button
        // Exclude elements that have their own specific click handling or shouldn't animate
        const button = target.closest('.btn, button:not(#theme-toggle-button):not(.modal-close-button):not([type="reset"]):not(#mobile-menu-toggle):not(.quick-preview-btn), input[type="submit"]');
        if (button) {
            // Use the new button-press-animation class
            // Check if the animation class is already present to avoid stacking animations
            if (!button.classList.contains('button-press-animation')) {
                button.classList.add('button-press-animation');
                // Remove the class after the animation finishes
                button.addEventListener('animationend', () => {
                    button.classList.remove('button-press-animation');
                }, { once: true }); // Use { once: true } to automatically remove the listener
            }
        }
    });

    // Add event listeners for form submissions
    const signupForm = document.querySelector('form.signup-form');
    if (signupForm) signupForm.addEventListener('submit', signupUser);

    const loginForm = document.querySelector('form.login-form');
    if (loginForm) loginForm.addEventListener('submit', loginUser);

    const contactForm = document.getElementById('contact-feedback-form');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactFormSubmit);
    }

    setupPageTransitions(); // Setup page transition effect for logo link
    setTimeout(triggerEntranceAnimation, 50); // Trigger entrance animation shortly after load

    // --- Add 'glow-container' class to relevant elements (New) ---
    // Manually add the class to elements that should have the mouse glow effect.
    // Ensure these elements are positioned relatively or absolutely for the pseudo-element to work correctly.
    document.querySelectorAll('.page-container, .login-form-container, .signup-form-container, .contact-form-container, .cv-form, .template-selection-page-container, .cv-list-container, .cv-preview-page-wrapper, .feature-item, .value-prop-container, .step-item, .template-option-card, .cv-item, .profile-section').forEach(container => {
        // Add the class only if the element doesn't already have a specific position set that might conflict
        const computedStyle = window.getComputedStyle(container);
        if (computedStyle.position === 'static' || computedStyle.position === '') {
            container.style.position = 'relative'; // Set position to relative if it's static
        }
        container.classList.add('glow-container');
    });

    setupMouseFollowingGlow(); // Setup mouse following glow effect (now uses .glow-container)
    setupHeaderScrollEffect(); // Setup header scroll effect


    // Handle messages passed via URL parameters (e.g., after redirect)
    const urlParams = new URLSearchParams(window.location.search);
    const errorParam = urlParams.get('error');
    const successParam = urlParams.get('success');
    const infoParam = urlParams.get('message') || urlParams.get('info'); // Check for 'message' or 'info'

    // Determine which message area to use based on the current page
    let pageMessageAreaId = null;
    if (document.getElementById('login-message-area')) pageMessageAreaId = 'login-message-area';
    else if (document.getElementById('signup-message-area')) pageMessageAreaId = 'signup-message-area';
    else if (document.getElementById('contact-message-area')) pageMessageAreaId = 'contact-message-area';
    // Note: Messages on form.php, experience.php, preview_cv.php, view_cvs.php are handled by PHP displaying session messages.
    // This JS part is mainly for login/signup/contact pages that use JS for form submission.

    // Display messages if a specific message area is found and message params exist
    if (pageMessageAreaId && !document.getElementById('autoHidePageMessage')) { // Avoid double-handling if autoHidePageMessage is present
        if (errorParam) displayFormMessage(pageMessageAreaId, decodeURIComponent(errorParam), 'error');
        else if (successParam) displayFormMessage(pageMessageAreaId, decodeURIComponent(successParam), 'success');
        else if (infoParam) displayFormMessage(pageMessageAreaId, decodeURIComponent(infoParam), 'info');

        // Clean up URL parameters after displaying the message
        if (errorParam || successParam || infoParam) {
            urlParams.delete('error'); urlParams.delete('success'); urlParams.delete('message'); urlParams.delete('info');
            history.replaceState({}, '', `${window.location.pathname}${urlParams.toString() ? '?' + urlParams.toString() : ''}`); // Reconstruct URL
        }
    }


    // Local Storage for Form Data Persistence (Personal and Experience forms)
    const cvFormOnPage = document.querySelector('form.cv-form');
    const currentPathname = window.location.pathname;

    if (cvFormOnPage) {
        const isEditingCv = document.body.classList.contains('editing-cv'); // Check if in edit mode
        // Select all relevant form fields
        const formFields = cvFormOnPage.querySelectorAll('input:not([type="submit"]):not([type="button"]):not([type="hidden"]):not([type="reset"]):not([type="password"]), select, textarea'); // Excluded password fields

        let storagePrefix = '';
        // Determine the correct local storage prefix based on the page
        if (currentPathname.includes('form.php')) storagePrefix = 'cv_form_personal_';
        else if (currentPathname.includes('experience.php')) storagePrefix = 'cv_form_experience_';

        if (storagePrefix) {
            formFields.forEach(field => {
                try {
                    // Load saved data only if the field is currently empty and not in edit mode
                    const shouldLoad = (field.value === '' || (field.tagName === 'SELECT' && field.selectedIndex === 0) || (field.type === 'date' && field.value === '') || (field.type === 'number' && field.value === ''));
                    if (shouldLoad && !isEditingCv) {
                        const savedValue = localStorage.getItem(storagePrefix + field.name);
                        if (savedValue !== null) {
                            field.value = savedValue;
                            // Handle select elements specifically
                            if (field.tagName === 'SELECT') {
                                for (let i = 0; i < field.options.length; i++) {
                                    if (field.options[i].value === savedValue) {
                                        field.options[i].selected = true; break;
                                    }
                                }
                            }
                        }
                    }
                } catch (e) { console.warn("localStorage read error for " + field.name + ":", e); }

                // Save data to local storage on input change
                field.addEventListener('input', function() {
                    try { localStorage.setItem(storagePrefix + this.name, this.value); } // Use this.name and this.value
                    catch (e) { console.warn("localStorage write error for " + this.name + ":", e); }
                });
                // Also save on change for select elements
                if (field.tagName === 'SELECT') {
                    field.addEventListener('change', function() {
                        try { localStorage.setItem(storagePrefix + this.name, this.value); } // Use this.name and this.value
                        catch (e) { console.warn("localStorage write error for " + this.name + ":", e); }
                    });
                }
            });
        }
    }

    // Setup modal functionality for quick preview on view_cvs.php
    const modalOverlay = document.getElementById('preview-modal-overlay');
    const modalCloseButton = document.getElementById('modal-close-button');
    const cvListElementForModal = document.getElementById('cv-list'); // The list containing preview buttons
    const pageContainerForModal = document.querySelector('.cv-list-container'); // Use the container to check for click events

    if (modalOverlay && modalCloseButton && pageContainerForModal) {
        // Add click listener to the CV list container to handle clicks on preview buttons
        pageContainerForModal.addEventListener('click', function(event) {
            const button = event.target.closest('.quick-preview-btn'); // Find the closest preview button
            if (button) {
                const cvId = button.getAttribute('data-cv-id'); // Get the CV ID from the data attribute
                if (cvId) openPreviewModal(cvId); // Open the modal
                else console.error('Quick preview button missing data-cv-id attribute!');
            }
        });
        // Close modal when clicking the overlay itself
        modalOverlay.addEventListener('click', function(event) {
            if (event.target === modalOverlay) closePreviewModal();
        });
        // Close modal when clicking the close button
        modalCloseButton.addEventListener('click', closePreviewModal);
        // Close modal when pressing the Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && modalOverlay.classList.contains('visible')) closePreviewModal();
        });
    }


    // Setup filtering and sorting for the CV list on view_cvs.php
    const searchInputLocal = document.getElementById('search-cvs');
    const sortSelectLocal = document.getElementById('sort-cvs');
    const cvListElementLocal = document.getElementById('cv-list');

    if (cvListElementLocal) {
        // Add event listeners to trigger filtering/sorting
        if (searchInputLocal) searchInputLocal.addEventListener('input', filterAndSortCVs);
        if (sortSelectLocal) sortSelectLocal.addEventListener('change', filterAndSortCVs);
        // Initial call to filterAndSortCVs which will also call setupScrollAnimations for the list
        filterAndSortCVs();
    }

    // --- Optional: Add Parallax Effect to Background CVs on Scroll ---
    if (backgroundCvsContainer) {
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            backgroundCvsContainer.style.transform = `translateY(${scrollTop * 0.1}px)`; // Adjust 0.1 for speed
        }, { passive: true });
    }

    // --- Optional: Add subtle particle effect (requires a canvas element in HTML) ---
    // Check if a canvas element with ID 'particle-canvas' exists
    const particleCanvas = document.getElementById('particle-canvas');
    if (particleCanvas) {
        const ctx = particleCanvas.getContext('2d');
        let particles = [];
        const particleCount = 80; // Use variable from CSS or define here
        const particleColor = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim();
        const particleSize = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--particle-size').trim());

        // Resize canvas to fill container
        function resizeCanvas() {
            particleCanvas.width = backgroundCvsContainer ? backgroundCvsContainer.offsetWidth : window.innerWidth;
            particleCanvas.height = backgroundCvsContainer ? backgroundCvsContainer.offsetHeight : window.innerHeight;
        }

        // Create particles
        function createParticles() {
            particles = [];
            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: Math.random() * particleCanvas.width,
                    y: Math.random() * particleCanvas.height,
                    size: Math.random() * particleSize + 1,
                    speedX: Math.random() * 0.5 - 0.25, // Subtle horizontal movement
                    speedY: Math.random() * 0.5 - 0.25, // Subtle vertical movement
                    opacity: Math.random() * 0.5 + 0.1 // Subtle opacity variation
                });
            }
        }

        // Draw particles
        function drawParticles() {
            ctx.clearRect(0, 0, particleCanvas.width, particleCanvas.height);
            ctx.fillStyle = particleColor;
            particles.forEach(particle => {
                ctx.globalAlpha = particle.opacity;
                ctx.beginPath();
                ctx.arc(particle.x, particle.y, particle.size / 2, 0, Math.PI * 2);
                ctx.fill();
            });
            ctx.globalAlpha = 1; // Reset opacity
        }

        // Update particle positions
        function updateParticles() {
            particles.forEach(particle => {
                particle.x += particle.speedX;
                particle.y += particle.speedY;

                // Wrap particles around the screen
                if (particle.x < 0) particle.x = particleCanvas.width;
                if (particle.x > particleCanvas.width) particle.x = 0;
                if (particle.y < 0) particle.y = particleCanvas.height;
                if (particle.y > particleCanvas.height) particle.y = 0;
            });
        }

        // Animation loop
        function animateParticles() {
            updateParticles();
            drawParticles();
            requestAnimationFrame(animateParticles);
        }

        // Setup on load and resize
        resizeCanvas();
        createParticles();
        animateParticles();

        window.addEventListener('resize', () => {
            resizeCanvas();
            createParticles(); // Recreate particles on resize
        });
    }
});

// --- Global Functions for Modals (openPreviewModal, closePreviewModal) ---
// Function to open the quick preview modal
function openPreviewModal(cvId) {
    const modalOverlay = document.getElementById('preview-modal-overlay');
    const modalLoadingSpinner = document.getElementById('modal-loading-spinner');
    const cvPreviewArea = document.getElementById('cv-preview-area');
    const modalTitle = document.getElementById('modal-title-h');
    const modalCloseButton = document.getElementById('modal-close-button');

    // Ensure all necessary modal elements exist
    if (!modalOverlay || !cvPreviewArea || !modalLoadingSpinner || !modalTitle || !modalCloseButton) {
        console.error('One or more modal elements are MISSING.'); return;
    }
    modalOverlay.classList.add('visible'); // Make the overlay visible
    modalOverlay.setAttribute('aria-hidden', 'false'); // Update ARIA attribute
    // Focus the close button for accessibility after the modal is visible
    requestAnimationFrame(() => {
        // Use a small delay to ensure the button is focusable
        setTimeout(() => {
            if (modalCloseButton) modalCloseButton.focus();
        }, 50);
    });


    cvPreviewArea.innerHTML = ''; // Clear previous preview content
    modalLoadingSpinner.style.display = 'block'; // Show loading spinner
    modalTitle.textContent = 'Parapamje e Shpejtë e CV-së (ID: ' + htmlspecialchars(cvId) + ')'; // Set modal title

    // Fetch the CV preview content from the server
    fetch('fetch_cv_preview_content.php?id=' + cvId)
        .then(response => {
            // Handle HTTP errors (e.g., 404, 500)
            if (!response.ok) {
                // Try to parse JSON error response first, fallback to text
                return response.json().then(errorJson => {
                    throw new Error(errorJson.message || `HTTP error! status: ${response.status}`);
                }).catch(() => {
                    return response.text().then(text => { throw new Error(text || `HTTP error! status: ${response.status}`); });
                });
            }
            return response.text(); // Get the HTML content as text
        })
        .then(html => {
            modalLoadingSpinner.style.display = 'none'; // Hide loading spinner
            cvPreviewArea.innerHTML = html; // Insert the fetched HTML into the preview area
            // After loading modal content, if it has reveal-on-scroll items, initialize them
            // This ensures unveil animation works for content loaded into the modal.
            // Use a small timeout to ensure elements are in the DOM before observing
            setTimeout(() => {
                if (typeof setupScrollAnimations === 'function') {
                    setupScrollAnimations(cvPreviewArea); // Apply animations within the modal
                }
            }, 50);
        })
        .catch(error => {
            console.error('Error fetching/processing CV preview for CV ID ' + cvId + ':', error);
            modalLoadingSpinner.style.display = 'none'; // Hide loading spinner
            // Display an error message in the preview area
            cvPreviewArea.innerHTML = '<p class="message error">Gabim gjatë ngarkimit të parapamjes: ' + htmlspecialchars(error.message.substring(0, 200)) + '...</p>';
            // Ensure the error message also fades out
            const errorMessageElem = cvPreviewArea.querySelector('.message.error');
            if (errorMessageElem) {
                setTimeout(() => {
                    errorMessageElem.classList.add('fade-out');
                }, 5000); // 5 seconds delay

                errorMessageElem.addEventListener('transitionend', function handler() {
                    if (event.propertyName === 'opacity' && errorMessageElem.classList.contains('fade-out')) {
                        errorMessageElem.removeEventListener('transitionend', handler);
                        errorMessageElem.remove();
                    }
                });
            }
        });
}

// Function to close the quick preview modal
function closePreviewModal() {
    const modalOverlay = document.getElementById('preview-modal-overlay');
    const cvPreviewArea = document.getElementById('cv-preview-area');
    const modalTitle = document.getElementById('modal-title-h');
    const modalLoadingSpinner = document.getElementById('modal-loading-spinner'); // Also hide spinner on close
    if (modalOverlay) {
        modalOverlay.classList.remove('visible'); // Hide the overlay
        modalOverlay.setAttribute('aria-hidden', 'true'); // Update ARIA attribute
    }
    if (cvPreviewArea) cvPreviewArea.innerHTML = ''; // Clear preview content
    if (modalTitle) modalTitle.textContent = 'Parapamje e Shpejtë e CV-së'; // Reset modal title
    if (modalLoadingSpinner) modalLoadingSpinner.style.display = 'none'; // Hide spinner
}
