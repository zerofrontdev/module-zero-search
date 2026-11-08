# Zero_Search

Store search that forgives typos and finds more than products.

- **Typos forgiven**: a search that finds nothing as typed runs once more
  with close spellings (OpenSearch fuzziness AUTO: a letter off up to 5
  letters, two beyond; the first letter right): "jaket" finds jackets. Exact
  results are never mixed with fuzzy ones. The results page says "Nothing
  matches "jaket" exactly; these are close spellings."; the search box's
  products are headed "Close spellings of …". Applies to the
  `quick_search_container` request (results page, layered navigation
  counts, the search box).
- **More than products**, in the search box's suggestions (3 of each) and
  above the results (a row of each): categories (with where they are, "Men ›
  Tops"), brands (Zero_Brands), blog posts (Zero_Blog), CMS pages. Each
  word of the query must be in the name. Another module adds its own:
  `Zero\Search\Model\SourceInterface`, registered on
  `Zero\Search\Model\Sources` in its `etc/frontend/di.xml`. A match may
  carry `tags`, the cache tags of what it links (categories `cat_c_<id>`,
  pages `cms_p_<id>`): the full page cached suggestions and results pages
  are tagged with them, so saving that category or page cleans them.
- **Popular searches**: the empty search box (on first focus) and a search
  with no results offer the store's most searched terms that found
  something (Marketing > Search Terms: untick "Display in Suggested Terms"
  to hide one).

Settings: Stores > Configuration > Zero > Search (typos, more results,
popular count). Synonyms and redirects for a term are Magento's own:
Marketing > SEO & Search > Search Synonyms, Search Terms.

The search box template is the Zero theme's
`Magento_Search/templates/form.mini.phtml`; the suggestions endpoint is
Zero_Core's `zero/search/suggest`, its template replaced here. Its answers
are full page cached per query, customer group and currency, tagged with the
products they show (0.6 s a keystroke uncached on 100 000 products).

## Install

Requires Magento Open Source 2.4.7–2.4.9 and PHP 8.2–8.4 with the GD extension built with WebP (`php -r 'var_dump(function_exists("imagewebp"));'` says `true`; Magento's own PHP requirements cover the rest). Without WebP in GD, Zero skips its WebP image copies and serves the originals.

```bash
composer require zerofront/module-zero-search
bin/magento setup:upgrade
bin/magento setup:di:compile           # production mode
bin/magento setup:static-content:deploy # production mode
bin/magento cache:flush
```

Composer installs `zerofront/module-zero-core` with it.

Part of [`zerofront/zero`](https://zerofront.dev), which installs the Zero theme and every Zero module at once.

## Tests

`Test/Unit` (fuzzy clauses, sources); `dev/tests/zero-e2e/tests/search.spec.ts`
(`ZERO_SEARCH_TYPO`, `ZERO_SEARCH_CATEGORY` for a catalog other than Luma's).

What changed in each release: [CHANGELOG.md](CHANGELOG.md).

## License

OSL-3.0 or AFL-3.0, your choice: see `NOTICE.md`.

---

Zero is made by [Fixstacks](https://fixstacks.com).
Documentation: https://zerofront.dev/docs. Demo store: https://demo.zerofront.dev.
