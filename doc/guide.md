# Advanced guide

## Album settings

You can specify few settings for an album by creating a **config.php** file within its folder:

```php
<?php
  
// Allows lazy loading on images (only load pictures when visible).
$lazy_loading = true;

// Theme of the album.
// Available:
//  - default
$theme = "default";

// Title of the album.
$title = "My album";

?>
```

## Serve multiple albums at once

Use this method if you wish to launch Expo to multiple albums from a specific folder.

Let's say we have...

- `/var/www/photos`
- `/var/www/photos/album1`
- `/var/www/photos/album2`

and you wish to launch Expo when the client goes to `/photos/album1` and `/photos/album2`.

```sh
# Clone Expo somewhere in your web server.
git clone --depth 1 https://github.com/cisoun/Expo /var/www/expo

# Move to photos folder.
mv /var/www/photos
```

Now create the redirections:

**For Apache:**

> [!NOTE]
> This requires the **mod_rewrite** Apache module.

Create `/var/www/photos/.htaccess`:

```sh
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule /(.*) /var/www/expo/index.php [END]
```

Now you should see your gallery when opening an album from  `/photos`.
