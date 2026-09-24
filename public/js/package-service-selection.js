// Customizable Package Service Selection Modal Logic
// Add this code after the fetchAndAddPackageServices function in pos/index.blade.php

let pendingPackageData = null;

function showPackageServiceModal(packageId, packageName, serviceLimit, staffId, qty, price, disc) {
    // Store package data for later use
    pendingPackageData = {
        id: packageId,
        name: packageName,
        serviceLimit: serviceLimit,
        staffId: staffId,
        quantity: qty,
        price: price,
        discount: disc
    };

    // Update modal title and limit text
    $('#packageModalName').text(packageName);
    $('#packageLimitText').text(`Select up to ${serviceLimit} services from this package`);
    $('#selectedCount').text(`0 / ${serviceLimit}`);

    // Fetch package services
    $.ajax({
        url: "{{ route('admin.pos.package.services', ':id') }}".replace(':id', packageId),
        method: 'GET',
        success: function (response) {
            if (response.services && response.services.length > 0) {
                renderPackageServices(response.services, serviceLimit);
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('packageServiceModal'));
                modal.show();
            } else {
                showAlert('No services found in this package', 'warning');
            }
        },
        error: function (xhr) {
            console.error('Error loading package services:', xhr);
            showAlert('Error loading package services', 'danger');
        }
    });
}

function renderPackageServices(services, serviceLimit) {
    const container = $('#packageServicesContainer');
    container.empty();

    services.forEach(service => {
        const serviceHtml = `
            <div class="service-item p-3 mb-2 border rounded" data-service-id="${service.id}">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${service.name}</h6>
                        <small class="text-muted">Price: ${formatCurrency(service.price)}</small>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input service-use-now" type="checkbox" 
                                id="use_now_${service.id}" data-service-id="${service.id}">
                            <label class="form-check-label" for="use_now_${service.id}">
                                Use Now
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input service-save-later" type="checkbox" 
                                id="save_later_${service.id}" data-service-id="${service.id}">
                            <label class="form-check-label" for="save_later_${service.id}">
                                Save for Later
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.append(serviceHtml);
    });

    // Add event listeners for checkboxes
    $('.service-use-now, .service-save-later').on('change', function () {
        updateServiceSelection(serviceLimit);
    });
}

function updateServiceSelection(serviceLimit) {
    let selectedCount = 0;

    // Count selected services (either use now or save later)
    $('.service-item').each(function () {
        const serviceId = $(this).data('service-id');
        const useNow = $(`#use_now_${serviceId}`).is(':checked');
        const saveLater = $(`#save_later_${serviceId}`).is(':checked');

        if (useNow || saveLater) {
            selectedCount++;
        }
    });

    // Update counter
    $('#selectedCount').text(`${selectedCount} / ${serviceLimit}`);

    // Disable unchecked services if limit reached
    if (selectedCount >= serviceLimit) {
        $('.service-item').each(function () {
            const serviceId = $(this).data('service-id');
            const useNow = $(`#use_now_${serviceId}`).is(':checked');
            const saveLater = $(`#save_later_${serviceId}`).is(':checked');

            if (!useNow && !saveLater) {
                $(`#use_now_${serviceId}`).prop('disabled', true);
                $(`#save_later_${serviceId}`).prop('disabled', true);
                $(this).addClass('opacity-50');
            }
        });
    } else {
        // Re-enable all checkboxes
        $('.service-use-now, .service-save-later').prop('disabled', false);
        $('.service-item').removeClass('opacity-50');
    }
}

// Confirm package service selection
$('#confirmPackageServicesBtn').on('click', function () {
    if (!pendingPackageData) return;

    const selectedServices = [];
    let selectedCount = 0;

    $('.service-item').each(function () {
        const serviceId = $(this).data('service-id');
        const useNow = $(`#use_now_${serviceId}`).is(':checked');
        const saveLater = $(`#save_later_${serviceId}`).is(':checked');

        if (useNow || saveLater) {
            selectedCount++;
            selectedServices.push({
                service_id: serviceId,
                save_future: saveLater ? 1 : 0
            });
        }
    });

    // Validate selection
    if (selectedCount === 0) {
        showAlert('Please select at least one service', 'warning');
        return;
    }

    if (selectedCount > pendingPackageData.serviceLimit) {
        showAlert(`You can only select up to ${pendingPackageData.serviceLimit} services`, 'warning');
        return;
    }

    // Add package to cart with selected services
    const packageItem = {
        type: 'package',
        id: pendingPackageData.id,
        name: pendingPackageData.name,
        price: pendingPackageData.price,
        quantity: pendingPackageData.quantity,
        staff_id: pendingPackageData.staffId || null,
        discount_amount: pendingPackageData.discount,
        notes: `Customizable (${selectedCount}/${pendingPackageData.serviceLimit} services)`,
        services: selectedServices  // Include selected services
    };
    addToCart(packageItem);

    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('packageServiceModal'));
    modal.hide();

    // Reset form
    resetPackageForm();
    pendingPackageData = null;

    showAlert('Customizable package added successfully', 'success');
});
