{{-- Shared in-page PDF preview. Links continue to work normally when JavaScript is unavailable. --}}
<div id="pdfPreviewDialog" class="fixed inset-0 z-[100] hidden p-3 sm:p-6" role="dialog" aria-modal="true"
    aria-labelledby="pdfPreviewTitle" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-950/70" data-pdf-preview-close></div>

    <section class="relative mx-auto flex h-full w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
        role="document">
        <header class="flex shrink-0 items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 sm:px-5">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600" aria-hidden="true">
                    <i class="fas fa-file-pdf"></i>
                </span>
                <div class="min-w-0">
                    <h2 id="pdfPreviewTitle" class="font-cairo text-sm font-bold text-slate-800">معاينة ملف PDF</h2>
                    <p class="truncate font-almarai text-[11px] text-slate-500">راجع الملف ثم احفظه عند جاهزيتك</p>
                </div>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <a id="pdfPreviewDownload" href="#" download data-pdf-preview-download
                    class="inline-flex items-center gap-2 rounded-lg bg-navy px-3 py-2 font-cairo text-xs font-bold text-white transition hover:bg-[#083358] focus:outline-none focus:ring-2 focus:ring-gold focus:ring-offset-2">
                    <i class="fas fa-download"></i>
                    <span class="hidden sm:inline">حفظ الملف</span>
                </a>
                <button type="button" data-pdf-preview-close aria-label="إغلاق المعاينة"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-200 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-gold">
                    <i class="fas fa-xmark text-base"></i>
                </button>
            </div>
        </header>

        <div class="relative min-h-0 flex-1 bg-slate-200">
            <div id="pdfPreviewLoading" class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-3 bg-slate-100 text-slate-600">
                <i class="fas fa-spinner animate-spin text-2xl text-navy" aria-hidden="true"></i>
                <p class="font-cairo text-sm font-bold">جارٍ إعداد معاينة الملف…</p>
            </div>
            <div id="pdfPreviewError" class="absolute inset-0 z-10 hidden flex-col items-center justify-center gap-3 bg-slate-100 px-6 text-center text-slate-600">
                <i class="fas fa-triangle-exclamation text-2xl text-amber-500" aria-hidden="true"></i>
                <p class="font-cairo text-sm font-bold">تعذّرت معاينة الملف داخل المتصفح.</p>
                <a id="pdfPreviewErrorDownload" href="#" download data-pdf-preview-download class="font-cairo text-sm font-bold text-navy underline">حفظ الملف لفتحه</a>
            </div>
            <iframe id="pdfPreviewFrame" class="h-full w-full border-0" title="معاينة ملف PDF" src="about:blank"></iframe>
        </div>
    </section>
</div>

<script>
    (function () {
        const dialog = document.getElementById('pdfPreviewDialog');
        const frame = document.getElementById('pdfPreviewFrame');
        const loading = document.getElementById('pdfPreviewLoading');
        const error = document.getElementById('pdfPreviewError');
        const download = document.getElementById('pdfPreviewDownload');
        const errorDownload = document.getElementById('pdfPreviewErrorDownload');
        let trigger = null;

        function isPdfExport(link) {
            // Do not intercept the download actions inside the preview dialog.
            if (link.dataset.pdfPreviewDownload !== undefined) return false;

            if (link.dataset.pdfPreview !== undefined) return true;
            try {
                const url = new URL(link.href, window.location.href);
                return url.origin === window.location.origin && (
                    /\bexport\b/i.test(url.pathname) ||
                    url.searchParams.get('format') === 'pdf' ||
                    url.searchParams.get('export') === 'pdf'
                );
            } catch (exception) {
                return false;
            }
        }

        function setLoading(isLoading) {
            loading.classList.toggle('hidden', !isLoading);
            error.classList.add('hidden');
            error.classList.remove('flex');
        }

        function openPreview(link) {
            trigger = link;
            const url = link.href;
            download.href = url;
            errorDownload.href = url;
            setLoading(true);
            dialog.classList.remove('hidden');
            dialog.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            frame.src = url;
            dialog.querySelector('[data-pdf-preview-close]').focus();
        }

        function closePreview() {
            if (dialog.classList.contains('hidden')) return;
            dialog.classList.add('hidden');
            dialog.setAttribute('aria-hidden', 'true');
            frame.src = 'about:blank';
            document.body.classList.remove('overflow-hidden');
            if (trigger) trigger.focus();
        }

        document.querySelectorAll('a[href]').forEach(function (link) {
            if (isPdfExport(link)) link.removeAttribute('target');
        });

        document.addEventListener('click', function (event) {
            const link = event.target.closest('a[href]');
            if (!link || !isPdfExport(link) || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            openPreview(link);
        });

        dialog.addEventListener('click', function (event) {
            if (event.target.closest('[data-pdf-preview-close]')) closePreview();
        });

        frame.addEventListener('load', function () {
            if (frame.src !== 'about:blank') loading.classList.add('hidden');
        });

        frame.addEventListener('error', function () {
            loading.classList.add('hidden');
            error.classList.remove('hidden');
            error.classList.add('flex');
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !dialog.classList.contains('hidden')) closePreview();
        });
    })();
</script>
