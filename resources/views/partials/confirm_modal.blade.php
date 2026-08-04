<!-- SweetAlert2 & Global Unified Confirmation System -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Global Helper Function for Custom JS Calls
        window.confirmAction = function(message, onConfirm, options = {}) {
            const isDelete = message.includes('حذف') || message.includes('إزالة') || message.includes(
                'تعطيل') || message.includes('إلغاء');

            return Swal.fire({
                title: options.title || 'تأكيد الإجراء',
                text: message,
                icon: options.icon || (isDelete ? 'warning' : 'question'),
                showCancelButton: true,
                confirmButtonColor: options.confirmColor || (isDelete ? '#dc2626' : '#004274'),
                cancelButtonColor: options.cancelColor || '#6b7280',
                confirmButtonText: options.confirmText || (isDelete ? 'نعم، استمرار' :
                    'نعم، تأكيد'),
                cancelButtonText: options.cancelText || 'إلغاء',
                reverseButtons: true,
                customClass: {
                    popup: 'font-cairo rounded-2xl shadow-xl border border-gray-100',
                    title: 'font-cairo font-bold text-gray-800 text-lg',
                    htmlContainer: 'font-almarai text-gray-600 text-sm mt-2',
                    confirmButton: 'font-cairo font-bold rounded-xl px-5 py-2.5 shadow-sm text-sm',
                    cancelButton: 'font-cairo font-bold rounded-xl px-5 py-2.5 text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
                return result;
            });
        };

        // Capture phase handler for forms
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            let confirmMsg = form.getAttribute('data-confirm');

            // Fallback for legacy inline onsubmit="return confirm('...')"
            if (!confirmMsg && form.hasAttribute('onsubmit')) {
                const attr = form.getAttribute('onsubmit') || '';
                const match = attr.match(/confirm\s*\(\s*(['"])(.*?)\1\s*\)/s);
                if (match) {
                    confirmMsg = match[2];
                    form.removeAttribute('onsubmit');
                }
            }

            if (confirmMsg) {
                e.preventDefault();
                e.stopImmediatePropagation();

                window.confirmAction(confirmMsg, function() {
                    HTMLFormElement.prototype.submit.call(form);
                });
            }
        }, true);

        // Capture phase handler for clickable elements (buttons & links)
        document.addEventListener('click', function(e) {
            const target = e.target.closest('[data-confirm], [onclick*="confirm"]');
            if (!target || target.tagName === 'FORM') return;

            let confirmMsg = target.getAttribute('data-confirm');

            if (!confirmMsg && target.hasAttribute('onclick')) {
                const attr = target.getAttribute('onclick') || '';
                const match = attr.match(/confirm\s*\(\s*(['"])(.*?)\1\s*\)/s);
                if (match) {
                    confirmMsg = match[2];
                    target.removeAttribute('onclick');
                }
            }

            if (confirmMsg) {
                e.preventDefault();
                e.stopImmediatePropagation();

                window.confirmAction(confirmMsg, function() {
                    if (target.tagName === 'A' && target.href) {
                        window.location.href = target.href;
                    } else if (target.type === 'submit' && target.form) {
                        HTMLFormElement.prototype.submit.call(target.form);
                    } else {
                        target.click();
                    }
                });
            }
        }, true);
    });
</script>
