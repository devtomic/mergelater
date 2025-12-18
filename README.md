# MergeLater

Schedule GitHub pull request merges for the perfect moment.

**[mergelater.com](https://mergelater.com)** · Free & Open Source

## About

MergeLater lets you schedule GitHub PR merges to happen automatically at a specific time. Stop timing deploys around your calendar—schedule the merge and go live your life.

Built in a single day using [Claude Code](https://m.academy/courses/learn-claude-code/).

## Features

- Schedule PR merges for any date/time
- Timezone-aware scheduling
- Choose merge method (squash, merge, rebase)
- Email & Slack notifications
- Full visibility dashboard

## Tech Stack

- Laravel 12
- PostgreSQL with PgBouncer
- Tailwind CSS
- GitHub OAuth

## Local Development

1. Clone the repo
2. Copy `.env.example` to `.env`
3. Set up GitHub OAuth app and add credentials
4. Run `composer install && npm install`
5. Run `php artisan migrate`
6. Run `php artisan serve`

## License

MIT License - see [LICENSE.md](LICENSE.md)

## Sponsor

A completely free project by [M.academy](https://m.academy). Learn how to build apps like this with [Crack the Claude Code](https://m.academy/courses/learn-claude-code/).
