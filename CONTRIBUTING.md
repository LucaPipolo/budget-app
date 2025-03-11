# Contributing to Budget-App

Thank you for contributing to this project!

To maintain high code quality and ensure consistency, we use several tools and follow specific guidelines. Please review
this document before submitting your contributions.

---

## Code Quality Tools

We use the following tools to enforce code quality and standards:

### 1. **Laravel Pint**

[Laravel Pint](https://laravel.com/docs/pint) is used to automatically format PHP code according to
the [PSR-12 coding standard](https://www.php-fig.org/psr/psr-12/).

| Command                    | Description                                           |
| -------------------------- | ----------------------------------------------------- |
| `sail composer run lint`   | Check for formatting issues without applying changes. |
| `sail composer run format` | Automatically format PHP code.                        |

### 2. **Prettier**

[Prettier](https://prettier.io/) ensures consistent formatting for frontend assets such as Blade views, JavaScript, CSS,
and other files.

| Command               | Description                                           |
| --------------------- | ----------------------------------------------------- |
| `sail bun run lint`   | Check for formatting issues without applying changes. |
| `sail bun run format` | Automatically format frontend files.                  |

### 3. **Commitlint**

We use [commitlint](https://github.com/conventional-changelog/commitlint) to enforce
the [Conventional Commits](https://www.conventionalcommits.org/) standard for commit messages.

Please check [the Conventional Commits specification](https://www.conventionalcommits.org/en/v1.0.0/#specification) to
learn more.

### 4. **PHP Insights**

[PHP Insights](https://phpinsights.com/) provides insights into the quality of PHP code, including metrics for
complexity, readability, and security.

| Command                      | Description                                |
| ---------------------------- | ------------------------------------------ |
| `sail composer run insights` | Analyze PHP code quality and get insights. |

---

## Testing

We use [Pest](https://pestphp.com/) for testing.

### Available Commands:

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

---

## Pre-Commit Hooks

Thanks to [lint-staged](https://github.com/okonet/lint-staged) and [Husky](https://typicode.github.io/husky/), Laravel
Pint and Prettier are automatically run before every commit to ensure code quality.
You don’t need to manually run these commands before committing — Husky takes care of it!
