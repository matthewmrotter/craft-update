<?php

$vendorDir = dirname(__DIR__);

return array (
  'mmikkel/cp-field-inspect' => 
  array (
    'class' => 'mmikkel\\cpfieldinspect\\CpFieldInspect',
    'basePath' => $vendorDir . '/mmikkel/cp-field-inspect/src',
    'handle' => 'cp-field-inspect',
    'aliases' => 
    array (
      '@mmikkel/cpfieldinspect' => $vendorDir . '/mmikkel/cp-field-inspect/src',
    ),
    'name' => 'CP Field Inspect',
    'version' => '1.0.5',
    'schemaVersion' => '1.0.0',
    'description' => 'Inspect field handles and easily edit field settings',
    'developer' => 'Mats Mikkel Rummelhoff',
    'developerUrl' => 'http://mmikkel.no',
    'documentationUrl' => 'https://github.com/mmikkel/CpFieldInspect-Craft/blob/master/README.md',
    'changelogUrl' => 'https://raw.githubusercontent.com/mmikkel/CpFieldInspect-Craft/master/CHANGELOG.md',
    'hasCpSettings' => false,
    'hasCpSection' => false,
  ),
  'dolphiq/redirect' => 
  array (
    'class' => 'dolphiq\\redirect\\RedirectPlugin',
    'basePath' => $vendorDir . '/dolphiq/redirect/src',
    'handle' => 'redirect',
    'aliases' => 
    array (
      '@dolphiq/redirect' => $vendorDir . '/dolphiq/redirect/src',
    ),
    'name' => 'Redirect plugin for Craft 3',
    'version' => '1.0.18',
    'schemaVersion' => '1.0.4',
    'description' => 'Craft redirect plugin provides an easy way to enter and maintain 301 and 302 redirects and 404 error pages.',
    'developer' => 'Dolphiq',
    'developerUrl' => 'https://dolphiq.nl/',
    'documentationUrl' => 'https://github.com/Dolphiq/craft3-plugin-redirect/blob/master/README.md',
    'changelogUrl' => 'https://raw.githubusercontent.com/Dolphiq/craft3-plugin-redirect/master/CHANGELOG.md',
    'hasCpSettings' => true,
    'hasCpSection' => true,
  ),
  'solspace/craft3-freeform' => 
  array (
    'class' => 'Solspace\\Freeform\\Freeform',
    'basePath' => $vendorDir . '/solspace/craft3-freeform/src',
    'handle' => 'freeform',
    'aliases' => 
    array (
      '@Solspace/Freeform' => $vendorDir . '/solspace/craft3-freeform/src',
    ),
    'name' => 'Freeform Lite',
    'version' => '2.5.7',
    'schemaVersion' => '2.1.3',
    'description' => 'The most intuitive and powerful form builder for Craft.',
    'developer' => 'Solspace',
    'developerUrl' => 'https://solspace.com/craft/freeform',
    'documentationUrl' => 'https://solspace.com/craft/freeform/docs',
    'changelogUrl' => 'https://raw.githubusercontent.com/solspace/craft3-freeform/master/CHANGELOG.md',
    'hasCpSection' => true,
  ),
  'verbb/super-table' => 
  array (
    'class' => 'verbb\\supertable\\SuperTable',
    'basePath' => $vendorDir . '/verbb/super-table/src',
    'handle' => 'super-table',
    'aliases' => 
    array (
      '@verbb/supertable' => $vendorDir . '/verbb/super-table/src',
    ),
    'name' => 'Super Table',
    'version' => '2.0.14',
    'description' => 'Super-charge your Craft workflow with Super Table. Use it to group fields together or build complex Matrix-in-Matrix solutions.',
    'developer' => 'Verbb',
    'developerUrl' => 'https://verbb.io',
    'developerEmail' => 'support@verbb.io',
    'documentationUrl' => 'https://github.com/verbb/super-table',
    'changelogUrl' => 'https://raw.githubusercontent.com/verbb/super-table/craft-3/CHANGELOG.md',
  ),
  'craftcms/redactor' => 
  array (
    'class' => 'craft\\redactor\\Plugin',
    'basePath' => $vendorDir . '/craftcms/redactor/src',
    'handle' => 'redactor',
    'aliases' => 
    array (
      '@craft/redactor' => $vendorDir . '/craftcms/redactor/src',
    ),
    'name' => 'Redactor',
    'version' => '2.1.7',
    'description' => 'Edit rich text content in Craft CMS using Redactor by Imperavi.',
    'developer' => 'Pixel & Tonic',
    'developerUrl' => 'https://pixelandtonic.com/',
    'developerEmail' => 'support@craftcms.com',
    'documentationUrl' => 'https://github.com/craftcms/redactor',
  ),
  'luwes/craft3-codemirror' => 
  array (
    'class' => 'luwes\\codemirror\\CodeMirror',
    'basePath' => $vendorDir . '/luwes/craft3-codemirror/src',
    'handle' => 'code-mirror',
    'aliases' => 
    array (
      '@luwes/codemirror' => $vendorDir . '/luwes/craft3-codemirror/src',
    ),
    'name' => 'CodeMirror',
    'version' => '1.0.1',
    'description' => 'Add the awesome in-browser code editor CodeMirror as a field type.',
    'developer' => 'Wesley Luyten',
    'developerUrl' => 'https://wesleyluyten.com',
    'documentationUrl' => 'https://github.com/luwes/craft3-codemirror/blob/master/README.md',
    'changelogUrl' => 'https://raw.githubusercontent.com/luwes/craft3-codemirror/master/CHANGELOG.md',
  ),
  'nystudio107/craft-seomatic' => 
  array (
    'class' => 'nystudio107\\seomatic\\Seomatic',
    'basePath' => $vendorDir . '/nystudio107/craft-seomatic/src',
    'handle' => 'seomatic',
    'aliases' => 
    array (
      '@nystudio107/seomatic' => $vendorDir . '/nystudio107/craft-seomatic/src',
    ),
    'name' => 'SEOmatic',
    'version' => '3.1.44',
    'description' => 'SEOmatic facilitates modern SEO best practices & implementation for Craft CMS 3. It is a turnkey SEO system that is comprehensive, powerful, and flexible.',
    'developer' => 'nystudio107',
    'developerUrl' => 'https://nystudio107.com',
    'documentationUrl' => 'https://github.com/nystudio107/craft-seomatic/blob/v3/README.md',
    'changelogUrl' => 'https://raw.githubusercontent.com/nystudio107/craft-seomatic/v3/CHANGELOG.md',
    'hasCpSettings' => true,
    'hasCpSection' => true,
    'components' => 
    array (
      'frontendTemplates' => 'nystudio107\\seomatic\\services\\FrontendTemplates',
      'helper' => 'nystudio107\\seomatic\\services\\Helper',
      'jsonLd' => 'nystudio107\\seomatic\\services\\JsonLd',
      'link' => 'nystudio107\\seomatic\\services\\Link',
      'metaBundles' => 'nystudio107\\seomatic\\services\\MetaBundles',
      'metaContainers' => 'nystudio107\\seomatic\\services\\MetaContainers',
      'script' => 'nystudio107\\seomatic\\services\\Script',
      'sitemaps' => 'nystudio107\\seomatic\\services\\Sitemaps',
      'tag' => 'nystudio107\\seomatic\\services\\Tag',
      'title' => 'nystudio107\\seomatic\\services\\Title',
    ),
  ),
);
