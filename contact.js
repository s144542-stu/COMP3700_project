/**
 * Enhanced JavaScript Validation for Contact Form
 * COMP3700 - Part 4 - Form Validation Requirements
 */

// Validation Function 2: Contact Form Validation
function validateContactForm(event) {
    event.preventDefault();
    
    let isValid = true;
    let errorMessages = [];
    
    // Clear previous errors
    clearContactErrors();
    
    // 1. Name Validation (Required + Length + Pattern)
    const name = document.getElementById('name').value.trim();
    const namePattern = /^[A-Za-z\s]{3,50}$/;
    if (name === '') {
        showContactError('name', 'Full name is required');
        errorMessages.push('Name is required');
        isValid = false;
    } else if (name.length < 3) {
        showContactError('name', 'Name must be at least 3 characters');
        errorMessages.push('Name too short');
        isValid = false;
    } else if (name.length > 50) {
        showContactError('name', 'Name must not exceed 50 characters');
        errorMessages.push('Name too long');
        isValid = false;
    } else if (!namePattern.test(name)) {
        showContactError('name', 'Name can only contain letters and spaces');
        errorMessages.push('Invalid name characters');
        isValid = false;
    }
    
    // 2. Email Validation (Required + Advanced Pattern)
    const email = document.getElementById('email').value.trim();
    const emailPattern = /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (email === '') {
        showContactError('email', 'Email address is required');
        errorMessages.push('Email is required');
        isValid = false;
    } else if (!emailPattern.test(email)) {
        showContactError('email', 'Please enter a valid email address');
        errorMessages.push('Invalid email format');
        isValid = false;
    } else if (email.length > 100) {
        showContactError('email', 'Email must not exceed 100 characters');
        errorMessages.push('Email too long');
        isValid = false;
    }
    
    // 3. Subject Validation (Required + Length)
    const subject = document.getElementById('subject').value.trim();
    if (subject === '') {
        showContactError('subject', 'Subject is required');
        errorMessages.push('Subject is required');
        isValid = false;
    } else if (subject.length < 5) {
        showContactError('subject', 'Subject must be at least 5 characters');
        errorMessages.push('Subject too short');
        isValid = false;
    } else if (subject.length > 100) {
        showContactError('subject', 'Subject must not exceed 100 characters');
        errorMessages.push('Subject too long');
        isValid = false;
    }
    
    // 4. Reason Validation (Required - Dropdown)
    const reason = document.getElementById('reason').value;
    if (reason === '') {
        showContactError('reason', 'Please select a reason for contact');
        errorMessages.push('Reason is required');
        isValid = false;
    }
    
    // 5. Message Validation (Required + Length)
    const message = document.getElementById('message').value.trim();
    if (message === '') {
        showContactError('message', 'Message is required');
        errorMessages.push('Message is required');
        isValid = false;
    } else if (message.length < 20) {
        showContactError('message', 'Message must be at least 20 characters');
        errorMessages.push('Message too short');
        isValid = false;
    } else if (message.length > 1000) {
        showContactError('message', 'Message must not exceed 1000 characters');
        errorMessages.push('Message too long');
        isValid = false;
    }
    
    // 6. Terms Agreement Validation (Required - Checkbox)
    const agreeTerms = document.getElementById('agreeTerms');
    if (!agreeTerms.checked) {
        showContactError('agreeTerms', 'You must agree to the terms and conditions');
        errorMessages.push('Terms agreement required');
        isValid = false;
    }
    
    // 7. Email Domain Validation (Logical)
    if (email !== '') {
        const domain = email.split('@')[1];
        const validDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'squ.edu.om', 'student.squ.edu.om'];
        if (domain && !validDomains.some(valid => domain.toLowerCase().includes(valid))) {
            showContactError('email', 'Please use a common email provider (Gmail, Yahoo, Outlook, Hotmail, or SQU)');
            errorMessages.push('Invalid email domain');
            isValid = false;
        }
    }
    
    // 8. Special Characters Validation in Subject
    const specialCharsPattern = /^[a-zA-Z0-9\s\-.,!?]+$/;
    if (subject !== '' && !specialCharsPattern.test(subject)) {
        showContactError('subject', 'Subject contains invalid special characters');
        errorMessages.push('Invalid characters in subject');
        isValid = false;
    }
    
    // Display summary of errors or submit
    if (!isValid) {
        const errorSummary = document.createElement('div');
        errorSummary.className = 'alert alert-danger mt-3';
        errorSummary.id = 'error-summary';
        errorSummary.innerHTML = '<strong>⚠️ Form Validation Errors:</strong><ul>' + 
            errorMessages.map(msg => `<li>${msg}</li>`).join('') + '</ul>';
        
        const form = document.querySelector('form');
        const firstElement = form.querySelector('.row');
        firstElement.parentNode.insertBefore(errorSummary, firstElement);
        
        // Scroll to error summary
        errorSummary.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        return false;
    }
    
    // If all validations pass, show success and change action to PHP
    alert('✓ Form validation successful! Submitting to server...');
    document.querySelector('form').action = 'processContact.php';
    document.querySelector('form').method = 'post';
    document.querySelector('form').enctype = 'application/x-www-form-urlencoded';
    
    return true;
}

// Helper function to show error message for contact form
function showContactError(fieldId, message) {
    const field = document.getElementById(fieldId);
    field.classList.add('is-invalid');
    
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback d-block';
    errorDiv.textContent = '❌ ' + message;
    
    // Insert error message after field
    field.parentNode.appendChild(errorDiv);
}

// Helper function to clear all error messages
function clearContactErrors() {
    // Remove all error classes
    document.querySelectorAll('.is-invalid').forEach(field => {
        field.classList.remove('is-invalid');
    });
    
    // Remove all error messages
    document.querySelectorAll('.invalid-feedback').forEach(msg => {
        msg.remove();
    });
    
    // Remove error summary
    const errorSummary = document.getElementById('error-summary');
    if (errorSummary) {
        errorSummary.remove();
    }
}

// Real-time character counter for message field
function updateCharacterCount() {
    const message = document.getElementById('message');
    const maxLength = 1000;
    
    if (message) {
        const counterDiv = document.createElement('div');
        counterDiv.id = 'char-counter';
        counterDiv.className = 'form-text text-muted';
        message.parentNode.appendChild(counterDiv);
        
        message.addEventListener('input', function() {
            const remaining = maxLength - this.value.length;
            const counter = document.getElementById('char-counter');
            counter.textContent = `Characters: ${this.value.length} / ${maxLength} (${remaining} remaining)`;
            
            if (remaining < 100) {
                counter.className = 'form-text text-warning';
            } else if (remaining < 0) {
                counter.className = 'form-text text-danger';
            } else {
                counter.className = 'form-text text-muted';
            }
        });
    }
}

// Attach validation to form on page load
window.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.querySelector('form');
    if (contactForm && document.getElementById('name')) {
        contactForm.addEventListener('submit', validateContactForm);
        
        // Add character counter
        updateCharacterCount();
        
        // Real-time validation on blur for name
        document.getElementById('name').addEventListener('blur', function() {
            const namePattern = /^[A-Za-z\s]{3,50}$/;
            if (this.value.trim() !== '') {
                if (!namePattern.test(this.value.trim())) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            }
        });
        
        // Real-time validation on blur for email
        document.getElementById('email').addEventListener('blur', function() {
            const emailPattern = /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (this.value.trim() !== '') {
                if (!emailPattern.test(this.value.trim())) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            }
        });
        
        // Real-time validation for message length
        document.getElementById('message').addEventListener('input', function() {
            if (this.value.length > 1000) {
                this.classList.add('is-invalid');
            } else if (this.value.length >= 20) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    }
});