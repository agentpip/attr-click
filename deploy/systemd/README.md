# Legacy attr-click user service

`attr-click.service` currently runs Laravel's development server on
`127.0.0.1:8092`. After the Apache cutover in `../apache/cutover.md` passes its
loopback health check, disable and remove that user unit. Apache's existing
system service becomes the durable PHP runtime; no replacement user unit is
required.
