/**
 * Employee Dashboard Scripts
 * 
 * Handles appointment management functions for employees
 */

// Function to check and accept appointment
function checkAndAcceptAppointment(appointmentId) {
    // First check if the slot is available
    fetch(`/employee/appointments/${appointmentId}/check-slot`)
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                // If slot is available, confirm with user
                Swal.fire({
                    title: 'Accept Appointment?',
                    text: "You are about to accept this appointment. Do you want to continue?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, accept it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If user confirms, accept the appointment
                        acceptAppointment(appointmentId);
                    }
                });
            } else {
                // If slot is not available, show conflict message
                Swal.fire({
                    title: 'Time Slot Conflict',
                    html: `
                        <p>There's a scheduling conflict with this appointment.</p>
                        <p><strong>Conflicting Appointment:</strong><br>
                        ${data.conflictDetails}</p>
                        <p>Would you still like to accept this appointment?</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, accept anyway',
                    cancelButtonText: 'No, cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If user confirms despite conflict, accept the appointment
                        acceptAppointment(appointmentId);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error checking appointment slot:', error);
            Swal.fire(
                'Error',
                'Failed to check appointment availability. Please try again.',
                'error'
            );
        });
}

// Function to accept appointment
function acceptAppointment(appointmentId) {
    // Send POST request to accept the appointment
    fetch(`/employee/appointments/${appointmentId}/accept`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire(
                'Accepted!',
                'The appointment has been accepted.',
                'success'
            ).then(() => {
                // Reload the page to show updated status
                window.location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to accept appointment');
        }
    })
    .catch(error => {
        console.error('Error accepting appointment:', error);
        Swal.fire(
            'Error',
            'Failed to accept appointment. Please try again.',
            'error'
        );
    });
}

// Function to reject appointment
function rejectAppointment(appointmentId) {
    Swal.fire({
        title: 'Reject Appointment?',
        text: "You are about to reject this appointment. This cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, reject it'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send POST request to reject the appointment
            fetch(`/employee/appointments/${appointmentId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Rejected!',
                        'The appointment has been rejected.',
                        'success'
                    ).then(() => {
                        // Reload the page to show updated status
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to reject appointment');
                }
            })
            .catch(error => {
                console.error('Error rejecting appointment:', error);
                Swal.fire(
                    'Error',
                    'Failed to reject appointment. Please try again.',
                    'error'
                );
            });
        }
    });
}

// Function to complete appointment
function completeAppointment(appointmentId) {
    Swal.fire({
        title: 'Complete Appointment?',
        text: "You are about to mark this appointment as completed.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, complete it'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send POST request to complete the appointment
            fetch(`/employee/appointments/${appointmentId}/complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Completed!',
                        'The appointment has been marked as completed.',
                        'success'
                    ).then(() => {
                        // Reload the page to show updated status
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to complete appointment');
                }
            })
            .catch(error => {
                console.error('Error completing appointment:', error);
                Swal.fire(
                    'Error',
                    'Failed to complete appointment. Please try again.',
                    'error'
                );
            });
        }
    });
}

// Function to view appointment details
function viewAppointmentDetails(appointmentId) {
    // Fetch appointment details
    fetch(`/employee/appointments/${appointmentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const appointment = data.appointment;
                
                // Format date and time
                const startTime = new Date(appointment.start_time);
                const endTime = new Date(appointment.end_time);
                const formattedDate = startTime.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                const formattedStartTime = startTime.toLocaleTimeString('en-US', { 
                    hour: '2-digit', 
                    minute: '2-digit'
                });
                const formattedEndTime = endTime.toLocaleTimeString('en-US', { 
                    hour: '2-digit', 
                    minute: '2-digit'
                });
                
                // Create appointment details HTML
                let detailsHtml = `
                    <div class="appointment-details">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Date & Time</h6>
                                <p class="mb-0 fw-medium">${formattedDate}</p>
                                <p class="mb-0">${formattedStartTime} - ${formattedEndTime}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Status</h6>
                                <span class="badge bg-${getStatusClass(appointment.status)}">${appointment.status}</span>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Service</h6>
                                <p class="mb-0 fw-medium">${appointment.service.name}</p>
                                <p class="mb-0">Duration: ${appointment.service.duration} minutes</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Price</h6>
                                <p class="mb-0 fw-medium">$${parseFloat(appointment.service.price).toFixed(2)}</p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-muted mb-1">Customer</h6>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <div class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                            ${appointment.customer.name.charAt(0)}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-medium">${appointment.customer.name}</p>
                                        <p class="mb-0 small">${appointment.customer.email}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                `;
                
                // Add notes if available
                if (appointment.notes) {
                    detailsHtml += `
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-muted mb-1">Notes</h6>
                                <p class="mb-0">${appointment.notes}</p>
                            </div>
                        </div>
                    `;
                }
                
                // Add action buttons based on status
                detailsHtml += `<div class="d-flex justify-content-end mt-4">`;
                
                if (appointment.status === 'pending') {
                    detailsHtml += `
                        <button class="btn btn-success me-2" onclick="checkAndAcceptAppointment(${appointment.id})">
                            <i class="fas fa-check me-1"></i> Accept
                        </button>
                        <button class="btn btn-danger" onclick="rejectAppointment(${appointment.id})">
                            <i class="fas fa-times me-1"></i> Reject
                        </button>
                    `;
                } else if (appointment.status === 'confirmed') {
                    detailsHtml += `
                        <button class="btn btn-primary" onclick="completeAppointment(${appointment.id})">
                            <i class="fas fa-check-double me-1"></i> Complete
                        </button>
                    `;
                }
                
                detailsHtml += `</div></div>`;
                
                // Show appointment details modal
                Swal.fire({
                    title: 'Appointment Details',
                    html: detailsHtml,
                    width: '600px',
                    showConfirmButton: false,
                    showCloseButton: true
                });
            } else {
                throw new Error(data.message || 'Failed to load appointment details');
            }
        })
        .catch(error => {
            console.error('Error fetching appointment details:', error);
            Swal.fire(
                'Error',
                'Failed to load appointment details. Please try again.',
                'error'
            );
        });
}

// Helper function to get status class for badges
function getStatusClass(status) {
    switch (status.toLowerCase()) {
        case 'pending':
            return 'warning';
        case 'confirmed':
            return 'info';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Initialize employee dashboard components
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any charts or other UI components here
    
    // Set up event listeners for appointment filtering
    const filterButtons = document.querySelectorAll('.appointment-filter-btn');
    if (filterButtons.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Get filter value
                const filter = this.dataset.filter;
                
                // Show/hide appointments based on filter
                const appointments = document.querySelectorAll('.appointment-item');
                appointments.forEach(appointment => {
                    if (filter === 'all' || appointment.dataset.status === filter) {
                        appointment.style.display = 'block';
                    } else {
                        appointment.style.display = 'none';
                    }
                });
            });
        });
    }
});