# Creode Server Monitoring Tool

A tool that sits within your existing site, allows backups (databases, directories) to be created, zipped and pushed to external storage (e.g. AWS S3)

Actions are triggered by a remote management system, allowing a single dashboard to monitor and trigger backups for a suite of websites.

### Installation on mac/linux
This will install the phar to the current directory
```
curl -s https://raw.githubusercontent.com/creode/csmt/master/install.sh | bash -s
```

## Example Usage
Run the installation command in a web-accessible directory of your choice. Initially the install will be open to the world, but performing a handshake (via the dashboard) will setup the Basic Auth security.

The install will download a `csmt.yml.example` file containing an example configuration for this site. You should copy this to `csmt.yml` and configure for this particular site.

### Example configuration file

```
environment: test
project:
 name: Basis_EN
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
  destination: media/en_files.zip
  storage: 
   s3:
    access: PASTEACCESSKEYHERE
    secret: PASTESECRETKEYHERE
    bucket: example.creode.client
    region: eu-west-2
```


### What are the config settings?
#### environment
This is 'live' or 'test' - the tool will restrict the commands that can be run per environment in order to prevent you overriding a live DB with a test one (for example)

#### project.name
This will be used for directory naming when creating temporary files - ensure it is directory friendly and does not include any spaces

#### databases.xxxx
Each node under 'databases' refers to its own database. You can configure multiple databases on multiple hosts to be a part of the same backup process.
##### databases.xxxx.filename
This is the name of the file that will be created on the server as part of the dump
##### databases.xxxx.destination
This is the path that the file will be placed in the off-server storage
##### databases.xxxx.storage
These are the credentials for the off-server storage, currently only AWS is supported. All config options are shown in the example above.

#### filesystem.xxxx
As with databases, each node under 'filesystem' refers to its own directory.
##### filesystem.xxxx.parentdir
In order to avoid zipping up a sub-sub-sub directory you need to specify the parent dir. The app will effectively `cd $parentdir` before creating a zip.
##### filesystem.xxxx.dir
The directory to be zipped, no slashes
##### filesystem.xxxx.filename
This is the name of the file that will be created on the server as part of the dump
##### filesystem.xxxx.destination
This is the path that the file will be placed in the off-server storage
##### filesystem.xxxx.storage
These are the credentials for the off-server storage, currently only AWS is supported. All config options are shown in the example above.


 
## AWS IAM Access levels
The user should have the ability to read and write to the bucket that you specify.

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
