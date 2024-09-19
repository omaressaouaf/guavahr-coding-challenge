## GuavaHR Coding Challenge Senior Full Stack Developer application by Omar Essaouaf

### Database Design

**Notes**:
all tables below might have an `id`, `created_at`, `updated_at` according to the need, this diagram is only a rough estimate based on the provided details but can be extended or modified based on more details or future increment optimizations

- **users**
    - `name`
    - `email`
    - `password`

- **posts**
    - `body`
    - `user_id` (foreign to users table)
    - `feed_context_id` (polymorphic to `groups` or `events` table, nullable if meant to be published on user profile)
    - `feed_context_type` (`App\Models\Group` or `App\Models\Event`, nullable if meant to be published on user profile)

- **comments**
    - `body`
    - `user_id` (foreign to users table)
    - `post_id` (foreign to posts table)

- **reactions**
    - `type` (Enum : Love, Sad, Laugh ...)
    - `user_id` (foreign to users table)
    - `post_id` (foreign to posts table)

- **attachments**
    - `type` (Enum : Link, File)
    - `file_path` (nullable if the type if link)
    - `preview_file_path` (nullable if the type if link)
    - `preview_url` (this will hold the link url if the type is link, however if the type is file it will hold a preview url for a small dimensions image or video that was generated when uploading for a quick preview instead of loading the full file )
    - `attachable_id` (polymorphic to `posts` or `comments` table)
    - `attachable_type` (`App\Models\Post` or `App\Models\Comment`)

- **groups**
    - `name`
    - `description`

- **events**
    - `name`
    - `description`
    - `date`
    - `location`

- **subscriptions**
    - `user_id` (foreign to users table)
    - `subscribable_id` (polymorphic to `groups` or `events` table)
    - `subscribable_type` (`App\Models\Group` or `App\Models\Event`)
    - `type` (Enum : Admin, Member)

- **followers**
    - `follower_id` (foreign to users table)
    - `following_id` (foreign to users table)

### API Routes

- `POST /posts` : Create a new post
- `GET /posts/{id}` : Retrieve a specific post
- `PUT /posts/{id}` : Update an existing post
- `DELETE /posts/{id}` : Delete a post
- `POST /posts/{post_id}/comments` : Add a comment to a post
- `DELETE /comments/{id}` : Delete a comment
- `POST /posts/{post_id}/reactions` : Add a reaction to a post
- `DELETE /reactions/{id}` : Delete a reaction
- `POST /attachments` : Upload a file or link as an attachment
- `GET /feed` : Retrieve the user’s home feed (followings & groups & events posts)
- `GET /users/{id}/feed` : Retrieve the user profile feed
- `GET /groups/{id}/feed` : Retrieve the group feed
- `GET /events/{id}/feed` : Retrieve the event feed
- `GET /link-preview` : Fetch metadata for a link
- `POST /followers` : Add the authenticated user as a follower to the specified user
- `DELETE /followers` : Delete the authenticated user from the followers list of the specifed user
- `POST /subscriptions` : Add the authenticated user as a subscriber to the specified group or event
- `DELETE /subscriptions/{id}` : Delete the subscription
