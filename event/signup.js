function validatePasswords() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const confirmPasswordInfo = document.getElementById('confirm-password-info');
    
 
    if (password !== confirmPassword) {
        confirmPasswordInfo.textContent = 'Passwords do not match.';
        return false; 
    } else {
        confirmPasswordInfo.textContent = '';
        return true;
    }
}