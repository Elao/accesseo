# Seo Tool

SEO Tool allows you to quickly get information about your on-page accessibility or SEO criteria, directly in the Symfony profiler.

## Accessibility Insights 

- Images whithout alt attribute ([See more on developer.mozilla.org](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/Img))
- Missing aria-hidden attribute on icons ([See more on developer.mozilla.org](https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/ARIA_Techniques/Using_the_aria-hidden_attribute))
- Buttons without any content ([See more on w3.org](https://www.w3.org/WAI/standards-guidelines/act/rules/button-non-empty-accessible-name-97a4e1/))
- Form : missing for attribute of the label ([See more on developer.mozilla.org](https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/forms/Basic_form_hints))
- Broken external links (very annoying to follow a broken link!)

## Seo Insights

### Seo main optimizations:

- Title ([See more on developer.mozilla.org](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/title))
- Meta description ([See more on Moz.com](https://moz.com/learn/seo/meta-description))
- Headings ([See more on developer.mozilla.org](https://developer.mozilla.org/fr/docs/Web/HTML/Element/Heading_Elements))

### Directives for robots:

- Canonical ([See more on developers.google.com](https://developers.google.com/search/docs/advanced/crawling/consolidate-duplicate-urls))
- Language - hreflang ([See more on developers.google.com](https://developers.google.com/search/docs/advanced/crawling/localized-versions))
- Robots meta tag, and X-Robots-Tag ([See more on developers.google.com](https://developers.google.com/search/reference/robots_meta_tag))

### Social Media:

- OpenGraph properties ([See more on official website "The Open Graph protocol"](https://ogp.me/))
- Twitter Cards ([See more on developer.twitter.com](https://developer.twitter.com/en/docs/twitter-for-websites/cards/overview/abouts-cards))

## Installation

`
composer config repositories.elao/seo-tool vcs https://github.com/Elao/seo-tool.git
composer require elao/seo-tool
`
