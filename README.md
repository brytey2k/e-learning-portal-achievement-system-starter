# Course Portal Achievement System

## About Project

Welcome to the Course Portal Achievement System documentation. This system is designed to provide users with achievements and badges based on their activities within the Course Portal. Achievements are unlocked when users complete certain milestones, such as watching lessons or writing comments, while badges are earned based on the number of achievements unlocked.

It however does not include a front end component. It only has the backend code for the application.

## Table of Contents

1. [Getting Started](#getting-started)
2. [Listening for Events](#listening-for-events)
3. [Achievements and Badges](#achievements-and-badges)
4. [Console Commands](#console-commands)

## Getting Started

To implement the Course Portal Achievement System, you need to integrate it into your Laravel project. Follow these steps to get started:

1. Clone this repository to your local environment.

2. Run `composer install` to install the necessary dependencies.

3. Configure your database settings in the `.env` file.

4. Run database migrations and seed the database with initial data:

   ```
   php artisan migrate --seed
   ```

5. Make sure to set up the event listeners for `LessonWatched` and `CommentWritten` events as described in the [Listening for Events](#listening-for-events) section below. Default event listeners have already been provided. You can also add on to these listeners to unlock additional achievements and badges.

## Listening for Events

In your Laravel application, you should have event listeners for the `LessonWatched` and `CommentWritten` events. When these events are fired, the Course Portal Achievement System will automatically listen for them and unlock relevant achievements and badges for users.

Here's an example of how to set up event listeners in Laravel:

```php
protected $listen = [
    LessonWatched::class => [
        // Add your listener for LessonWatched event here
    ],
    CommentWritten::class => [
        // Add your listener for CommentWritten event here
    ],
];
```

Ensure that these listeners call the appropriate methods to unlock achievements and badges.

## Achievements and Badges

The Course Portal Achievement System has several achievements and badges that users can unlock:

### Lessons Watched Achievements

- First Lesson Watched
- 5 Lessons Watched
- 10 Lessons Watched
- 25 Lessons Watched
- 50 Lessons Watched

### Comments Written Achievements

- First Comment Written
- 3 Comments Written
- 5 Comments Written
- 10 Comments Written
- 20 Comments Written

### Badges

- Beginner: 0 Achievements
- Intermediate: 4 Achievements
- Advanced: 8 Achievements
- Master: 10 Achievements

Achievements and badges are unlocked automatically when users meet the criteria. When an achievement is unlocked, an `AchievementUnlocked` event is fired with the relevant information. Similarly, when a user earns a new badge, a `BadgeUnlocked` event is fired.

## Console Commands

To simulate user activities and test the Course Portal Achievement System, you can use Laravel console commands. These commands allow you to simulate watching lessons and writing comments for testing purposes.

Here are some example commands you can run:

- Simulate watching a lesson:
  ```
  php artisan app:watch-lesson {--lesson=1: Lesson ID to watch} {--user=1: User ID of the watcher}
  ```
  Replace the IDs with the user id of the user watching the lesson and lesson id for the lesson being watched respectively.

- Simulate writing a comment:
  ```
  php artisan app:write-comment {--user=1} {--comment=This is a comment}
  ```

You can use these commands to test the unlocking of achievements and badges based on user activities.

That's it! You now have the Course Portal Achievement System integrated into your Laravel project. Users can earn achievements and badges as they engage with the Course Portal, and you can monitor their progress through the provided commands.

## How to run the project
- Run `php artisan serve` to start the server

## Testing
- Run `php artisan test` to run the tests
- Run `php artisan test --coverage-html coverage-report` to generate a coverage report
