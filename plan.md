# Project Plan: ChronoWeave

**Description:** A social network designed to connect people through shared memories and experiences, allowing users to create time-stamped posts and explore interconnected narratives.


## Development Goals

- [ ] Design the database schema in database.sql including users, posts, comments, friendships, and likes tables.
- [ ] Configure the database connection in api/config.php.
- [ ] Implement user registration logic with password hashing in api/register.php.
- [ ] Implement user login and session management in api/login.php.
- [ ] Implement user logout functionality in api/logout.php.
- [ ] Build the HTML structure for register.html, login.html, home.html, profile.html, friends.html, and settings.html using HTML and Tailwind CSS.
- [ ] Develop JavaScript in assets/js/auth.js to handle user registration, login, and logout using AJAX.
- [ ] Create a PHP API endpoint (api/create_post.php) to handle the creation of new posts.
- [ ] Implement JavaScript in assets/js/main.js to allow users to create posts and display posts on the home page using AJAX.
- [ ] Develop a PHP API endpoint (api/get_posts.php) to retrieve posts for the home page feed.
- [ ] Create a PHP API endpoint (api/get_user.php) to retrieve user profile information.
- [ ] Develop JavaScript in assets/js/profile.js to display user profile information and implement user profile editing functionality.
- [ ] Implement a PHP API endpoint (api/update_user.php) to handle user profile updates.
- [ ] Create PHP API endpoints (api/add_friend.php, api/accept_friend.php, api/reject_friend.php, api/get_friends.php) to manage friend requests and friendships.
- [ ] Develop JavaScript in assets/js/friends.js to manage friend requests and display the user's friends list.
- [ ] Create PHP API endpoints (api/create_comment.php, api/get_comments.php) to manage comments on posts.
- [ ] Implement the display of comments beneath each post on the home page using AJAX.
- [ ] Develop PHP API endpoints (api/like_post.php, api/unlike_post.php) to manage likes on posts.
- [ ] Implement liking and unliking posts on the home page using AJAX.
- [ ] Implement image upload functionality for user profile pictures and potentially for posts (store file paths in database).
- [ ] Style the website using Tailwind CSS to create a visually appealing and responsive user interface.
- [ ] Implement error handling and input validation on both the client-side (JavaScript) and server-side (PHP) to ensure data integrity and security.
