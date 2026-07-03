<?php
/* Per-site mail delivery config — generated at stamp time. NOT committed to
   any repo (the whole output tree this file lives under is gitignored) —
   if you switch to SMTP below, this holds a real mailbox password.

   Default: PHP's built-in mail() — zero setup, works out of the box on
   most hosting, but deliverability can be weak without SPF/DKIM configured
   for the sending domain (mail may land in spam).

   To switch to SMTP (recommended once you have real credentials — Gmail,
   Office365, or your host's own mailbox — for far better deliverability):
   set 'method' to 'smtp' and fill in the smtp block below. No code changes
   needed anywhere else — lib/mail.php reads this file at send time.
*/
return [
  'method' => 'phpmail',   // 'phpmail' | 'smtp'
  'to'     => null,        // null = use business.email from content.json
  'smtp'   => [
    'host'   => '',
    'port'   => 587,
    'secure' => 'tls',     // 'tls' | 'ssl' | 'none'
    'user'   => '',
    'pass'   => '',
    'from'   => '',        // usually must match 'user' for the provider to accept it
  ],
];
