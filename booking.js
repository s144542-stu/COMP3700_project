/**
 * Enhanced JavaScript Validation for Booking Form
 * COMP3700 - Part 4 - Form Validation Requirements
 */

// Validation Function 1: Booking Form Validation
function validateBookingForm(event) {
    event.preventDefault();
    
    let isValid = true;
    let errorMessages = [];
    
    // Clear previous errors
    clearErrors();
    
    // 1. Full Name Validation (Required + Pattern)
    const fullName = document.getElementById('fullName').value.trim();
    const namePattern = /^[A-Za-z]{2,}\s[A-Za-z]{2,}$/;
    if (fullName === '') {
        showError('fullName', 'Full name is required');
        errorMessages.push('Full name is required');
        isValid = false;
    } else if (!namePattern.test(fullName)) {
        showError('fullName', 'Please enter valid first and last name (letters only)');
        errorMessages.push('Invalid name format');
        isValid = false;
    }
    
    // 2. Email Validation (Required + Advanced Pattern)
    const email = document.getElementById('email').value.trim();
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.(com|org|edu|net|om)$/;
    if (email === '') {
        showError('email', 'Email address is required');
        errorMessages.push('Email is required');
        isValid = false;
    } else if (!emailPattern.test(email)) {
        showError('email', 'Please enter a valid email address');
        errorMessages.push('Invalid email format');
        isValid = false;
    }
    
    // 3. Phone Number Validation (Pattern - Oman format)
    const phone = document.getElementById('phone').value.trim();
    const phonePattern = /^\+968-\d{8}$/;
    if (phone !== '' && !phonePattern.test(phone)) {
        showError('phone', 'Phone format must be +968-XXXXXXXX');
        errorMessages.push('Invalid phone format');
        isValid = false;
    }
    
    // 4. Booking Type Validation (Required - Radio button)
    const bookingType = document.querySelector('input[name="bookingType"]:checked');
    if (!bookingType) {
        alert('Please select a booking type (Hotel or Event)');
        errorMessages.push('Booking type is required');
        isValid = false;
    }
    
    // 5. Hotel/Event Selection Validation (Required)
    const itemName = document.getElementById('itemName').value;
    if (itemName === '') {
        showError('itemName', 'Please select a hotel or event');
        errorMessages.push('Hotel/Event selection is required');
        isValid = false;
    }
    
    // 6. Date Validation (Required + Logical)
    const checkIn = document.getElementById('checkIn').value;
    const checkOut = document.getElementById('checkOut').value;
    const eventDate = document.getElementById('eventDate').value;
    
    if (bookingType && bookingType.value === 'hotel') {
        if (checkIn === '' || checkOut === '') {
            showError('checkIn', 'Check-in and check-out dates are required');
            errorMessages.push('Hotel dates are required');
            isValid = false;
        } else {
            // Logical validation: Check-out must be after check-in
            const checkInDate = new Date(checkIn);
            const checkOutDate = new Date(checkOut);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (checkInDate < today) {
                showError('checkIn', 'Check-in date cannot be in the past');
                errorMessages.push('Invalid check-in date');
                isValid = false;
            }
            if (checkOutDate <= checkInDate) {
                showError('checkOut', 'Check-out must be after check-in');
                errorMessages.push('Invalid check-out date');
                isValid = false;
            }
        }
    } else if (bookingType && bookingType.value === 'event') {
        if (eventDate === '') {
            showError('eventDate', 'Event date is required');
            errorMessages.push('Event date is required');
            isValid = false;
        } else {
            const evtDate = new Date(eventDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (evtDate < today) {
                showError('eventDate', 'Event date cannot be in the past');
                errorMessages.push('Invalid event date');
                isValid = false;
            }
        }
    }
    
    // 7. Number of Guests Validation (Required + Range)
    const guests = document.getElementById('guests').value;
    if (guests === '' || guests < 1) {
        showError('guests', 'Number of guests must be at least 1');
        errorMessages.push('Invalid number of guests');
        isValid = false;
    } else if (guests > 20) {
        showError('guests', 'Maximum 20 guests allowed per booking');
        errorMessages.push('Too many guests');
        isValid = false;
    }
    
    // Display summary of errors or submit
    if (!isValid) {
        const errorSummary = document.createElement('div');
        errorSummary.className = 'alert alert-danger mt-3';
        errorSummary.innerHTML = '<strong>Please correct the following errors:</strong><ul>' + 
            errorMessages.map(msg => `<li>${msg}</li>`).join('') + '</ul>';
        
        const form = document.querySelector('form');
        form.insertBefore(errorSummary, form.firstChild);
        
        // Scroll to top to show errors
        window.scrollTo(0, 0);
        
        return false;
    }
    
    // If all validations pass, change action to PHP script
    document.querySelector('form').action = 'processBooking.php';
    document.querySelector('form').method = 'post';
    return true;
}

// Helper function to show error message
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    field.classList.add('is-invalid');
    
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback d-block';
    errorDiv.textContent = message;
    
    // Insert error message after field
    field.parentNode.appendChild(errorDiv);
}

// Helper function to clear all error messages
function clearErrors() {
    // Remove all error classes
    document.querySelectorAll('.is-invalid').forEach(field => {
        field.classList.remove('is-invalid');
    });
    
    // Remove all error messages
    document.querySelectorAll('.invalid-feedback').forEach(msg => {
        msg.remove();
    });
    
    // Remove error summary
    document.querySelectorAll('.alert-danger').forEach(alert => {
        alert.remove();
    });
}

// Attach validation to form on page load
window.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.querySelector('form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', validateBookingForm);
        
        // Real-time validation on blur
        document.getElementById('fullName').addEventListener('blur', function() {
            const namePattern = /^[A-Za-z]{2,}\s[A-Za-z]{2,}$/;
            if (this.value.trim() !== '' && !namePattern.test(this.value.trim())) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        document.getElementById('email').addEventListener('blur', function() {
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.(com|org|edu|net|om)$/;
            if (this.value.trim() !== '' && !emailPattern.test(this.value.trim())) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }
});