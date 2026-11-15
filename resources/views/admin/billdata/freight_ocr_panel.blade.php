<div class="card card-outline card-info mt-3" id="freight-ocr-panel">
    <div class="card-header">
        <strong>Asynchronous Invoice OCR Validation</strong>
    </div>
    <div class="card-body">
        <button type="button" class="btn btn-info" id="startFreightOcr">Validate Selected Invoices with OCR</button>
        <button type="button" class="btn btn-outline-warning" id="retryFreightOcr" style="display:none">Retry Failed OCR</button>
        <div class="progress mt-3" style="height:22px">
            <div class="progress-bar" id="freightOcrProgress" style="width:0%">0%</div>
        </div>
        <div class="mt-2" id="freightOcrTotals">No batch started</div>
    </div>
</div>
<script>
(function () {
    'use strict';

    function initializeFreightOcr() {
        if (typeof window.Swal === 'undefined') {
            console.error('SweetAlert2 is not loaded. Check the SweetAlert2 script in the parent Blade.');
            return;
        }

        const Swal = window.Swal;
        const csrf = @json(csrf_token());
        const startUrl = @json(route('admin.freight-ocr.start'));
        const statusTemplate = @json(route('admin.freight-ocr.status', ['batch' => '__BATCH__']));
        const retryTemplate = @json(route('admin.freight-ocr.retry', ['batch' => '__BATCH__']));
        const startButton = document.getElementById('startFreightOcr');
        const retryButton = document.getElementById('retryFreightOcr');
        const form = document.getElementById('freightValidationForm');
        const progressBar = document.getElementById('freightOcrProgress');
        const totals = document.getElementById('freightOcrTotals');

        if (!startButton || !retryButton || !form || !progressBar || !totals) {
            console.error('Freight OCR panel elements are missing.');
            return;
        }

        let batch = null;
        let timer = null;
        let completedNotice = false;
        let busy = false;
        const seen = new Map();
        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2200,
            timerProgressBar: true
        });

        function selected() {
            return Array.from(document.querySelectorAll('#table tbody tr[data-id]'))
                .filter(row => row.querySelector('.validate-checkbox')?.checked)
                .map(row => Number(row.dataset.id));
        }

        function icon(status) {
            if (status === 'valid') return 'success';
            if (status === 'invalid') return 'error';
            if (status === 'review_required') return 'warning';
            return 'info';
        }

        function message(value) {
            return Array.isArray(value) ? value.join('; ') : String(value || '');
        }

        function render(item) {
            const row = document.querySelector('#table tbody tr[data-id="' + item.billdata_id + '"]');
            if (!row) return;

            const labels = {
                valid: 'Valid',
                invalid: 'Invalid',
                review_required: 'Review Required',
                failed: 'OCR Failed',
                queued: 'Queued',
                processing: 'Processing'
            };
            const label = labels[item.status] || item.status;
            const statusCell = row.querySelector('.status');
            const remarkInput = row.querySelector('.remark');
            const submitCheckbox = row.querySelector('.submit-checkbox');
            const returnCheckbox = row.querySelector('.return-checkbox');

            if (statusCell) {
                statusCell.textContent = label;
                statusCell.className = 'status ' + (
                    item.status === 'valid' ? 'text-success' :
                    item.status === 'invalid' ? 'text-danger' : 'text-warning'
                );
            }

            if (remarkInput && item.remarks) {
                remarkInput.value = message(item.remarks);
            }

            if (submitCheckbox) {
                submitCheckbox.checked = item.status === 'valid';
                submitCheckbox.disabled = item.status !== 'valid';
            }

            if (returnCheckbox) {
                returnCheckbox.checked = item.status === 'invalid';
                returnCheckbox.disabled = !['valid', 'invalid'].includes(item.status);
            }

            row.classList.toggle('table-danger', item.status === 'invalid');
            row.classList.toggle('table-success', item.status === 'valid');
            row.classList.toggle('table-warning', item.status === 'review_required');

            const previousStatus = seen.get(item.id);
            if (previousStatus !== item.status) {
                seen.set(item.id, item.status);

                if (['valid', 'invalid', 'review_required', 'failed'].includes(item.status)) {
                    const lrNumber = row.querySelector('.sticky-col-5')?.textContent?.trim() || item.billdata_id;
                    toast.fire({
                        icon: icon(item.status),
                        title: 'LR ' + lrNumber + ': ' + label
                    });
                }
            }

            row.dataset.ocrStatus = item.status;
        }

        async function poll() {
            if (!batch || busy) return;
            busy = true;

            try {
                const response = await fetch(statusTemplate.replace('__BATCH__', batch), {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) throw new Error('Status request failed');

                const data = await response.json();
                (data.results || []).forEach(render);

                const counts = data.counts || {};
                const percent = data.total ? Math.round(100 * data.completed / data.total) : 0;
                progressBar.style.width = percent + '%';
                progressBar.textContent = percent + '%';
                totals.textContent =
                    'Processed ' + data.completed + '/' + data.total +
                    ' | Valid ' + (counts.valid || 0) +
                    ' | Invalid ' + (counts.invalid || 0) +
                    ' | Review ' + (counts.review_required || 0) +
                    ' | Failed ' + (counts.failed || 0);

                if (data.finished) {
                    clearInterval(timer);
                    timer = null;
                    retryButton.style.display = (counts.failed || 0) > 0 ? 'inline-block' : 'none';

                    if (!completedNotice) {
                        completedNotice = true;
                        Swal.fire({
                            icon: (counts.invalid || counts.review_required || counts.failed) ? 'warning' : 'success',
                            title: 'OCR Validation Completed',
                            text: totals.textContent
                        });
                    }
                }
            } catch (error) {
                console.error(error);
                clearInterval(timer);
                timer = null;
                Swal.fire('Status update failed', error.message, 'error');
            } finally {
                busy = false;
            }
        }

        startButton.addEventListener('click', async function () {
            const ids = selected();

            if (!ids.length) {
                Swal.fire('No records selected', 'Select rows using Validate checkboxes', 'warning');
                return;
            }

            if (ids.length > 1000) {
                Swal.fire('Limit exceeded', 'Select at most 1,000 rows', 'warning');
                return;
            }

            this.disabled = true;

            try {
                const response = await fetch(startUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ ids: ids })
                });
                const data = await response.json();

                if (!response.ok) throw new Error(data.message || 'Cannot start batch');

                batch = data.batch_id;
                seen.clear();
                completedNotice = false;
                clearInterval(timer);
                timer = setInterval(poll, 3000);
                poll();
                toast.fire({ icon: 'success', title: 'OCR batch queued: ' + data.total + ' records' });
            } catch (error) {
                Swal.fire('Unable to start OCR', error.message, 'error');
            } finally {
                this.disabled = false;
            }
        });

        retryButton.addEventListener('click', async function () {
            if (!batch) return;

            try {
                const response = await fetch(retryTemplate.replace('__BATCH__', batch), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Retry request failed');

                completedNotice = false;
                this.style.display = 'none';
                clearInterval(timer);
                timer = setInterval(poll, 3000);
                poll();
            } catch (error) {
                Swal.fire('Retry failed', error.message, 'error');
            }
        });

        form.addEventListener('submit', function (event) {
            const pending = Array.from(document.querySelectorAll('#table tbody tr[data-ocr-status]'))
                .some(row => row.querySelector('.validate-checkbox')?.checked &&
                    !['valid', 'invalid'].includes(row.dataset.ocrStatus));

            if (pending) {
                event.preventDefault();
                Swal.fire(
                    'OCR validation incomplete',
                    'Wait for selected records to finish OCR validation',
                    'warning'
                );
            }
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeFreightOcr);
    } else {
        initializeFreightOcr();
    }
})();
</script>
