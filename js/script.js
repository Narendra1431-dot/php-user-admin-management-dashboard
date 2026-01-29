// Task 3: JavaScript Functions for User Management

// Show Alert Message
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => alertDiv.remove(), 5000);
    }
}

// Confirm Delete
function confirmDelete(user_id, username) {
    if (confirm(`Are you sure you want to delete user "${username}"?`)) {
        window.location.href = `php/delete_user.php?id=${user_id}`;
    }
}

// Validate Form
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('input[required], select[required]');
    for (let input of inputs) {
        if (!input.value.trim()) {
            showAlert(`${input.name} is required`, 'warning');
            return false;
        }
    }
    return true;
}

// Toggle Password Visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
    }
}

// Search Users (AJAX)
function searchUsers(searchTerm) {
    if (searchTerm.length < 2) {
        location.reload();
        return;
    }
    
    fetch(`php/search_users.php?q=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('tbody');
            if (tbody) {
                tbody.innerHTML = '';
                
                if (data.users.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="100%">No users found</td></tr>';
                    return;
                }
                
                data.users.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                        <td>${user.first_name} ${user.last_name}</td>
                        <td>${user.role_name}</td>
                        <td>
                            <a href="profile.php?id=${user.user_id}" class="btn btn-sm btn-primary">View</a>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(${user.user_id}, '${user.username}')">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        })
        .catch(error => showAlert('Search failed', 'danger'));
}

// Upload File with Validation
function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
    if (file.size > maxSize) {
        showAlert('File size exceeds 5MB limit', 'danger');
        event.target.value = '';
        return false;
    }
    
    if (!allowedTypes.includes(file.type)) {
        showAlert('Only JPG, PNG, and GIF files are allowed', 'danger');
        event.target.value = '';
        return false;
    }
    
    return true;
}

// Format Date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

// Debounce Function for Search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle Function
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Ready Event
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded successfully');
});
