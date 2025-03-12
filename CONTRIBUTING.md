# Contributing to Budget App

Thank you for contributing to this project!

To maintain high code quality and ensure consistency, we use several tools and follow specific guidelines. Please review
this document before submitting your contributions.

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
