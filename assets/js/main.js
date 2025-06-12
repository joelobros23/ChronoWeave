/* Content for assets/js/main.js */

document.addEventListener('DOMContentLoaded', () => {
    const postForm = document.getElementById('post-form');
    const postContent = document.getElementById('post-content');
    const postsContainer = document.getElementById('posts-container');

    if (postForm) {
        postForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const content = postContent.value.trim();

            if (content) {
                try {
                    const response = await fetch('api/create_post.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ content })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        postContent.value = ''; // Clear the input
                        loadPosts(); // Reload posts to display the new one
                    } else {
                        console.error('Error creating post:', data.error || 'Unknown error');
                        alert('Error creating post: ' + (data.error || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Network error:', error);
                    alert('Network error creating post.');
                }
            } else {
                alert('Post content cannot be empty.');
            }
        });
    }


    async function loadPosts() {
        if (!postsContainer) return; // Ensure postsContainer exists

        try {
            const response = await fetch('api/get_posts.php');
            const data = await response.json();

            if (response.ok) {
                postsContainer.innerHTML = ''; // Clear existing posts
                data.forEach(post => {
                    const postElement = createPostElement(post);
                    postsContainer.appendChild(postElement);
                });
            } else {
                console.error('Error loading posts:', data.error || 'Unknown error');
                postsContainer.innerHTML = '<p>Error loading posts.</p>';
            }
        } catch (error) {
            console.error('Network error:', error);
            postsContainer.innerHTML = '<p>Network error loading posts.</p>';
        }
    }

    function createPostElement(post) {
        const postDiv = document.createElement('div');
        postDiv.classList.add('post');

        // Basic post structure (you can expand on this)
        postDiv.innerHTML = `
            <p>${post.content}</p>
            <small>Posted by User ${post.user_id} at ${post.created_at}</small>
        `;

        return postDiv;
    }


    // Initial load of posts
    if (postsContainer) {
        loadPosts();
    }
});