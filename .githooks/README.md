# Git hooks for secret protection

This repository uses a local pre-commit hook in `.githooks/pre-commit`.

## Enable once per clone

Run:

```bash
git config core.hooksPath .githooks
```

The hook scans staged content for common secret patterns and blocks commits if detected.
