INSERT IGNORE INTO migrations (version, class, group, namespace, time, batch)
VALUES 
  ('2024-01-01-000016', 'App\Database\Migrations\CreatePostsTable', 'default', 'App', UNIX_TIMESTAMP(), 3),
  ('2024-01-01-000017', 'App\Database\Migrations\CreatePostReactionsTable', 'default', 'App', UNIX_TIMESTAMP(), 3),
  ('2024-01-01-000018', 'App\Database\Migrations\CreatePostCommentsTable', 'default', 'App', UNIX_TIMESTAMP(), 3);
