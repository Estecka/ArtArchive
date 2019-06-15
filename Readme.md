## Introduction
**ArtArchive** is a single-user gallery for PHP and MySQL, inspired by [Danbooru](https://safebooru.donmai.us)'s tagging system.

Note that this software was primarily developed for my own personal usage. One feature that it will notably lack and that I don't plan on implementing yet is the ability to upload medias directly from the website's interface. All uploads shall be done via a FTP client.

## Requirements
The server configuration used during developpement is:
- PHP: 7.0.10
- MySQL: 5.7.14
- Apache: 2.4.23

You need to know how to : 
- upload files via FTP (both for installation *and* usage)
- create/configure a database.

Not required, but could come handy in a pinch:
- manage a database (preferably via PhpMyAdmin), 
- run SQL queries on the database.


## Features
- Attach multiple files to a single artwork.
- Manage numerous tags, separate tags into categories.
- Description page for each and every tag and category
- Multimedia artworks :
  - image
  - audio
  - documents (txt, pdf, html)

#### Planned features :
- Subscription via RSS feed
- Disqus comments

## Installation
- [Download](https://github.com/Estecka/ArtArchive/releases/tag/0.1.0) and unzip the software archive into some folder.
- Open [`database\config.php`](database/config.php) in a text editor, and fill in your database information.
- Open [`auth\config.php`](auth/config.php) in a text editor, and fill in the credentials you want to use for admin rights.
- Upload the whole folder onto your website via FTP.
- In your web-broswer, navigate to your website and follow the Installation Wizard. (This require the credentials configured earlier.)

Once the installation is complete, it is best that you delete the [`site\webmaster\databasewizard\`](site/webmaster/databasewizard/) folder from the site.
