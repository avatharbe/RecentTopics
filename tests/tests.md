# RecentTopics Test Suite

## What is this document?

This file explains every test in the RecentTopics test suite — what the test does, why it exists, and what would go wrong if it were removed. It is written for developers who are new to automated testing.

---

## A quick primer on automated tests

**What is an automated test?**
A test is a small PHP script that calls your code and then checks whether the result matches what you expect. If the check fails, the test runner (PHPUnit) marks it red and prints a message. If it passes, it is green. You run all the tests every time you change code, so a broken change gets caught immediately — before it reaches users.

**Why bother writing them?**
Because phpBB extensions have many moving parts: listeners, controllers, database queries, template variables. Without tests, the only way to know whether a change broke something is to manually click through every scenario in a browser. Tests do that checking automatically in seconds.

**Unit tests vs functional tests**
- A *unit test* tests one class in isolation. Dependencies (the database, other services, the template engine) are replaced with *mocks* — fake objects that return whatever the test tells them to. Unit tests are fast and focused.
- A *functional test* starts a real phpBB installation, loads the extension, makes real HTTP requests, and checks the rendered HTML. It is slower but catches problems that unit tests cannot, such as broken service wiring or routing mistakes.

**What is a mock?**
A mock is a stand-in object. Instead of using the real `$auth` service (which requires a full phpBB setup), the test creates a fake one that returns `true` when asked `acl_get('u_rt_view')`. This lets the test stay focused on the class being tested, not on all its dependencies.

**What is a data provider?**
A data provider is a method that returns a table of inputs and expected outputs. PHPUnit runs the test once per row. For example, `viewonline_data()` returns four rows (the RT page, the simple page, some other app page, a non-app page). The test `test_viewonline_overwrite_location` runs four times — once for each row — with a one-line description telling you which case failed.

**What is a ReflectionClass?**
PHP's `ReflectionClass` lets you reach inside an object and read or call things that are marked `private` or `protected`. Tests use it to check internal state without changing the production code's visibility.

**What is an event dispatcher?**
phpBB fires named events as it runs (see `contrib/events.md`). An event dispatcher is the object that does the firing. In the event contract tests, the test suite builds its own lightweight dispatcher so it can register listeners inline, trigger a method, and check whether the listener was called.

---

## Test structure

```
tests/
├── acp/
│   └── acp_template_test.php      ACP template assets (no external images)
├── event/
│   ├── listener_test.php          Main event listener (board index, ACP, WOL, permissions)
│   └── ucp_listener_test.php      UCP preferences listener
├── controller/
│   └── page_controller_test.php   Dedicated /rt and /rt/simple page controller
├── core/
│   ├── announcements_first_test.php   ACP "Show announcements first" setting
│   ├── forum_list_test.php        Which forums the list may draw topics from
│   ├── topic_count_test.php       "Show all pages" count uses the forum list
│   ├── topic_likes_test.php       ACP "Show like counts" setting
│   └── recenttopics_events_test.php   Public event API contract (contrib/events.md)
├── language/
│   └── viewonline_lang_test.php   Who Is Online label in every language pack
├── template/
│   └── template_vars_test.php     No unassigned template variables in shipped templates
└── functional/
    └── recenttopics_test.php      End-to-end browser tests
```

Tests run automatically via GitHub Actions on every push and pull request to `main` and `develop`.

---

## 1. Main event listener (`event/listener_test.php`)

**What this code does:**
`event/listener.php` is the heart of the extension on the board index. It subscribes to phpBB core events and responds to them. When phpBB fires `core.index_modify_page_title`, the listener calls `display_recent_topics()` to render the block. When it fires `core.viewonline_overwrite_location`, the listener sets the "Viewing Recent Topics" label in the Who Is Online page. It also hooks into ACP events so administrators can configure per-forum inclusion and user permissions.

**What this test file does:**
It creates the listener with mocked dependencies — a `config` object with real settings, and mocks for `auth`, `request`, `helper`, `language`, and `rt_functions`. Each test calls one handler method and checks the effect.

| Test | Scenario | What failure means |
|------|----------|--------------------|
| `test_getSubscribedEvents` | Verifies the listener registers exactly the 7 expected phpBB events | Any event subscription that was accidentally removed or renamed stops working silently — this is the canary |
| `test_display_rt_enabled` | `rt_index` config = 1; calls `display_rt()` | `display_recent_topics()` must be called exactly once; if not, the RT block never appears |
| `test_display_rt_disabled` | `rt_index` config = 0; calls `display_rt()` | `display_recent_topics()` must NOT be called; if it is, the block appears even when the admin turned it off |
| `test_viewonline_overwrite_location` — `rt_page` | Session page is `app.php/rt` | Location must be set to "Recent Topics" with the correct route URL |
| `test_viewonline_overwrite_location` — `rt_simple` | Session page is `app.php/rt/simple` | Same as above but for the simple page route |
| `test_viewonline_overwrite_location` — `other_page` | Session page is some other app route | Location and URL must remain empty — the listener must not match unrelated pages |
| `test_viewonline_overwrite_location` — `not_app` | Session page is `viewtopic.php` (not an app route) | Same — must not match; wrong matches show incorrect location text to all users |
| `test_acp_manage_forums_request_data` | Admin saves forum settings with checkbox = 0 | `forum_data['forum_recent_topics']` must be set to 0; if missing, the per-forum exclusion never saves |
| `test_acp_manage_forums_initialise_data_add` | Action is 'add' (creating a new forum) | Default must be set to `'1'` (included); new forums should appear in RT by default |
| `test_acp_manage_forums_initialise_data_edit` | Action is 'edit' (editing an existing forum) | Default must NOT be set; the edit form reads its value from the database, not from a hard-coded default |
| `test_acp_manage_forums_display_form` | Stored value is 1 | `template_data['RECENT_TOPICS']` must be set to 1 so the checkbox shows the right state |
| `test_add_permission` | Calls `add_permission()` | All 6 `u_rt_*` permission keys must be registered; a missing key silently removes that user control |
| `test_topictitle_remove_re` — `with_re_prefix` | Subject is `'Re: Test topic'` | Must become `'Test topic'`; "Re: " must be stripped |
| `test_topictitle_remove_re` — `without_re_prefix` | Subject is `'Test topic'` | Must remain `'Test topic'`; non-prefixed titles must be left alone |
| `test_topictitle_remove_re` — `no_subject_key` | Row has no `topic_last_post_subject` key | Must not crash; must not add the key to the row |

---

## 2. UCP preferences listener (`event/ucp_listener_test.php`)

**What this code does:**
`event/ucp_listener.php` lets registered users customise their own Recent Topics experience. They can choose how many topics to show, where to position the block, whether to show only unread topics, and so on. The listener hooks into the UCP display-preferences page to show those settings and save them.

There are three handlers:
- `ucp_prefs_get_data` — runs on page load AND on form submit; builds the data array, keeping the locations within their allowed options and the number within 1–999, and (only on page load) renders the UCP template block
- `ucp_prefs_set_data` — maps the submitted form fields to the SQL column names used in `phpbb_users`
- `ucp_register_set_data` — runs on `core.ucp_register_register_after`, after the new account is inserted, and writes the global config defaults to that user's row

**What this test file does:**
Creates the listener with mocks for auth, config, request, template, user, language, and db. Because `ucp_register_set_data` actually runs a database query, it is the only handler tested with mock expectations on `sql_build_array` and `sql_query`.

| Test | Scenario | What failure means |
|------|----------|--------------------|
| `test_getSubscribedEvents` | Verifies the 3 expected event subscriptions | Accidentally removed subscription stops the UCP page from working |
| `test_ucp_prefs_set_data` | Submits 5 preference fields | Each `data['rt_*']` field must map to the correct `sql_ary['user_rt_*']` column; a mismatch means preferences silently fail to save |
| `test_number_only_permission_shows_ucp_section` | Page load; user holds only `u_rt_view` and `u_rt_number` | `S_RT_SHOW` and `A_RT_NUMBER` are assigned. `S_RT_SHOW` used to omit `u_rt_number`, hiding the whole section from such a user (#216) |
| `test_ucp_prefs_get_data_no_submit` | Page load (submit = false) | Must: merge user DB values into `data`, call `add_lang()`, call `template->assign_vars()` |
| `test_submitted_preferences_are_validated` | Data provider: valid values; unknown location; `RT_SIDE` as viewforum location; numbers 100000, 0 and -5 | Locations outside the allowed options fall back to the user's stored value; the number is clamped to 1–999 (#198) |
| `test_invalid_stored_location_falls_back_to_board_default` | Submitted and stored locations both invalid | The board default `rt_location` is used (#198) |
| `test_ucp_prefs_get_data_on_submit` | Form submit (submit = true) | Must: merge data; must NOT call `template->assign_vars()` — template must only be touched on page load, not on form processing |
| `test_register_defaults_run_after_the_account_exists` | Event map | `ucp_register_set_data` must be on `core.ucp_register_register_after` (fires after `user_add()`, carries `user_id`) and not on `core.ucp_register_data_after` (fires during validation, no `user_id`, so the UPDATE hit `user_id = 0`; #196) |
| `test_ucp_register_set_data` | New user registration (user_id = 3) | Must call `sql_build_array('UPDATE', ...)` with all 5 default values, then `sql_query()` with `WHERE user_id = 3` — verified by mock expectations on the db object |

---

## 3. Page controller (`controller/page_controller_test.php`)

**What this code does:**
`controller/page_controller.php` serves two routes:
- `/rt` — the full standalone Recent Topics page, with full phpBB page chrome
- `/rt/simple` — a headerless version for embedding in iframes; tries to force the `pbwow3` style if it is installed

Both routes call `display_recent_topics()` (unless the feature is disabled) and then return an HTTP `Response` object via `helper->render()`.

**What this test file does:**
Creates the controller with a real `config` and mocks for db, helper, language, rt_functions, and user. The DB mock is needed for `display_simple()` because the controller queries the styles table to look up `pbwow3`.

| Test | Scenario | What failure means |
|------|----------|--------------------|
| `test_display_enabled` | `rt_page_enable` = 1 | `display_recent_topics()` called once; language loaded; `helper->render()` called with `recent_topics_page.html`; returns a `Response` object |
| `test_display_disabled` | `rt_page_enable` = 0 | `display_recent_topics()` must NOT be called; the page still renders (it is not a 404) but shows no topics |
| `test_display_simple` | `rt_page_enable` = 1; DB finds no pbwow3 style | `display_recent_topics()` called once; `helper->render()` called with `recent_topics_simple.html`; falls back gracefully when pbwow3 is not present |
| `test_display_simple_with_style_found` | DB returns a pbwow3 style row | `$user->style` must be set to the returned row — this is how phpBB switches the rendering style mid-request |

---

## 4. Event contract tests (`core/recenttopics_events_test.php`)

**What this code does:**
`core/recenttopics.php` is the query engine — ~900 lines that build SQL queries, run them, loop over the results, and fire the six public events documented in `contrib/events.md`. Other extensions depend on those events. A breaking change (renamed variable, missing argument, wrong firing order) would silently break any extension that listens to them.

**What this test file does:**
It verifies the *event contract*: that every event actually fires, carries the documented variables, and honours modifications made by listeners. To do this it builds a real (not mocked) event dispatcher and registers inline listener closures. It then instantiates `core\recenttopics` with mocked dependencies, calls the private methods under test via `ReflectionMethod`, and inspects what the listeners received.

Because `core\recenttopics` calls several phpBB global functions (`censor_text`, `topic_status`, `append_sid`, etc.) that are not available in the test bootstrap, the file also declares no-op function stubs for those.

**Why not just call `display_recent_topics()` from end to end?**
Because that method requires a fully seeded database (topics, forums, user sessions, permissions). Testing the events in isolation via the private methods is faster and more focused — the functional tests cover the full path.

| Test | Event | What it verifies |
|------|-------|-----------------|
| `test_sql_pull_topics_list_fires_with_sql_array` | `sql_pull_topics_list` | Event fires; `sql_array` key is present and is an array |
| `test_sql_pull_topics_list_modification_is_applied` | `sql_pull_topics_list` | A listener that sets `sql_array['LIMIT'] = 99` — that value is used in the query that follows |
| `test_sql_pull_topics_data_fires_with_sql_array` | `sql_pull_topics_data` | Event fires; `sql_array` key is present and is an array |
| `test_sql_pull_topics_data_modification_is_applied` | `sql_pull_topics_data` | A listener's modification to `sql_array` is visible to a listener on the legacy alias in the same cycle |
| `test_legacy_sql_pull_topics_data_fires_after_new_event` | `paybas.recenttopics.sql_pull_topics_data` | New event fires first, legacy alias fires second — order must be `['new', 'legacy']` |
| `test_modify_topics_list_fires_with_documented_variables` | `modify_topics_list` | Event fires; both `topic_list` and `rowset` keys are present and are arrays |
| `test_legacy_modify_topics_list_fires_after_new_event` | `paybas.recenttopics.modify_topics_list` | New event fires before legacy alias |
| `test_topictitle_remove_re_fires_with_row` | `topictitle_remove_re` | Event fires once per topic row; `row` key is present and is an array |
| `test_topictitle_remove_re_modification_is_applied` | `topictitle_remove_re` | A second listener can read the (already stripped) subject after the built-in listener runs |
| `test_modify_topictitle_fires_with_row_and_prefix` | `modify_topictitle` | Event fires; both `row` and `prefix` keys are present; `prefix` is a string |
| `test_modify_topictitle_prefix_is_applied_to_template_var` | `modify_topictitle` | A listener that sets `prefix = '[STICKY]'` — the `TOPIC_TITLE` template variable must start with `[STICKY]` |
| `test_modify_tpl_ary_fires_with_row_and_tpl_ary` | `modify_tpl_ary` | Event fires; both `row` and `tpl_ary` keys are present and are arrays |
| `test_modify_tpl_ary_contains_documented_template_variables` | `modify_tpl_ary` | All core template variables (`TOPIC_ID`, `FORUM_ID`, `TOPIC_TITLE`, `TOPIC_AUTHOR`, `FORUM_NAME`, `REPLIES`, `VIEWS`, `LAST_POST_TIME`, `LAST_POST_SUBJECT`, `LAST_POST_AUTHOR`, `U_VIEW_TOPIC`, `U_VIEW_FORUM`, `S_UNREAD_TOPIC`, `S_TOPIC_TYPE`, `TOPIC_LIKES`) are present in `tpl_ary` |
| `test_modify_tpl_ary_added_key_reaches_template` | `modify_tpl_ary` | A listener that adds `MY_EXTRA = 'hello'` — that key must be passed to `assign_block_vars()` |
| `test_legacy_modify_tpl_ary_fires_after_new_event` | `paybas.recenttopics.modify_tpl_ary` | New event fires before legacy alias |
| `test_legacy_modify_tpl_ary_sees_new_listener_modification` | `paybas.recenttopics.modify_tpl_ary` | A modification made by a listener on the new event is visible to a listener on the legacy alias |

---

## 5. Forum list (`core/forum_list_test.php`)

**What this code does:**
`get_forum_list()` in `core/recenttopics.php` decides which forums the list may take topics from: forums where the user has `f_read` or `f_list_topics`, minus passworded forums the user has not unlocked, minus forums the admin excluded in the ACP.

**What this test file does:**
It builds `core\recenttopics` with a mocked `auth` (returning the readable forums), a mocked `user` (returning the passworded forums not yet unlocked) and a db stub that answers the ACP-exclusion query with every forum it is asked about. It calls `get_forum_list()` via `ReflectionMethod` and reads the private `forum_ids`.

| Test | Scenario | What it verifies |
|------|----------|-----------------|
| `test_locked_passworded_forum_is_excluded` | Readable forums 1, 2, 3; forum 2 passworded and locked | Only 1 and 3 remain. phpBB grants `f_read` on a passworded forum regardless of the password, so without this the forum's titles and authors leak (#193) |
| `test_locked_passworded_forum_is_excluded_when_one_forum_remains` | Readable forums 1, 2; forum 2 locked | Only 1 remains, on the code path that skips the ACP-exclusion query |
| `test_forums_without_locked_password_are_kept` | Readable forums 1, 2, 3; none locked | All three remain |

---

## 6. Page count (`core/topic_count_test.php`)

**What this code does:**
With "Show all recent topic pages" (`rt_page_number`) on, `display_recent_topics()` counts the user's recent topics to set the page limit. The count must use the same forum list as the topic list itself.

**What this test file does:**
It runs `display_recent_topics()` with mocked `auth`, `user`, `db` and `content_visibility`, a real event dispatcher, and `rt_page_number = 1`. The `content_visibility` mock records the forum ids each topic query is scoped to.

| Test | Scenario | What it verifies |
|------|----------|-----------------|
| `test_page_count_query_uses_forum_list` | Readable forums 1 and 2; "Show all pages" on | Every topic query, including the page count, is scoped to forums 1 and 2. Before the fix the count ran before `get_forum_list()` and got `null`, so it only counted forums where the user has `m_approve` (#195) |

---

## 7. Like counts (`core/topic_likes_test.php`)

**What this code does:**
With Post Love installed, Recent Topics can show each topic's like count. `show_likes()` decides whether it does: the Post Love service must be present and the ACP "Show like counts" setting (`rt_show_likes`) switched on. It drives both the `S_POSTLOVE` template flag and whether the like counts are fetched at all.

**What this test file does:**
It runs `fill_template()` for one topic with a stand-in Post Love service that reports 7 likes and counts its calls. (The real service is optional and may not be installed, so it is not mocked by class.)

| Test | Scenario | What it verifies |
|------|----------|-----------------|
| `test_like_counts_hidden_when_setting_is_off` | `rt_show_likes = 0` | `TOPIC_LIKES` is 0, Post Love is never queried, `show_likes()` is false. Before the fix the setting was ignored (#197) |
| `test_like_counts_shown_when_setting_is_on` | `rt_show_likes = 1` | `TOPIC_LIKES` is 7, Post Love is queried once, `show_likes()` is true |

---

## 8. Who Is Online label (`language/viewonline_lang_test.php`)

**What this code does:**
When someone is on `/app.php/rt` or `/app.php/rt/simple`, the listener sets their Who Is Online location to `VIEWING_RECENT_TOPICS` and the link to the page. Core's `viewonline_body.html` wraps that label in its own `<a href="{U_FORUM_LOCATION}">`.

**What this test file does:**
It loads `recenttopics.php` from every language pack and checks that `VIEWING_RECENT_TOPICS` is plain text: no HTML and no `%` placeholder.

| Test | Scenario | What it verifies |
|------|----------|-----------------|
| `test_viewing_recent_topics_is_plain_text` | Data provider: one row per language pack | The label has no `<` and no `%`. The packs used to carry `<a href="%s">…</a>`, which nested a link with an unfilled `%s` inside core's link (#199) |

---

## 9. ACP template assets (`acp/acp_template_test.php`)

**What this code does:**
The ACP page shows a "Become a patron" button. The image ships with the extension (`adm/style/images/become_a_patron_button.png`), and the ACP module passes its URL to the template as `U_PATREON_BUTTON`.

**What this test file does:**
It reads the template and the image straight from disk.

| Test | Scenario | What it verifies |
|------|----------|-----------------|
| `test_acp_template_loads_no_external_patreon_image` | `adm/style/acp_recenttopics.html` | No `patreon.com/external` URL; the image uses `{{ U_PATREON_BUTTON }}`. Before the fix every ACP page view made the admin's browser call Patreon's CDN (#200) |
| `test_patreon_button_is_bundled` | `adm/style/images/become_a_patron_button.png` | The file exists and is a PNG |

---

## 10. Announcements first (`core/announcements_first_test.php`)

**What this code does:**
With the ACP setting "Show announcements first" (`rt_announcements_first`) on, `fill_template()` moves the announcements and global announcements on the current page to the top of that page. Only rows already on the page move, so old announcements never come back into the list; stickies stay with the normal topics.

**What this test file does:**
It runs `fill_template()` on one page of four topics in time order (normal, announcement, sticky, global announcement) and records the order in which they reach `assign_block_vars()`.

| Test | Scenario | What it verifies |
|------|----------|-----------------|
| `test_announcements_lead_the_page_when_setting_is_on` | Setting on | Order is announcement, global announcement, normal, sticky: announcements first in their time order, the rest unchanged (#201) |
| `test_page_keeps_time_order_when_setting_is_off` | Setting off (default) | Order is unchanged |

---

## 11. Template variables (`template/template_vars_test.php`)

**What this code does:**
The topic-list templates in `styles/*/template/` render a hidden screen-reader label inside each last-post link.

**What this test file does:**
It reads every `.html` template the extension ships (one data-provider row per file) and checks it for variables the PHP never assigns.

| Test | Scenario | What it verifies |
|------|----------|-----------------|
| `test_no_unassigned_view_latest_post` | Every template file | No `VIEW_LATEST_POST`. It was never assigned, so the last-post link's screen-reader label rendered empty; the templates now use `lang('GOTO_LAST_POST')`, matching the link's title (#215) |

---

## 12. Functional tests (`functional/recenttopics_test.php`)

**What this code does:**
These tests start a real phpBB installation (using the test framework's built-in install), enable the extension, and make HTTP requests using a real browser-like crawler (Symfony DomCrawler). They check the actual rendered HTML for specific elements.

Functional tests are slow — they each involve HTTP round-trips and database queries. But they catch entire classes of bug that unit tests cannot: misconfigured services, broken Twig templates, missing migrations, and routing problems.

**What this test file does:**
Logs in as the admin account, optionally creates topics or changes config values, makes a `GET` request, and asserts that the response contains the expected HTML elements.

| Test | Scenario | What is checked |
|------|----------|----------------|
| `test_rt_page` | `rt_page_enable` = 1; GET `/app.php/rt` | `<a id="recent-topics">` is present; `<title>` contains "Recent Topics" |
| `test_rt_simple_page` | `rt_page_enable` = 1; GET `/app.php/rt/simple` | `<a id="recent-topics">` is present |
| `test_rt_page_disabled` | `rt_page_enable` = 0; GET `/app.php/rt` | Page still loads (no 500 error); `<a id="recent-topics">` is present; `#recent-topics-box` must NOT appear |
| `test_index_has_recent_topics` | `rt_index` = 1; create a topic; GET `index.php` | `<a id="recent-topics">` is present; `#recent-topics-box` contains the created topic title |
| `test_acp_patreon_button_is_local` | Logged in as admin; GET the Recent Topics ACP page | The Patreon button's `src` is the bundled image, not `patreon.com`, and that image URL returns 200 (#200) |
| `test_announcements_first_on_index` | `rt_announcements_first` = 1; create an announcement, then a newer normal topic; GET `index.php` | The announcement is listed above the newer normal topic in `#recent-topics-box` (#201) |
| `test_acp_saves_announcements_first` | Admin ticks "Show announcements first" in the ACP and submits | `rt_announcements_first` is stored as 1 (#201) |
| `test_ucp_preferences` | Logged in as admin; GET `/ucp.php?i=ucp_prefs&mode=view` | `input[name="rt_enable"]` and `input[name="rt_number"]` are present on the preferences page |

---

## What is NOT tested (and why)

- **`core/recenttopics.php` (the query builder itself)**: The private methods that build SQL are tested indirectly via the event contract tests (which inject and observe modifications) and the functional tests (which verify the final rendered output). Dedicated query-builder unit tests would require mocking phpBB's content visibility API in detail and are deferred until the query logic is refactored into smaller, independently testable pieces.
- **Migrations**: The test framework runs migrations automatically as part of the functional test setup. A broken migration causes the functional tests to fail to start — so migrations are tested implicitly.
- **ACP module**: The ACP module (`acp/recenttopics_module.php`) delegates to phpBB's module framework. Testing it requires a full admin session. It is better covered by manual testing or a dedicated ACP functional test in a future iteration.
- **Language files**: These are static key-value arrays. Typos are caught by visual inspection, not unit tests.

---

## Running locally

```bash
# Unit and integration tests only (fast)
phpunit --configuration ext/avathar/recenttopics/phpunit.xml.dist --testsuite "Extension Test Suite"

# Functional tests (requires a configured test database)
phpunit --configuration ext/avathar/recenttopics/phpunit.xml.dist --testsuite "Extension Functional Tests"
```
