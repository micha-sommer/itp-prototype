useful commands:
deutsch:
```shell
php bin/console translation:extract --force --format=yaml --as-tree=9 de
```
englisch:
```shell
php bin/console translation:extract --force --format=yaml --as-tree=9 en
```

Send mails:
```shell
php bin/console messenger:consume async -vv
```
