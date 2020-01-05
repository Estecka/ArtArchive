# Upgrade guide

For most upgrades, simply replacing the files that changed should be enough.
However some of them require certain files to be deleted, and/or changes to be made on the database. 
This guide will keep track of what kind of changes are made between versions, and how to perform them.

In order to update the files, you _could_ just dump this entire repo into your website, but mind this this will overwrite you configuration files.  
To do it with more finesse, and only replace the files that need to be, clone the repo, and run this command :  
`git archive <target_version> -o patch.zip $(git diff <your_version> <target_version> --name-only --diff-filter=MAR)`  
then unzip the resulting "patch.zip" file into your website.  
A handful of pre-made patches can be found attached to the appropriate [releases](https://github.com/Estecka/ArtArchive/releases).

## 0.3.1
From [`0.2.1`](#021) or [`0.3.0`](#030)  
Update files.  
In `/public_html/css/`, delete `layout.css` and `colors.css`.

### 0.3.0
From [`0.2.1`](#021)  
Update files.  

## 0.2.1
From [`0.1.x`](#010) or [`0.2.0`](#020)  
Update files.  
Make sure not to overwrite [`/auth/config.php`](/auth/config.php) in the process.
**Move all files from `/site/storage/` into `/public_html/storage/`.**  
Delete the `/site/` folder completely.  

#### 0.2.0
From [`0.1.x`](#010)  
Update files only.

#### 0.1.1
From [`0.1.0`](#010)  
Update files only.

### 0.1.0
Original release, fresh installation only.
