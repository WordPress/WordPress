<p align="center">
  <a href="https://sentry.io/?utm_source=github&utm_medium=logo" target="_blank">
    <img src="https://sentry-brand.storage.googleapis.com/sentry-wordmark-dark-280x84.png" alt="Sentry" width="280" height="84">
  </a>
</p>

# Contributing to the Sentry PHP SDK

We welcome contributions to `sentry-php` by the community.

Please search the [issue tracker](https://github.com/getsentry/sentry-php/issues) before creating a new issue (a problem or an improvement request). Please also ask in our [Sentry Community on Discord](https://discord.com/invite/Ww9hbqr) before submitting a new issue. There is a ton of great people in our Discord community ready to help you!

If you feel that you can fix or implement it yourself, please read on to learn how to submit your changes.

## Submitting changes

- Setup the development environment.
- Clone the `sentry-php` repository and prepare necessary changes.
- Add tests for your changes to `tests/`.
- Run tests and make sure all of them pass.
- Open a pull request.

We will review your pull request as soon as possible.
Thank you for contributing!

## PR reviews

For feedback in PRs, we use the [LOGAF scale](https://develop.sentry.dev/engineering-practices/code-review/#logaf-scale) to specify how important a comment is.

You only need one approval from a maintainer to be able to merge. For some PRs, asking specific or multiple people for review might be adequate.

Our different types of reviews:
  
  1. **LGTM without any comments.** You can merge immediately.
  2. **LGTM with low and medium comments.** The reviewer trusts you to resolve these comments yourself, and you don't need to wait for another approval. 
  3. **Only comments.** You must address all the comments and need another review until you merge.
  4. **Request changes.** Only use if something critical is in the PR that absolutely must be addressed. We usually use `h` comments for that. When someone requests changes, the same person must approve the changes to allow merging. Use this sparingly.

## Development environment

### Clone the repository

```bash
git clone git@github.com:getsentry/sentry-php.git
```

Make sure that you have PHP 7.2+ installed. Version 7.4 or higher is required to run style checkers. On macOS, we recommend using brew to install PHP. For Windows, we recommend an official PHP.net release.

### Install the dependencies

Dependencies are managed through [Composer](https://getcomposer.org/):

```bash
composer install
```

### Running tests

Tests can then be run via [PHPUnit](https://phpunit.de/):

```bash
vendor/bin/phpunit
```

## Releasing a new version

(only relevant for Sentry employees)

Prerequisites:

- All changes that should be released must be in the `master` branch.
- Every commit should follow the [Commit Message Format](https://develop.sentry.dev/commit-messages/#commit-message-format) convention.

Manual Process:

- Update CHANGELOG.md with the version to be released. Example commit: https://github.com/getsentry/sentry-php/commit/877bca3f0f0ac0fc8ec0a218c6070cccea266795.
- On GitHub in the `sentry-php` repository go to "Actions" select the "Release" workflow.
- Click on "Run workflow" on the right side and make sure the `master` branch is selected.
- Set "Version to release" input field. Here you decide if it is a major, minor or patch release. (See "Versioning Policy" below)
- Click "Run Workflow"

This will trigger [Craft](https://github.com/getsentry/craft) to prepare everything needed for a release. (For more information see [craft prepare](https://github.com/getsentry/craft#craft-prepare-preparing-a-new-release)) At the end of this process, a release issue is created in the [Publish](https://github.com/getsentry/publish) repository. (Example release issue: https://github.com/getsentry/publish/issues/815)

Now one of the persons with release privileges (most probably your engineering manager) will review this Issue and then add the `accepted` label to the issue.

There are always two persons involved in a release.

If you are in a hurry and the release should be out immediately there is a Slack channel called `#proj-release-approval` where you can see your release issue and where you can ping people to please have a look immediately.

When the release issue is labeled `accepted` [Craft](https://github.com/getsentry/craft) is triggered again to publish the release to all the right platforms. (See [craft publish](https://github.com/getsentry/craft#craft-publish-publishing-the-release) for more information). At the end of this process, the release issue on GitHub will be closed and the release is completed! Congratulations!

There is a sequence diagram visualizing all this in the [README.md](https://github.com/getsentry/publish) of the `Publish` repository.

### Versioning Policy

This project follows [semver](https://semver.org/), with three additions:

- Semver says that major version `0` can include breaking changes at any time. Still, it is common practice to assume that only `0.x` releases (minor versions) can contain breaking changes while `0.x.y` releases (patch versions) are used for backwards-compatible changes (bugfixes and features). This project also follows that practice.

- All undocumented APIs are considered internal. They are not part of this contract.

- Certain features (e.g. integrations) may be explicitly called out as "experimental" or "unstable" in the documentation. They come with their own versioning policy described in the documentation.

We recommend pinning your version requirements against `1.x.*` or `1.x.y`.
Either one of the following is fine:

```json
"sentry/sentry": "^1.0",
"sentry/sentry": "^1",
```

A major release `N` implies the previous release `N-1` will no longer receive updates. We generally do not backport bugfixes to older versions unless they are security relevant. However, feel free to ask for backports of specific commits on the bug tracker.

## Commit message format guidelines

See the documentation on commit messages here:

https://develop.sentry.dev/commit-messages/#commit-message-format
