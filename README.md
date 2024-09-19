# GuavaHR Coding Challenge Senior Full Stack Developer application by Omar Essaouaf

## Documentation and Planning

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

### Description

- **Performance considerations for handling feeds** : we could use an advanced join query to retrieve user home feed, or we can go a step further and cache the feeds in the database in a way which when the post is created we determine the correponding users that we should show the post to based on the subscriptions and followers, and this feed can be saved in a separate database table (e.g., `feeds (user_id, post_id)`)
- **File storage scalability** : we may want to use a dedicated file storage service such as aws s3 for a reliable storage solution (backup, latency, region availability)
- For the translations we can go either the manual way and give the user the possiblity to set his post in different languages, tho this seems like a cumbersome process for the user so we might as well just rely on translation services like Google Translation or DeepL to deligate the translation process
- We should be aware of some of the security concerns regarding file uploads, including size limits and malicious file detection and private files hosting to ensure the privacy of post's attachments.

### Question answers

- 1 : Common features such as handling posts, comments, and reactions could be abstracted into separate services or classes for modularity. Example: `ReactionService` for managing reactions across posts and comments (if needed). `FileHandlingService` for managing attachments.`TranslationService` for handling posts and comments translation.

- 2 : I would implement cursor based pagination to handle large data sets efficiently since accurate page pagination is not required, for the frontend i would use a simple infinit scrolling mechanism to fetch the next pages.

- 3 : Services like Google Translate may not always offer the best accurate translation which might lead to possible misinterpretations.

- 4 : I've never encounted this use case but after doing a bit of research i found couple of solutions such as OpenGraph or oEmbed that can offer metadata about a url using a simple http request, so this might be delegated to separate route to get the metadata on realtime or can be implemented as background job that fetches and stores link metadata when a link is attached to a post/comment, ensuring a quick response to the user.

- 5 : I would say the tricky part about handling attachments is preventing malicious file uploads and generating thumbnails for images/videos for quick previews without loading the full file, luckily in laravel ecosystem there is a well known package `spatie/laravel-medialibrary` that can help with the latter

- 6 : We can track post views by logging the post ID and user ID in a separate `views` table.
To calculate percentages (e.g., 71% of users have seen this post), count the number of unique users who viewed the post and divide by the total number of users eligible to see it.
and when the user sees the post (either when it's loaded in the screen or being navigated to) we can send a post request to the backend to log the view, we can go a step further and cache the count percentage inside the posts table everytime the post is seen to improve the stat performance

