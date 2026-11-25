/**
 * UI Helper Utilities
 * Centralized helpers for password visibility, clipboard copying, and audio chimes.
 */

export function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const icon = btn ? btn.querySelector('i') : null;
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    if (icon) {
        if (isPassword) {
            icon.classList.remove('fa-eye', 'fa-regular');
            icon.classList.add('fa-eye-slash', 'fa-regular');
        } else {
            icon.classList.remove('fa-eye-slash', 'fa-regular');
            icon.classList.add('fa-eye', 'fa-regular');
        }
    }
}

export function copyToClipboard(text, successMessage = 'Copied to clipboard!') {
    if (!text) return Promise.reject(new Error('No text provided'));

    if (navigator.clipboard && navigator.clipboard.writeText) {
        return navigator.clipboard.writeText(text).then(() => {
            if (window.notifications && typeof window.notifications.show === 'function') {
                window.notifications.show(successMessage, 'success');
            }
        });
    }

    // Fallback for older browsers
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        if (window.notifications && typeof window.notifications.show === 'function') {
            window.notifications.show(successMessage, 'success');
        }
    } finally {
        document.body.removeChild(textarea);
    }
    return Promise.resolve();
}

export function playAppAudioChime(type = 'info') {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();

        osc.type = 'sine';
        const freqs = {
            success: 587.33, // D5
            warning: 440.00, // A4
            error: 329.63,   // E4
            info: 523.25     // C5
        };
        osc.frequency.setValueAtTime(freqs[type] || freqs.info, audioCtx.currentTime);

        gain.gain.setValueAtTime(0.04, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.25);

        osc.connect(gain);
        gain.connect(audioCtx.destination);

        osc.start();
        osc.stop(audioCtx.currentTime + 0.25);
    } catch {
        // AudioContext not allowed or not supported in current environment
    }
}

// Bind to window for global Blade template accessibility
if (typeof window !== 'undefined') {
    window.togglePasswordVisibility = togglePasswordVisibility;
    window.copyToClipboard = copyToClipboard;
    window.playAppAudioChime = playAppAudioChime;
}
