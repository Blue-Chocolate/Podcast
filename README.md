🎧 Podcast API Documentation
Public Routes (No Auth Required)
Method	Endpoint	Description
GET	/releases	Get list of releases
GET	/rss/podcast/{slug}	Get RSS feed for a podcast
GET	/podcasts/{slug}/feed	Get RSS feed of a podcast
GET	/podcasts	List all podcasts
GET	/podcasts/{id}	Get details of a specific podcast
GET	/episodes	List all episodes
GET	/episodes/{id}	Get details of a specific episode
GET	/blogs	List all blogs
GET	/blogs/{id}	Get details of a specific blog
POST	/submissions	Submit a new submission
Authentication
Method	Endpoint	Description
POST	/login	Login user and get Sanctum token. Returns token and role.
POST	/logout	Logout user (Auth required)

Login Request Example:

{
  "email": "user@example.com",
  "password": "secret"
}


Login Response Example:

{
  "token": "sanctum-token-here",
  "role": "user"
}

User Routes (Auth + Role:user)
Method	Endpoint	Description
GET	/user/playlists	List all playlists for user
GET	/user/playlists/{id}	Get specific playlist details
GET	/user/releases/{id}/download	Download a release

Note: User cannot create/update/delete posts, blogs, podcasts, episodes, etc. Admin manages all that via Filament.

Admin Routes (Removed from API docs)

Post, Blog, Podcast, Episode, Category, Playlist management is handled via Filament admin panel.

No need to expose POST/PUT/DELETE routes in API docs.

✅ Summary of Clean API:

Public: releases, podcasts, episodes, blogs, submissions, RSS feeds

Authenticated User: playlists, download releases

Admin: Managed entirely in Filament, no API CRUD needed


1️⃣ Public Endpoints (No Auth)

Get all releases:
GET http://www.haqqeq.com/api/releases

Get RSS feed for a podcast by slug:
GET http://www.haqqeq.com/api/rss/podcast/{slug}

Get podcast RSS feed by slug:
GET http://www.haqqeq.com/api/podcasts/{slug}/feed

List all podcasts:
GET http://www.haqqeq.com/api/podcasts

Get podcast details by ID:
GET http://www.haqqeq.com/api/podcasts/{id}

List all episodes:
GET http://www.haqqeq.com/api/episodes

Get episode details by ID:
GET http://www.haqqeq.com/api/episodes/{id}

List all blogs:
GET http://www.haqqeq.com/api/blogs

Get blog details by ID:
GET http://www.haqqeq.com/api/blogs/{id}

