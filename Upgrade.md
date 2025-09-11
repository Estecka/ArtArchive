# Upgrade guide

For most upgrades, simply replacing the files that changed should be enough.
However some upgrades require certain files to be deleted, and in the futur, require changes to be made on the database.
This guide will keep track of what kind of changes are made between versions, and how to perform them.

In order to update the files, you _could_ just dump this entire repo into your website, but mind this this will overwrite you configuration files.  
To do it with more finesse, and only replace the files that need to be, clone the repo, and run this command :  
`git archive <target_version> -o patch.zip $(git diff <old_version> <target_version> --name-only --no-renames --diff-filter=MA)`  
then unzip the resulting "patch.zip" file into your website.  
A handful of pre-made patches can be found attached to the appropriate [releases](https://github.com/Estecka/ArtArchive/releases).

As files are renamed or moved around, some files in your website may become unused. Leaving them should be benign in most case. In order to clean them up, you can get the list of files to delete by running this command in the repo :  
`git diff <old_version> <target_version> --name-only --no-renames --diff-filter=D`

The guide below will tell you exactly what needs to be done for each version upgrade.

## 0.8.0
From [`0.2.1`](#021) or above :  
Delete everything in `public_html/css/`  
**Then** unzip patch  
Make sure not to overwrite [`/auth/config.php`](/auth/config.php) in the process.  

## 0.7.0
From [`0.3.1`](#031) or above :  
Unzip patch.  
Make sure not to overwrite [`/auth/config.php`](/auth/config.php) in the process.  
Delete `public_html/css/socialLinks.css`

## 0.4.0 - 0.6.1
From [`0.3.1`](#031) or above :  
Unzip patch only

## 0.3.1
From [`0.2.1`](#021) or [`0.3.0`](#030) :  
Unzip patch.  
In `/public_html/css/`, delete `layout.css` and `colors.css`.

### 0.3.0
From [`0.2.1`](#021)  
Unzip patch only.  

## 0.2.1
From [`0.1.x`](#010) or [`0.2.0`](#020) :  
Unzip patch.  
Make sure not to overwrite [`/auth/config.php`](/auth/config.php) in the process.  
**Move all files from `/site/storage/` into `/public_html/storage/`.** Then delete the `/site/` folder completely.  

#### 0.2.0
From [`0.1.x`](#010) :  
Unzip patch only.

#### 0.1.1
From [`0.1.0`](#010) :  
Unzip patch only.

### 0.1.0
Original release, fresh installation only.
