<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Helper function to get salon slug from URL
    function getSalonSlug() {
        // Priority 1: Get from authenticated user's salon (most reliable)
        const userSalonSlug = '<?php echo e(auth()->user()->salon->slug ?? ""); ?>';
        if (userSalonSlug) return userSalonSlug;

        // Priority 2: Get from route parameter
        const bladeSlug = '<?php echo e(request()->route("salon_slug") ?? ""); ?>';
        if (bladeSlug) return bladeSlug;

        const pathParts = window.location.pathname.split('/').filter(p => p);
        return pathParts[0] || '';
    }

    // Function to check and accept appointment
    function checkAndAcceptAppointment(appointmentId) {
        const salonSlug = getSalonSlug();
        if (!salonSlug) {
            Swal.fire({
                title: 'Error',
                text: 'Could not determine salon URL. Please refresh the page.',
                icon: 'error'
            });
            return;
        }
        fetch(`/${salonSlug}/employee/appointments/${appointmentId}/check-slot`)
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    Swal.fire({
                        title: 'Accept Appointment?',
                        text: 'Are you sure you want to accept this appointment?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, accept it',
                        cancelButtonText: 'No, cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            acceptAppointment(appointmentId);
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Not Available',
                        text: data.message,
                        icon: 'warning'
                    });
                }
            })
            .catch(error => {

                Swal.fire({
                    title: 'Error',
                    text: 'Failed to check slot availability',
                    icon: 'error'
                });
            });
    }

    // Function to accept appointment
    function acceptAppointment(appointmentId) {
        const salonSlug = getSalonSlug();
        if (!salonSlug) {
            Swal.fire({
                title: 'Error',
                text: 'Could not determine salon URL. Please refresh the page.',
                icon: 'error'
            });
            return;
        }
        fetch(`/${salonSlug}/employee/appointments/${appointmentId}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'Failed to accept appointment',
                    icon: 'error'
                });
            });
    }

    // Function to reject appointment
    function rejectAppointment(appointmentId) {
        Swal.fire({
            title: 'Reject Appointment?',
            text: 'Are you sure you want to reject this appointment?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, reject it',
            cancelButtonText: 'No, cancel',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                const salonSlug = getSalonSlug();
                if (!salonSlug) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Could not determine salon URL. Please refresh the page.',
                        icon: 'error'
                    });
                    return;
                }
                fetch(`/${salonSlug}/employee/appointments/${appointmentId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Rejected!',
                                text: data.message,
                                icon: 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: error.message || 'Failed to reject appointment',
                            icon: 'error'
                        });
                    });
            }
        });
    }

    // Function to complete appointment
    function completeAppointment(appointmentId) {
        const salonSlug = getSalonSlug();
        if (!salonSlug) {
            Swal.fire({
                title: 'Error',
                text: 'Could not determine salon URL. Please refresh the page.',
                icon: 'error'
            });
            return;
        }

        // First, try to complete the appointment (which will tell us if payment is required)
        fetch(`/${salonSlug}/employee/appointments/${appointmentId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(async response => {
                const data = await response.json();
                if (!response.ok && !data.requires_payment) {
                    throw new Error(data.message || 'Failed to complete appointment');
                }
                text: error.message || 'Failed to complete appointment',
                    icon: 'error'
            });
    });
    }
</script><?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\employee\dashboard-scripts.blade.php ENDPATH**/ ?>