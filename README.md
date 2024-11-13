# ⏳ Expiration Actions

A WordPress plugin to specify expiration datetime and action for any post type.

### Setup

Navigate to `Settings` / `Expiration Actions` menu to select target post types and update default shortcode text

### Usage

1. Open any document of type selected during the **Setup** process
2. Specify Redirect or Shortcode action
3. Make sure that the metabox is enabled for the document you are currently editing

### Actions

If both are specified then Redirect has a precedence

- Redirect (status **302**)
    - To enable redirect just specify a url
- Shortcode
    - Make sure the redirect url is empty
    - Just use the following shortcode`[expiration_message]Shortcode Text[/expiration_message]`

### Editor Modes

Expiration Actions metabox disabled by default. Use `Enable` checkbox to enable plugin for a specific document

- Classic mode
    - Place a shortcode using **Text** editor tab
- Gutenberg mode
    - Use default **Shortcode** or **CustomHTML** blocks and place the needed content inside

### 2 levels of shortcode rewriting

- Global level (inside `Settings` / `Expiration Actions` menu) - to rewrite default shortcode text across the site
- Shortcode level - to rewrite default and Global level text for a specific shortcode
- Default shortcode text (hard coded within the plugin)

### Wrap with HTML

Your can wrap the shortcode with needed HTML to apply any markup and styling

### Examples

- `[expiration_message]Shortcode text[/expiration_message]` - will output `Shortcode text`
- `[expiration_message][/expiration_message]` or `[expiration_message]` - will output text (if it exists)
  from `Settings` / `Expiration Actions` tab or default text otherwise
- `<div style='color:red;'><h1>[expiration_message]Shortcode Block[/expiration_message]</h1></div>`

### Potential problems

- Your custom post types should support 'custom-fields' to make this plugin works in Gutenberg