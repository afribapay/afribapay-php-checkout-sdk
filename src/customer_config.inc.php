<?php

return [
    // The user identifier for the API, typically a public key.
    'apiUser' => 'pk_sandbox_261012052931cqRn9OC',

    // The secret key for authenticating API requests, keep this secure.
    'apiKey' => 'sk_sandbox_AFP121026ZGBtRpjREmKs1ansIQIw',

    // The merchant key, specific to the account of the merchant using the API.
    'merchantKey' => 'mk_sandbox_2610120529319E',

    // The unique identifier for the agent, used to track transactions or activities.
    'agent_id' => 'APM31923613',

    // Specifies the environment where the API is running: 'production' for live usage, or 'sandbox' for testing.
    'environment' => 'sandbox',

    // Sets the language for API responses, here 'fr' for French.
    'lang' => 'en',

    // Bootstrap CSS used by the payment buttons:
    //  - 'auto'  : load from public CDNs with automatic browser-side fallback (default)
    //  - string  : your own URL (self-hosted file or another CDN)
    //  - false   : do not inject anything (your site already loads Bootstrap 5)
    'bootstrapCss' => 'auto',

];


