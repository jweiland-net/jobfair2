:navigation-title: Administrator manual

..  include:: /Includes.rst.txt


..  _admin-manual:

====================
Administrator manual
====================

Target group: **Administrators / Integrators**

This manual describes how to install and configure Jobboard: activating the
extension, including its Site Sets, and setting the storage folder and page
IDs the extension needs.


..  _admin-manual-installation:

Installation
============

#.  Require the extension via Composer:

    ..  code-block:: bash

        composer require jweiland/jobboard

#.  Activate the extension, either via the :guilabel:`Extensions` module in
    the TYPO3 backend, or via the command line:

    ..  code-block:: bash

        vendor/bin/typo3 extension:setup

#.  Open the :guilabel:`Site Configuration` module for the site the jobs
    should appear on, and add the Site Set :guilabel:`Jobboard - Main`
    (`jweiland/jobboard`). It automatically pulls in `jweiland/maps2-default`,
    `jweiland/maps2-googlemaps` and `jweiland/jobboard-pdf`.

#.  Create a storage folder page for job, job area and job type records, and
    configure it below.

..  figure:: ../Images/AdministratorManual/ExtensionManager.png
    :width: 500px
    :alt: TYPO3 Extensions module listing installed extensions
    :zoom: lightbox

    The :guilabel:`Extensions` module, where Jobboard can be activated.


..  _admin-manual-settings:

Site settings
=============

The following settings are available once the Site Set has been added to a
site and can be adjusted per site under :guilabel:`Site Configuration >
Settings`:

..  typo3:site-set-settings:: PROJECT:/Configuration/Sets/Jobboard/settings.definitions.yaml
    :name: jobboard-site-settings


..  _admin-manual-address-search-scope:

Address search and page scoping
================================

The city/zip field of the frontend search form is not a plain text input -
while the visitor types, it queries :t3ext:`tt_address` for autocomplete
suggestions through
:php:class:`\JWeiland\Jobboard\Middleware\AddressSearchMiddleware`. This
search deliberately never learns which storage pages it may look at from
the request itself.

..  important::
    :t3ext:`tt_address` is a shared table. On many installations it also
    stores addresses that have nothing to do with Jobboard - other
    address book entries, internal contacts, or otherwise personal data
    outside the scope of this extension. Without a page restriction, the
    autocomplete search would work as an unauthenticated lookup across
    every visible :t3ext:`tt_address` record on the whole site, not just
    job locations.

To avoid this, the storage pages are never sent through a template, a
`data-*` attribute or JavaScript as part of the request - any value that
travels through the browser like that can be rewritten by whoever controls
the client, no matter how it got there. Instead,
:php:class:`\JWeiland\Jobboard\Middleware\AddressSearchMiddleware` reads
`jobboard.storagePid` directly from the current site's Site Settings via
the PSR-7 request
(:php:`$request->getAttribute('site')->getSettings()`), the very setting
configured above under :ref:`admin-manual-settings`. TYPO3's
:php:class:`\TYPO3\CMS\Frontend\Middleware\SiteResolver` runs very early
in the request stack, well before Jobboard's own middleware, so the site
and its settings are already available - no extra transfer of the page ID
is needed, and nothing about the search scope can be influenced by the
request.

..  note::
    This is also why `jobboard.storagePid` is a comma-separated list of
    page IDs (`type: string`) rather than a single page picker. If
    :t3ext:`tt_address` records used for job locations live in more than
    one folder, list every additional page ID directly in this setting
    under :guilabel:`Site Configuration > Settings`, e.g. `0,34,67`.
    Extending `plugin.tx_jobboard.persistence.storagePid` via TypoScript
    instead would only narrow down the job list/search, not this
    middleware - it never parses TypoScript, only Site Settings.


..  _admin-manual-import:

Importing jobs automatically
============================

Jobboard does not ship with a preconfigured job source. To pull job postings
from an external API on a schedule, implement one small PHP class and
register a TYPO3 :guilabel:`Scheduler` task that runs the console command
described in :ref:`admin-api`. That page walks through building such a
class step by step.

..  toctree::
    :maxdepth: 2
    :titlesonly:
    :hidden:

    Api/Index
