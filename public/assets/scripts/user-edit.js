 document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('editProfileForm');

        // Fetch user details
        fetch('/user/profile')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const user = res.data;
                    document.getElementById('name').value = user.name;
                    document.getElementById('phone').value = user.phone || '';
                    document.getElementById('gender').value = user.gender || '';
                } else {
                    showAlert('Failed to load user details', 'error');
                }
            })
            .catch(err => showAlert('Error loading details', 'error'));

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = {
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
                gender: document.getElementById('gender').value
            };
            

            fetch('/user/profile/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        showAlert('Profile updated successfully!', 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showAlert(res.message || 'Update failed', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showAlert('An error occurred', 'error');
                });
        });

       
    });