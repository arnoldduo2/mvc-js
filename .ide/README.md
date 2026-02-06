# IDE Helper Files

This directory contains stub files that help IDEs understand runtime-defined constants and functions.

**Important:** These files are NOT included in the application runtime. They exist only for IDE autocomplete and type checking.

## Files

- `constants.stub.php` - Defines all global constants for IDE recognition

## Usage

If your IDE still shows warnings about undefined constants, you may need to configure it to recognize stub files:

### For PHPStorm/IntelliJ:

1. Go to Settings → PHP → Include Path
2. Add this `.ide` directory

### For VS Code with Intelephense:

Add to your `.vscode/settings.json`:

```json
{
  "intelephense.stubs": [".ide"]
}
```
