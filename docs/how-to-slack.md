# How to send Slack notifications?

* First you need to require the recipe `recipe/slack.php` to include its tasks and options.
* Then you have to provide your `slack_webhook`. More options are available for customizing the messages, the colors, etc. Check the last section for all available options.
* Then hook the Slack tasks into your deployment flow.

```php
require 'recipe/laravel-deployer.php';
require 'recipe/slack.php'; 

// ...

set('slack_webhook', 'YOUR_SLACK_WEBHOOK');
before('deploy', 'slack:notify');
after('success', 'slack:notify:success');
after('deploy:failed', 'slack:notify:failure');
```

# Available tasks

| task  | description |
| - | - |
| `slack:notify` | Notify about the beginning of deployment. |
| `slack:notify:success` | Notify about successful end of deployment. |
| `slack:notify:failure` | Notify about failed deployment. |

# Available options

| option  | default | description |
| - | - | - |
| `slack_webhook` |  | **(required)** Slack incoming webhook url. |
| `slack_title` | `{{application}}` | The title of application. |
| `slack_text` | ``_{{user}}_ deploying `{{branch}}` to *{{target}}*`` | Notification message template, markdown supported. |
| `slack_success_text` | `Deploy to *{{target}}* successful` | Success template. |
| `slack_failure_text` | `Deploy to *{{target}}* failed` | Failure template. |
| `slack_color` | `#4d91f7` | Notification's color. |
| `slack_success_color` | `{{slack_color}}` | Success' color. |
| `slack_failure_color` | `#ff0909` | Failure's color. |