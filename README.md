# Youtube Dl
Youtube downloader app for OwnCloud

Place **youtubedl** folder in **owncloud/apps/**

# Requirements
* youtube-dl (For download youtube videos. See [http://rg3.github.io/youtube-dl/download.html](http://rg3.github.io/youtube-dl/download.html) for installing
* avconv (Required for converting downloaded file to mp3. Install with *sudo apt-get install libav-tools* )

# TODO
* Rename downloaded file's name.
* Default settings. (Such as default folder)
* Cache for directory listing
* Use OwnCloud API instead of Symfony Proccess for removing old files.