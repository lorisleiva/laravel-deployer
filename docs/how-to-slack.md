# How to send Slack notifications?

1. First you need to include the recipe `recipe/slack.php` in your `config/deploy.php` file.
2. Then you have to provide your `slack_webhook`. More options are available for customizing the messages, the colors, etc. Check the last section for all available options.
3. Then hook the Slack tasks into your deployment flow.

```php
// config/deploy.php

'include' => [  // 1
    'recipe/slack.php',
],
'options' => [  // 2
    'slack_webhook' => 'YOUR_SLACK_WEBHOOK',
],
'hooks' => [    // 3
    'start' => ['slack:notify'],
    'success' => ['slack:notify:success'],
    'fail' => ['slack:notify:failure'],
],
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
