# Rising Seer Website

Marketing website and landing pages for Rising Seer.

## Structure

```
website/
├── cosmopyxis-local/           # Local WordPress instance
│   ├── app/
│   │   └── public/
│   │       └── wp-content/
│   │           └── themes/
│   │               └── rising-seer/  # Custom theme
│   │                   ├── assets/   # Logos, animations, JS
│   │                   ├── page-splash.php  # Landing page
│   │                   ├── page-verify.php  # Email verification
│   │                   └── style.css
│   ├── conf/                   # WordPress config
│   └── logs/                   # Local logs
└── cosmopyxis-wordpress-backup.tar.gz  # Backup file
```

## Key Pages

- **Splash Page** (`page-splash.php`): Main landing page at risingseer.com
- **Verify Page** (`page-verify.php`): Email authentication/verification flow

## Assets

- Rising Seer logo with eye tracking animation
- Brand wordmark and decorative elements
- Gold star animations
- Custom JavaScript for interactive elements

## Development

1. Use [Local by Flywheel](https://localwp.com/) or similar for local WordPress development
2. Theme location: `website/cosmopyxis-local/app/public/wp-content/themes/rising-seer/`
3. Make changes to PHP templates and CSS as needed

## Deployment

- **Production**: Use WP Migrate or FTP (Cyberduck) to push changes
- **Domain**: risingseer.com
- **Theme**: Rising Seer (custom)

## Notes

- Unity WebGL app has been removed (deprecated in favor of React Native)
- This repo contains only the WordPress marketing site
- For the mobile app, see [rising-seer-mobile](https://github.com/rising-seer/rising-seer-mobile)