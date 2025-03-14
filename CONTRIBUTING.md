# Contributing to Budget App

Thank you for contributing to this project!

To maintain high code quality and ensure consistency, we use several tools and follow specific guidelines. Please review
this document before submitting your contributions.

## Setup

To know how to run the project locally, please refer to the steps outlined
in [this section of the README file](https://github.com/LucaPipolo/budget-app/blob/main/README.md#Setup).

## Code Quality Tools

We use the following tools to enforce code quality and standards:

### Laravel Pint

[Laravel Pint](https://laravel.com/docs/pint) is used to automatically format PHP code according to
the [PSR-12 coding standard](https://www.php-fig.org/psr/psr-12/).

| Command                    | Description                                           |
| -------------------------- | ----------------------------------------------------- |
| `sail composer run lint`   | Check for formatting issues without applying changes. |
| `sail composer run format` | Automatically format PHP code.                        |

### Prettier

[Prettier](https://prettier.io/) ensures consistent formatting for frontend assets such as Blade views, JavaScript, CSS,
and other files.

| Command               | Description                                           |
| --------------------- | ----------------------------------------------------- |
| `sail bun run lint`   | Check for formatting issues without applying changes. |
| `sail bun run format` | Automatically format frontend files.                  |

### Commitlint

We use [commitlint](https://github.com/conventional-changelog/commitlint) to enforce
the [Conventional Commits](https://www.conventionalcommits.org/) standard for commit messages.

Please check [the Conventional Commits specification](https://www.conventionalcommits.org/en/v1.0.0/#specification) to
learn more.

### PHP Insights

[PHP Insights](https://phpinsights.com/) provides insights into the quality of PHP code, including metrics for
complexity, readability, and security.

| Command                      | Description                                |
| ---------------------------- | ------------------------------------------ |
| `sail composer run insights` | Analyze PHP code quality and get insights. |

## Pre-Commit Hooks

Thanks to [lint-staged](https://github.com/okonet/lint-staged) and [Husky](https://typicode.github.io/husky/), Laravel
Pint and Prettier are automatically run before every commit to ensure code quality.
You don’t need to manually run these commands before committing — Husky takes care of it!

## Testing

We use [Pest](https://pestphp.com/) for testing.

Available commands are:

| Command                                    | Description                                |
| ------------------------------------------ | ------------------------------------------ |
| `sail test`                                | Run all tests.                             |
| `sail test --type-coverage`                | Check type coverage across the codebase.   |
| `sail test --coverage-html tests/coverage` | Generate an HTML report for test coverage. |

You can view the coverage report by opening the `tests/coverage/index.html` file in your browser.

We also use [Laravel Dusk](https://laravel.com/docs/dusk) for browser-based end-to-end testing.

| Command     | Description                 |
| ----------- | --------------------------- |
| `sail dusk` | Run all Dusk browser tests. |

## Debugging

We provide several tools for debugging:

### Laravel Debug Bar

You can debug using the [Laravel Debug Bar](https://laraveldebugbar.com/), which provides detailed information about
requests, queries, and more.
Check [the official usage instructions](https://laraveldebugbar.com/usage/) to know more.

### Xdebug

To enable [Xdebug](https://xdebug.org/), add `SAIL_XDEBUG_MODE=develop,debug` to the `.env` file.

We recommend using browser extensions such
as [Xdebug helper for Chrome](https://chromewebstore.google.com/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc?hl=en)
or [Xdebug helper for Firefox](https://addons.mozilla.org/en-US/firefox/addon/xdebug-helper-for-firefox/).

You can enable additional Xdebug modes such
as [coverage](https://xdebug.org/docs/code_coverage), [profile](https://xdebug.org/docs/profiler)
or [trace](https://xdebug.org/docs/trace) simply by adding them to the environment variable, e.g.
`SAIL_XDEBUG_MODE=develop,debug,profile,trace`.

Note that enabling additional Xdebug modes like profile or trace may increase resource usage and slow down execution, so
use them judiciously in development environments.

### Laravel Telescope

You can use [Laravel Telescope](https://laravel.com/docs/12.x/telescope) for monitoring and debugging application
behavior, including requests, exceptions, scheduled tasks, and more.

To disable Telescope, set `TELESCOPE_ENABLED=FALSE` in the `.env` file.

### Sentry

On staging environments, [Sentry](https://sentry.io/welcome/) is enabled for advanced debugging and error tracking
capabilities.  
Sentry captures application errors and performance issues, providing actionable insights to improve debugging workflows.

If you are part of the somoscuatro development team, you should already have access. If not, request access
from [Luca Pipolo](mailto:luca@somoscuatro.es) or [Joan López](mailto:joan@somoscuatro.es).

## Working with the API

The Budget App provides an API that developers can use for integrations and testing. The API collection is publicly
available on Postman and is configured for the stage environment. You can access it using the Run in Postman button
below:

[<img src="https://run.pstmn.io/button.svg" alt="Run In Postman" style="width: 128px; height: 32px;">](https://app.getpostman.com/run-collection/16254781-422c5f76-4438-42c2-9e44-f4779cce7b23?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D16254781-422c5f76-4438-42c2-9e44-f4779cce7b23%26entityType%3Dcollection%26workspaceId%3Ddeb06c9e-d726-4976-b31d-5448bb7c9692#?env%5BBudget%20App%20%5Bstage%5D%5D=W3sia2V5IjoiYmFzZV91cmwiLCJ2YWx1ZSI6Imh0dHBzOi8vc3RhZ2UtYmRhLnNvbW9zY3VhdHJvLmVzIiwiZW5hYmxlZCI6dHJ1ZSwidHlwZSI6ImRlZmF1bHQiLCJzZXNzaW9uVmFsdWUiOiJodHRwczovL3N0YWdlLWJkYS5zb21vc2N1YXRyby5lcyIsImNvbXBsZXRlU2Vzc2lvblZhbHVlIjoiaHR0cHM6Ly9zdGFnZS1iZGEuc29tb3NjdWF0cm8uZXMiLCJzZXNzaW9uSW5kZXgiOjB9LHsia2V5IjoidmVyc2lvbiIsInZhbHVlIjoidjEiLCJlbmFibGVkIjp0cnVlLCJ0eXBlIjoiZGVmYXVsdCIsInNlc3Npb25WYWx1ZSI6InYxIiwiY29tcGxldGVTZXNzaW9uVmFsdWUiOiJ2MSIsInNlc3Npb25JbmRleCI6MX1d)

To simplify API requests, we
use [Postman environment variables]([Postman environment variables](https://learning.postman.com/docs/sending-requests/variables/managing-environments/)).
These variables allow you to dynamically set values like
base URLs, authentication tokens, and other parameters. This ensures that requests are tailored to specific environments
without requiring manual adjustments.

Example Environment Variables:

| Name       | Value                            | Description       |
| ---------- | -------------------------------- | ----------------- |
| `base_url` | https://stage-budget-app.com/api | The API base URL. |
| `version`  | v1                               | The API version.  |

Feel free to fork the collection and customize it for your needs. If you encounter any issues or have suggestions for
improving the API documentation, please open an issue in this repository.
