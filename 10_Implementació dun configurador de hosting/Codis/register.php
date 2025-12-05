<VirtualHost *:80>
    ServerName junyMIH.cat
    ServerAlias www.junyMIH.cat

    RewriteEngine On

    RewriteCond %{HTTP_HOST} ^www\.junyMIH\.cat$ [NC]
    RewriteRule ^(.*)$ http://www.sapalomera.cat/$1 [R=301,L]

    RewriteCond %{HTTP_HOST} ^junyMIH\.cat$ [NC]
    RewriteRule ^(.*)$ http://recujunyMIH.com/$1 [R=301,L]
</VirtualHost>
