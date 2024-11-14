# Multisite Root Asset Fix

Fixes broken assets, media, and ACF fields in WordPress multisite subdirectory installations by ensuring all subsites load assets from the main site URL.

## Requirements

- WordPress 5.0+
- PHP 7.2+
- WordPress Multisite with subdirectories (not subdomains)

## Installation

### Via Composer (recommended)

```bash
composer require josephfusco/multisite-root-asset-fix
```

### Manual Installation

1. Upload to `/wp-content/plugins/`
2. Network Activate through 'Network Admin > Plugins'
3. No configuration needed

## Features

- Fixes CSS, JS, and media URLs
- Corrects ACF field paths
- Zero configuration
- Works automatically

## Contributing

1. Fork repository
2. Create feature branch
3. Submit Pull Request

## License

GPL v2 or later
