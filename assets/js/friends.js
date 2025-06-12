document.addEventListener('DOMContentLoaded', () => {
  const friendsListContainer = document.getElementById('friendsList');
  const friendRequestsContainer = document.getElementById('friendRequests');

  // Function to fetch and display friends list
  const fetchFriends = async () => {
    try {
      const response = await fetch('api/get_friends.php');
      const data = await response.json();

      if (response.ok) {
        if (data.friends && data.friends.length > 0) {
          friendsListContainer.innerHTML = ''; // Clear existing content
          data.friends.forEach(friend => {
            const friendElement = document.createElement('div');
            friendElement.classList.add('friend');
            friendElement.innerHTML = `
              <a href="profile.html?user=${friend.id}">${friend.username}</a>
            `;
            friendsListContainer.appendChild(friendElement);
          });
        } else {
          friendsListContainer.innerHTML = '<p>No friends yet.</p>';
        }
      } else {
        friendsListContainer.innerHTML = `<p>Error fetching friends: ${data.message || 'Unknown error'}</p>`;
      }
    } catch (error) {
      console.error('Error fetching friends:', error);
      friendsListContainer.innerHTML = '<p>Error fetching friends. Please try again.</p>';
    }
  };

  // Function to fetch and display friend requests
  const fetchFriendRequests = async () => {
    try {
      const response = await fetch('api/get_friends.php?status=pending');
      const data = await response.json();

      if (response.ok) {
        if (data.friends && data.friends.length > 0) {
          friendRequestsContainer.innerHTML = ''; // Clear existing content
          data.friends.forEach(request => {
            const requestElement = document.createElement('div');
            requestElement.classList.add('friend-request');
            requestElement.innerHTML = `
              <span>${request.username}</span>
              <button class="accept-request" data-user-id="${request.id}">Accept</button>
              <button class="reject-request" data-user-id="${request.id}">Reject</button>
            `;
            friendRequestsContainer.appendChild(requestElement);
          });

          // Attach event listeners to the accept and reject buttons
          document.querySelectorAll('.accept-request').forEach(button => {
            button.addEventListener('click', async (event) => {
              const userId = event.target.dataset.userId;
              await handleFriendRequest(userId, 'accept');
            });
          });

          document.querySelectorAll('.reject-request').forEach(button => {
            button.addEventListener('click', async (event) => {
              const userId = event.target.dataset.userId;
              await handleFriendRequest(userId, 'reject');
            });
          });

        } else {
          friendRequestsContainer.innerHTML = '<p>No pending friend requests.</p>';
        }
      } else {
        friendRequestsContainer.innerHTML = `<p>Error fetching friend requests: ${data.message || 'Unknown error'}</p>`;
      }
    } catch (error) {
      console.error('Error fetching friend requests:', error);
      friendRequestsContainer.innerHTML = '<p>Error fetching friend requests. Please try again.</p>';
    }
  };

  // Function to handle accepting or rejecting friend requests
  const handleFriendRequest = async (userId, action) => {
    try {
      let apiEndpoint = '';
      if (action === 'accept') {
        apiEndpoint = 'api/accept_friend.php';
      } else if (action === 'reject') {
        apiEndpoint = 'api/reject_friend.php';
      }

      const formData = new FormData();
      formData.append('friend_id', userId);

      const response = await fetch(apiEndpoint, {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (response.ok) {
        // Refresh the friend requests and friends list
        fetchFriendRequests();
        fetchFriends();
      } else {
        alert(`Error ${action}ing friend request: ${data.message || 'Unknown error'}`);
      }
    } catch (error) {
      console.error(`Error ${action}ing friend request:`, error);
      alert(`Error ${action}ing friend request. Please try again.`);
    }
  };

  // Initial fetch of friends and friend requests
  fetchFriends();
  fetchFriendRequests();
});