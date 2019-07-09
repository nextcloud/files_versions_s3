# files_versions_s3

Use S3 object versioning for file versioning

## Warning

This app does not include any mechanism for expiring old s3 versions,
you should setup your own version expiry (also called "Lifecycle management" in S3)
to prevent versions from taking up an ever increasing amount of space. 

## Usage

- install the app
- check if bucket versioning is enabled for your storage using `occ files_versions_s3:status`
- enable bucket versioning if not yet enabled using `occ files_versions_s3:enable <id>`
