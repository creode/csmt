# Creode Server Monitoring Tool

A tool that sits within your existing site, allows backups (databases, directories) to be created, zipped and pushed to external storage (e.g. AWS S3)

Actions are triggered by a remote management system, allowing a single dashboard to monitor and trigger backups for a suite of websites.

### Installation on mac/linux
This will install the phar to the current directory
```
curl -s https://raw.githubusercontent.com/creode/csmt/master/install.sh | bash -s
```

## Example Usage
Drop the csmt.phar file into a web-accessible directory of your choice
In the same directory, create an `index.php` file with the following contents:

```
<?php

$command = isset($_GET['command']) ? $_GET['command'] : '--version';


exec('php csmt.phar ' . escapeshellarg($command), $output, $return_var);

foreach($output as $out) {
    echo $out . PHP_EOL;
}
```

Also in the same directory create a `csmt.yml` file containing the configuration for this site, example structure:

```
databases:
 website:
  host: mysql
  name: website
  pass: webpassword
  user: webuser
  filename: website.sql
  destination: databases/website.sql
  storage: 
   s3:
    access: PASTEACCESSKEYHERE
    secret: PASTESECRETKEYHERE
    bucket: example.creode.client
    region: eu-west-2
 wordpress:
  host: mysql
  name: website
  pass: webpassword
  user: webuser
  filename: wordpress.sql
  destination: databases/wordpress.sql
  storage: 
   s3:
    access: PASTEACCESSKEYHERE
    secret: PASTESECRETKEYHERE
    bucket: example.creode.client
    region: eu-west-2
filesystem:
 en_files:
  parentdir: /var/www/html
  dir: files
  filename: en_files.zip
  destination: databases/wordpress.sql
  storage: 
   s3:
    access: PASTEACCESSKEYHERE
    secret: PASTESECRETKEYHERE
    bucket: example.creode.client
    region: eu-west-2
```


## Securing the tool
The simplest way to secure the tool and config is to perform a handshake from your dashboard. This will automatically setup the htpasswd and htaccess files you need.

If you prefer to set this up yourself then add the following .htaccess to prevent web access to your config file

```
AuthType Basic
AuthName "Password Protected Area"
AuthUserFile /somewhere/outside/web/root/.htpasswd
Require valid-user

<files csmt.yml>
 order allow,deny
 deny from all
</files>
```

You can generate your .htpasswd using a tool like http://www.htaccesstools.com/htpasswd-generator/


## Search Engines
Make sure you add your directory to your `robots.txt` file (in the root of your site) so that it doesn't get crawled.
