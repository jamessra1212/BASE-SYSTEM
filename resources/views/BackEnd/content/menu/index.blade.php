@extends('BackEnd.layouts.master')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card dt-modern-card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">

            <div class="card-header bg-white pt-4 pb-3 px-4 d-flex align-items-center justify-content-between w-100"
                style="border-bottom: 1px solid #f1f5f9;">

                <h5 class="m-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                    <i class="fas fa-th"></i>MANAGE MENU
                </h5>

                <button id="btn_add" class="btn btn-sm px-3 ms-auto text-white fw-medium shadow-sm"
                        style="background-color: #10b981; border: 1px solid #10b981; font-size: 0.85rem; padding: 0.45rem 1.1rem; border-radius: 6px; white-space: nowrap;"
                        onmouseover="this.style.backgroundColor='#059669'"
                        onmouseout="this.style.backgroundColor='#10b981'">
                    <i class="fas fa-plus me-1"></i>Create Menu
                </button>
            </div>

            <div class="card-body px-4 pb-4 pt-3 overflow-hidden">
                <div class="table-responsive">
                    {{ $dataTable->table(['class' => 'table align-middle border-0 w-100 mb-0']) }}
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Changed this from a raw class to an explicit ID container for your AJAX script target -->
    <div id="modal-body"></div>
</div>
@endsection

@push('script')

    {{ $dataTable->scripts() }}

    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        let isModalOpen = false;

        $('#btn_add').on('click', function(e) {
            e.preventDefault();

            if (isModalOpen) return;

            isModalOpen = true;
            let modalId = 'MENU_ENTRY_MODAL';

            $.ajax({
                url: '{{ route("sida.menu.create") }}',
                type: 'GET',
                success: function (data) {
                    $('#modal-body').html(data);

                    const modal = new bootstrap.Modal(document.getElementById(modalId));
                    modal.show();

                    $('#' + modalId).on('hidden.bs.modal', function () {
                        isModalOpen = false;
                        $('#modal-body').html('');
                    });
                },
                error: function(xhr) {
                    isModalOpen = false;
                    toastr.error('Failed to load the application form.');
                }
            });
        });

        $(document).on('submit', '#form_menu', function(e) {
            e.preventDefault();

            let $form = $(this);
            let formData = new FormData(this);
            let $submitBtn = $('#btn_save_menu');

            // Reset error UI states
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').text('');
            $('#modal_error_summary').addClass('d-none');

            $.ajax({
                url: "{{ route('sida.menu.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1.5"></i> Saving...');
                },
                success: function(response) {
                    toastr.success(response.message || 'Menu created successfully!');

                    // Hide Modal
                    const modalEl = document.getElementById('MENU_ENTRY_MODAL');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }

                    // Reset form controls
                    $form[0].reset();

                    // Refresh DataTable
                    if (window.LaravelDataTables && window.LaravelDataTables["tblMenu"]) {
                        window.LaravelDataTables["tblMenu"].ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        $('#modal_error_summary').removeClass('d-none');

                        $.each(errors, function(key, messages) {
                            let input = $form.find(`[name="${key}"]`);
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(messages[0]);
                        });
                    } else {
                        toastr.error('An unexpected error occurred while saving the menu.');
                        console.error(xhr.responseText);
                    }
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1.5"></i> Save Menu');
                }
            });
        });
    });

    </script>

@endpush
