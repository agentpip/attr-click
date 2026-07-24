# attr.click Apache cutover

## Architecture

Caddy continues to own public HTTP/TLS and reverse-proxies `attr.click` to
`127.0.0.1:8092`. Apache's system service handles PHP through the installed
`mod_php` worker pool. The former `attr-click.service` user unit must not run
concurrently because it owns that loopback port with `php artisan serve`.

## One-time privileged preparation

Run these commands **on `UMacBookPro` as an administrator**, from the deployed
application checkout. They install and enable the Apache fragments but do not
reload Apache yet, so the existing Laravel development server remains live.

```sh
sudo install -m 0644 deploy/apache/attr-click-listen.conf /etc/apache2/conf-available/attr-click-listen.conf
sudo install -m 0644 deploy/apache/attr-click.conf /etc/apache2/sites-available/attr-click.conf
sudo a2enconf attr-click-listen
sudo a2ensite attr-click
sudo chgrp www-data /home/michael/Sites/attr.click/.env
sudo chmod 640 /home/michael/Sites/attr.click/.env
sudo setfacl -R -m u:www-data:rwX /home/michael/Sites/attr.click/storage /home/michael/Sites/attr.click/bootstrap/cache
sudo setfacl -m d:u:www-data:rwx /home/michael/Sites/attr.click/storage /home/michael/Sites/attr.click/bootstrap/cache
sudo apache2ctl configtest
```

The `.env` file is owned by Michael and readable only by its `www-data` group,
so Apache can load the application key and production connection settings
without exposing them to other local users. The runtime ACL grants Apache write
access only to Laravel runtime state. Deployment normalizes non-secret source
directories to be readable by Apache; it does not loosen permissions on `.env`
or cached configuration. The production connection uses MySQL, so the
application `database/` source directory is not made writable.

## Cutover and rollback

Only run this after privileged preparation reports `Syntax OK`.

```sh
# Stop the old loopback listener, then let Apache claim its unchanged port.
systemctl --user stop attr-click.service
sudo systemctl reload apache2
curl -fsS -H 'Host: attr.click' http://127.0.0.1:8092/up

# Once the health check is 200, retire the obsolete development-server unit.
systemctl --user disable attr-click.service
rm -f ~/.config/systemd/user/attr-click.service
systemctl --user daemon-reload
```

If the Apache reload or health check fails, restore service immediately:

```sh
sudo a2dissite attr-click
sudo a2disconf attr-click-listen
sudo systemctl reload apache2
systemctl --user start attr-click.service
```

Caddy is unchanged throughout: it retains certificate management, public ports,
compression, and the `attr.click -> 127.0.0.1:8092` route.
