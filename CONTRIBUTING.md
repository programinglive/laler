# Contributing to Laler

Thank you for your interest in contributing to Laler! We welcome contributions from the community and are pleased to have you join us.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [How to Contribute](#how-to-contribute)
- [Coding Guidelines](#coding-guidelines)
- [Commit Message Format](#commit-message-format)
- [Pull Request Process](#pull-request-process)
- [Release Process](#release-process)
- [Getting Help](#getting-help)

## Code of Conduct

This project and everyone participating in it is governed by our [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code.

## Getting Started

1. **Fork the repository** on GitHub
2. **Clone your fork** locally:
   ```bash
   git clone https://github.com/YOUR_USERNAME/laler.git
   cd laler
   ```
3. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```
4. **Run tests** to ensure everything works:
   ```bash
   vendor/bin/phpunit --stop-on-failure
   ```

## Development Setup

### Prerequisites

- PHP ^8.0
- Composer
- Node.js (for release tooling)
- Git

### Local Development

1. **Create a feature branch**:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes** following our coding guidelines

3. **Run tests** frequently:
   ```bash
   vendor/bin/phpunit --stop-on-failure
   ```

4. **Test with examples**:
   ```bash
   php examples/plain_php_usage.php
   ```

## How to Contribute

### Types of Contributions

- **Bug Reports**: Found a bug? Please create an issue with detailed reproduction steps
- **Feature Requests**: Have an idea? Open an issue to discuss it first
- **Code Contributions**: Bug fixes, new features, improvements
- **Documentation**: Improve README, add examples, fix typos
- **Testing**: Add test cases, improve test coverage

### Before Contributing

1. **Check existing issues** to avoid duplicates
2. **Discuss major changes** by opening an issue first
3. **Follow our coding guidelines** and conventions
4. **Ensure tests pass** before submitting

## Coding Guidelines

### PHP Code Style

- Follow PSR-12 coding standard
- Use strict typing: `declare(strict_types=1);`
- Document public methods with PHPDoc
- Use meaningful variable and method names

### Code Quality

- **Write tests** for new functionality
- **Maintain backward compatibility** when possible
- **Handle errors gracefully** with proper exceptions
- **Validate input parameters** appropriately

### File Organization

- Place new dumpers in `src/Dumpers/`
- Add examples to `examples/` directory
- Update tests in `tests/` directory
- Document changes in README.md

## Commit Message Format

We use [Conventional Commits](https://www.conventionalcommits.org/) for clear and consistent commit messages:

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code formatting (no logic changes)
- `refactor`: Code restructuring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks
- `ci`: CI/CD changes

### Examples

```bash
feat: add new BrowserConsoleDumper for web debugging
fix: resolve memory leak in DumpCaptureManager
docs: update README with Laravel 11 compatibility
test: add unit tests for TauriDumper integration
```

### Interactive Commits

Use our interactive commit tool:
```bash
npm run commit
```

## Pull Request Process

1. **Ensure tests pass**:
   ```bash
   vendor/bin/phpunit --stop-on-failure
   ```

2. **Update documentation** if needed

3. **Create descriptive PR title** following conventional commit format

4. **Fill out PR template** with:
   - Clear description of changes
   - Link to related issues
   - Testing instructions
   - Screenshots (if UI changes)

5. **Request review** from maintainers

6. **Address feedback** promptly and professionally

### PR Requirements

- [ ] Tests pass
- [ ] Code follows style guidelines
- [ ] Documentation updated
- [ ] Commit messages follow conventional format
- [ ] No merge conflicts
- [ ] Descriptive PR title and description

## Release Process

We use automated releases with semantic versioning:

```bash
# Patch release (1.0.1 -> 1.0.2)
npm run release:patch

# Minor release (1.0.1 -> 1.1.0)
npm run release:minor

# Major release (1.0.1 -> 2.0.0)
npm run release:major

# Auto-detect version bump
npm run release
```

## Getting Help

### Questions?

- **GitHub Discussions**: Best for general questions and community interaction
- **Issues**: For bug reports and feature requests
- **Email**: [mahatmamahardhika200588@gmail.com](mailto:mahatmamahardhika200588@gmail.com) for security or private matters

### Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [Symfony VarDumper Component](https://symfony.com/doc/current/components/var_dumper.html)
- [Laravel Documentation](https://laravel.com/docs)
- [Conventional Commits](https://www.conventionalcommits.org/)

## Recognition

Contributors will be recognized in:
- GitHub contributor list
- Release notes for significant contributions
- Special mentions for outstanding contributions

---

Thank you for contributing to Laler! Your efforts help make debugging PHP applications easier for developers worldwide. 🚀
