<?php include __DIR__."/../partials/sidebar.php"; ?>

<link rel="stylesheet" href="/assets/styles/pages/edit-profile.css">
<style>
    .btn-save {
    background: #2563eb;
    color: white;
}
.btn-save:hover {
    background: #2059d4ff;
}
</style>
<?php include __DIR__."/../partials/edit-profile.php"; ?>

</div>
         <div id="custom-alert" class="alert-box"></div>

    <script src="/assets/scripts/sidebar.js"></script>

<script >
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('editProfileForm');

        // Fetch user details
        fetch('/admin/get-profile')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const user = res.data;
                    console.log(user);
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
            

            fetch('/admin/profile-update', {
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

 </script>
</body>
</html>


