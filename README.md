## About Mobile Application Subscription Managment Api

- For simplicity I made the Mock APIs inside the same project, since the build in PHP development server (artisan: serve) is single threaded Mock APIs wont work unless we run it on a real server.

## API

- Since there can only be one subscription for same appId and same deviceId, I combine them to create unique client_token and modified it for security purposes.

## WORKER

- Scheduled two commands. ('App\Console\Commands\CheckExpiryDate', 'App\Console\Commands\CheckRateLimitedSubscription)
- CheckExpiryDate is scheduled to run daily.
- CheckRateLimitedSubscription is scheduled to run hourly.
- In Both of them I used Chunk method and get the responses from api for each subscription, Held the subscription ids that are going to be canceled inside an array then executed after the loop for faster execution time.
- Did the same thing when any of the responses gave a rate limit error. Inserted them inside a table after the loop.
- In the CheckRateLimitedSubscription command I only check rate_limited_subscriptions table hourly, I add subscription_id in an array if response returns false then I updated the status to 'canceled' and deleted it after the loop .