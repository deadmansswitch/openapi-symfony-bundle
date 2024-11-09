# OpenApi spec generator for Symfony

### Configuration

Until public release please you have to rely on Configuration.php file to get verified configuration structure. \
The following example can be outdated.

```yaml
dead_mans_switch_openapi:
  openapi: 3.0.0
  info:
    title: 'API'
    version: '1.0.0'
    summary: 'API description'
    termsOfService: 'https://example.com/terms'
  directories:
    - "%kernel.project_dir%/src/Controller"
```