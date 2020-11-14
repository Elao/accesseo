# Seo Tool

SEO Tool allows you to quickly get information about your page about accessibility or SEO criteria, directly in the Symfony profiler. By implementing small HTML improvements, you can easily improve user's experience and allow search engines to better target your content, and increase your visibility in results pages.

## Accessibility Insights 

- Images whithout alt attribute
- Non-explicit icons
- Buttons whithout any contents
- Form : missing for attribute of the label
- Broken external links

## Seo Insights

### Seo main optimizations:

- Title
- Meta description
- Only one H1
- Headings

### Directives for robots:

- Canonical
- Language
- X-robots-tag
- Hreflang
- Meta robots
- Meta Googlebot
- Meta Googlebot-news
- Hreflang tags

### Social Media:

- OpenGraph properties
- Twitter Properties

## Installation

`
composer ...
`
## Usage

In `config/services.yaml`, add :

    App\SEOTool\DataCollector\SEOCollector:
         tags:
             -
                 name: data_collector
                 template: '/profiler/seo_collector.html.twig'
                 # must match the value returned by the getName() method
                 id: 'app.seo_collector'
                 # optional priority
                 # priority: 300
 
    App\SEOTool\DataCollector\AccessibilityCollector:
        tags:
             -
                 name: data_collector
                 template: '/profiler/accessibility_collector.html.twig'
                 # must match the value returned by the getName() method
                 id: 'app.accessibility_collector'
                 # optional priority
                 # priority: 300
