# How to create complex strategies?

If the `strategies` configuration is not enough for you and you'd like to define your strategy as a PHP function, follow these steps:

1. [Create your own recipe](how-to-custom-recipes.md) and include it in your configurations.
    
    ```php
    // config/deploy.php

    'include' => [
        'recipe/my_recipe.php',
    ],
    ```

2. Create a new task in your recipe using the `strategy:` namespace.

    ```php
    // recipe/my_recipe.php
    
    desc('My complex custom strategy')
    task('strategy:my_strategy', function() {
        // ...    
    });
    ```

3. Use your new custom strategy.

    ```php
    // config/deploy.php

    // Globally...
    'default' => 'my_strategy',

    // ... Or locally
    'hosts' => [
        'domain.com' => [
            'strategy' => 'my_strategy',
            // ...
        ],
    ],
    ```