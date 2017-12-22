# Creode Server Management Tool

A tool that sits within your existing site, allows backups (databases, directories) to be created, zipped and pushed to AWS S3.

Actions are triggered by a remote management system, allowing a single dashboard to monitor and trigger backups for a suite of websites.

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

And the following .htaccess to prevent web access to your config file

```
<files csmt.yml>
 order allow,deny
 deny from all
</files>
```

Make sure you also add your directory to your `robots.txt` file (in the root of your site) so that it doesn't get crawled.
