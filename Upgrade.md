# Upgrade guide

For most upgrades, simply replacing the files that changed should be enough.

In the futur, deleting files and/or manipulating the database may be required. This guide will keep track of what kind of changes are made between versions, and how to perform them.

In order to update the files, you _could_ just dump this entire repo into your website, but mind this this will overwrite you configuration files.  
To do it with more finesse, and only replace the files that need to be, clone the repo, and run this command :  
`git archive <target_version> -o patch.zip $(git diff <your_version> <target_version> --name-only)`  
then unzip the resulting "patch.zip" file into your website.  
A handful of pre-made patches can be found attached to the appropriate [releases](https://github.com/Estecka/ArtArchive/releases).


## 0.2.0
From [`0.1.x`](#010)  
Update files only.

### 0.1.1
From [`0.1.0`](#010)  
Update files only.

## 0.1.0
Original release, fresh installation only.
