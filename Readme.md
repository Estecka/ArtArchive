## Introduction
**ArtArchive** is a single-user gallery for PHP and MySQL, inspired by [Danbooru](https://safebooru.donmai.us)'s tagging system.  
It's main purpose is to serve as an organizer rather than a portfolio.

One feature that it will notably lacks as of now is the ability to upload medias directly from the website's interface. All uploads must be done via a FTP client.

## Requirements
The server configuration used during developpement is:
- PHP: 7.0.10
- MySQL: 5.7.14
- Apache: 2.4.23

You need to know how to upload files via FTP, for *both* installation *and* usage).  
Uploading files is not yet supported, for now you have to upload the files via FTP, and provide the files' url instead when creating an artwork.

## Features
- Attach multiple files to a single artwork.
- Manage numerous tags, separate tags into categories.
- Description page for each and every tag and category
- Multimedia artworks :
  - image
  - audio
  - documents (txt, pdf, html)

#### Planned features :
- Disqus comments
- Thumbnail generation

## Installation
This is for a fresh installation. To upgrade from an existing version, refer to the [upgrade guide](Upgrade.md).  

- [Download](https://github.com/Estecka/ArtArchive/releases/tag/0.1.0) and unzip the software archive into some folder.
- Open [`database\config.php`](database/config.php) in a text editor, and fill in your database information.
- Open [`auth\config.php`](auth/config.php) in a text editor, and fill in the credentials you want to use for admin rights.
- Upload the whole folder onto your server.
	- If possible, match this repo's `public_html` folder with the one on your server.
	- If your host forbids placing files outside of this folder, or does not offer this functionnality,
	just dump everything wheverver you're allowed to;
	it will be emulated using url-rewriting.
- In your web-broswer, navigate to your website and follow the Installation Wizard. (This require the credentials configured earlier.)
