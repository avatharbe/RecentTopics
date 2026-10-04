### Changelog

- 3.0.12 (04/10/2026) — bugfix release

  New features
  - [NEW] ACP option "Show announcements first": the announcements and global announcements on each page of the list move to the top of that page. Only topics already on the page move, so older announcements are not pulled back in; off by default (#201)

  Fixes
  - [FIX] Password-protected forums no longer leak topic titles, last-post subjects and authors into Recent Topics (index, viewforum and the standalone pages) before the password is entered (#193)
  - [FIX] Existing members see Recent Topics straight after the extension is enabled — the install now clears the cached permissions, so the new `u_rt_view` grant takes effect (#194)
  - [FIX] With "Show all recent topic pages" on, the page count now covers the user's Recent Topics forums instead of only forums they moderate, so the list is no longer cut short (#195)
  - [FIX] New members get the board's Recent Topics defaults on registration — the defaults were written before the account existed, to user id 0 (#196)
  - [FIX] The ACP "Show like counts" setting now works; like counts showed whenever Post Love was installed (#197)
  - [FIX] UCP location and topic-count preferences are validated: locations must be one of the offered options, the count is kept within 1–999 (#198)
  - [FIX] Who Is Online shows "Viewing Recent Topics" as plain text in every language instead of a nested link with a stray `%s` (#199)
  - [FIX] The last-post link in the list has a screen-reader label again (#192, thanks karelpak1)

  Housekeeping
  - [CHANGE] The ACP "Become a patron" button is bundled with the extension instead of loaded from Patreon's servers on every ACP page view (#200)
  - [CHANGE] New `release_3_0_12` migration adds the `rt_announcements_first` setting
  - [CHANGE] Unit tests for the forum list, the page count, like counts, announcement ordering, the UCP checks and the language packs; functional tests for the ACP settings

- 3.0.11 (10/08/2026) — validation release

  Version jumps from 3.0.1 to 3.0.11: the number was reset to 3.0.0 during the 3.0.0 namespace
  rename, but the phpBB extensions database had already published up to 3.0.10.

  Fixes
  - [FIX] UCP no longer stores `user_rt_*` preferences the user lacks permission to set — the write path now mirrors the per-preference ACL checks the display path already made (#188)
  - [FIX] `contrib/cleanup_recenttopics.sql` uses a `{TABLE_PREFIX}` placeholder instead of a hardcoded `phpbb_`, so it can no longer be run unedited on a custom-prefix board (#189)

  Housekeeping
  - [CHANGE] Documented that `rt_ads_code` is intentionally raw HTML, at the template output site (#190)
  - [CHANGE] Docblock pass over the event listeners, `core/recenttopics.php`, the page controller and the ACP module

- 3.0.1 (24/06/2026) — bugfix release

  Fixes
  - [FIX] Recent Topics pagination prev/next arrows rendered as empty (invisible) buttons — restored the missing Font Awesome chevron icons in `rt_pagination.html`, RTL-aware to match core pagination (#183)

- 3.0.0 (31/05/2026) — relaunch under avathar/recenttopics namespace, consolidating everything since the avathar fork of paybas/recenttopics

  New features
  - [NEW] Display Recent Topics on viewforum (top/bottom) in addition to the index page (#178)
  - [NEW] Standalone pages: `/app.php/rt` (full) and `/app.php/rt/simple` (iframe-embeddable), with a dedicated ACP toggle independent of index display (#155)
  - [NEW] Optional advertisement / HTML block on the index page, with ACP toggle and textarea (#160)
  - [NEW] Postlove integration — like counts in the topic list when `avathar/postlove` is installed (#162); ACP toggle to enable/disable (#164)
  - [NEW] ACP toggle to show/hide date in side view (#163)
  - [NEW] `modify_ads_code` event so third-party extensions can inject advertisement content (#175)
  - [NEW] Backward-compat `paybas.recenttopics.*` event aliases — downstream extensions written against the original fork keep working (#169)
  - [NEW] Forum visibility widened: forums where the user has `f_list_topics` ("Can see topics") but not `f_read` now also appear — exposes titles to entice login while content stays gated (#182)
  - [NEW] Slovak (sk) and Swedish (sv) translations; grammar / spelling sweep across all locales
  - [NEW] `contrib/cleanup_recenttopics.sql` script for removing legacy installs

  Changes
  - [CHG] Renamed namespace from the legacy `recenttopicsav` to `recenttopics` — the "av" suffix from the paybas-era fork is no longer needed since the vendor namespace is already `avathar`
  - [CHG] Migration history squashed to a single release migration; canonical version is now `ext::RT_VERSION` (class constant), no `rt_version` row in `phpbb_config`
  - [CHG] Donate link moved from PayPal to Patreon
  - [CHG] PHP 8.1 and phpBB 3.3.0 minimum enforced via `is_enableable()` with clear error messages (#171)
  - [CHG] Removed abandoned integrations: paybas/topicprefixes (#166), imkingdavid/prefixed (#167), nickvergessen/newspage

  Fixes
  - [FIX] PHP 8.0–8.4 compatibility across the codebase (#142, #148)
  - [FIX] French language files no longer 500 on unescaped apostrophes
  - [FIX] Duplicate ad block render in pbWoW3 — use `DEFINE` for the `ADSIDE` flag so it's detectable across template scopes
  - [FIX] Standalone page loads `functions_display.php` and resolves the template path (#138, #150)
  - [FIX] User registration applies ACP default preferences (#124)
  - [FIX] "Posted in" star icon (S_USER_POSTED) now shows in the topic listing (#133)
  - [FIX] Undefined array key warning in the viewonline listener (#176)
  - [FIX] pbWoW3 top/bottom block: collapse support and column alignment (#139)
  - [FIX] Duplicate collapse buttons in pbWoW3 style
  - [FIX] Empty "Latest version:" line when the version check service is unreachable
  - [FIX] Unclosed `<span>` in UCP preferences template
  - [FIX] pbTech side-block heading matches the category-bar style
  - [FIX] Default install grants `u_rt_view` to Administrators and Global moderators in addition to REGISTERED/GUESTS — staff accounts whose primary group is not REGISTERED now see Recent Topics by default

- 2.2.15 (05/04/2021) — last release under paybas/recenttopics before the avathar fork
  - [FIX] PHP 8.0 compatibility (#142)
