# Accesseo

Accesseo helps you improve the accessibility and SEO of your pages with some simple optimization insights.

## Installation

```
composer config repositories.elao/accesseo vcs git@github.com:Elao/accesseo.git
composer require --dev elao/accesseo
```

in `config/bundles.php` add:

```php
Elao\Bundle\Accesseo\ElaoAccesseoBundle::class => ['dev' => true],
```

in `config/routes/dev`, create `accesseo.yaml` and add:

```yaml
accesseo:
    resource: '@ElaoAccesseoBundle/config/routing/accesseo.xml'
    prefix: /_accesseo
```

### Configuration options

in `config/packages/elao_accesseo.yaml` :

#### Disable bundle : 

```yaml
elao_accesseo:
    enabled: false
```

#### Disable SEO panel : 

```yaml
elao_accesseo:
  enabled_seo_panel: false
```

#### Disable Accessibility panel : 

```yaml
elao_accesseo:
  enabled_accessibility_panel: false
```

### Accessibility panel : icons

Configure the list of icons css classes in your html so we can check if they are readable by screen readers :

```yaml
elao_accesseo:
  icons: 
    - icon
    - duck-icon
    - ...
```
