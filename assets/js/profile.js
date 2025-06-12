/* Content for assets/js/profile.js */

document.addEventListener('DOMContentLoaded', () => {
    const profileUsername = document.getElementById('profile-username');
    const profileBio = document.getElementById('profile-bio');
    const profilePicture = document.getElementById('profile-picture');
    const editProfileButton = document.getElementById('edit-profile-button');
    const saveProfileButton = document.getElementById('save-profile-button');
    const profileForm = document.getElementById('profile-form');
    const bioInput = document.getElementById('bio');
    const profilePictureInput = document.getElementById('profile_picture');


    const userId = localStorage.getItem('user_id');

    if (!userId) {
        // Redirect to login if user is not logged in
        window.location.href = 'login.html';
    }

    // Function to fetch and display user profile
    async function fetchUserProfile() {
        try {
            const response = await fetch(`api/get_user.php?id=${userId}`);
            const data = await response.json();

            if (data.success) {
                profileUsername.textContent = data.user.username;
                profileBio.textContent = data.user.bio || 'No bio available.';
                if (data.user.profile_picture) {
                    profilePicture.src = data.user.profile_picture;
                } else {
                    profilePicture.src = 'assets/images/default_profile.png'; // Use a default image
                }

                // Set form values for editing
                bioInput.value = data.user.bio || '';
            } else {
                console.error('Error fetching profile:', data.message);
                // Handle error (e.g., display an error message)
            }
        } catch (error) {
            console.error('Error fetching profile:', error);
            // Handle error (e.g., display an error message)
        }
    }

    // Function to handle profile editing
    function enableEditMode() {
        profileBio.style.display = 'none';
        profileForm.style.display = 'block';
        editProfileButton.style.display = 'none';
        saveProfileButton.style.display = 'block';
    }

    // Function to handle saving profile changes
    async function saveProfileChanges() {
        const formData = new FormData(profileForm);
        formData.append('user_id', userId);

        try {
            const response = await fetch('api/update_user.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Update displayed profile information
                profileBio.textContent = bioInput.value || 'No bio available.';
                if (profilePictureInput.files.length > 0) {
                    profilePicture.src = URL.createObjectURL(profilePictureInput.files[0]);
                }
                // Revert to view mode
                profileBio.style.display = 'block';
                profileForm.style.display = 'none';
                editProfileButton.style.display = 'block';
                saveProfileButton.style.display = 'none';

            } else {
                console.error('Error updating profile:', data.message);
                // Handle error (e.g., display an error message)
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            // Handle error (e.g., display an error message)
        }
    }


    // Event listeners
    editProfileButton.addEventListener('click', enableEditMode);
    saveProfileButton.addEventListener('click', saveProfileChanges);

    // Initial profile fetch
    fetchUserProfile();
});