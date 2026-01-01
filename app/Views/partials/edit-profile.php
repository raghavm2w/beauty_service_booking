<div class="edit-profile-container">
    <div class="edit-profile-card">
        <h1>Edit Profile</h1>

        <form id="editProfileForm">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required minlength="3" maxlength="30">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required pattern="[1-9][0-9]{9}"
                    title="10 digit phone number">
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>