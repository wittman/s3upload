# S3Upload Plugin (for [Piwigo](http://piwigo.org) photo gallery)

The S3Upload plugin uploads gallery photos to your configured AWS S3 storage account, automatically or by batch selection. 

Developed by Micah Wittman.

## S3Upload Plugin - What Does It Do, Exactly?

The **short answer**: When a photo is added to your Piwigo gallery, S3Upload plugin uploads the (full size) image to your Amazon S3 account, and when viewing an individual photo a download link to the S3 image is provided.

The **long answer**: When a photo is added to your Piwigo gallery (your database table holding the index of images is updated), S3Upload queues those newly added photos to be processed for uploading a full-size copy to your configured Amazon S3 bucket of choice.

When a queued image is confirmed successfully uploaded to S3, it is cleared from an index of queued files pending upload.

Any upload transfer attempts that fail are displayed in the "10 Most Recent Pending Uploads to S3" table in this tab. The status column indicates the error code. Common problems: `'failed_code_0'` likely means bad AWS bucket or location constraint. `'failed_code_403'` likely means bad AWS key and/or password. `'failed_code_404'` likely means bad bucket.

Once a bad configuration is corrected, the previously failed uploads should succeed and the failure records in the Pending table will clear and the (last 10) uploads will appear in the Success table.

Each S3 uploaded photo will (optional) have a download link in the Piwigo photo display info box.

2 methods of photo uploading are provided. Method 1#: Active Monitoring (while enabled) will queue and upload every photo that's added to the gallery *AFTER* the installation of the S3Upload plugin. Method #2: the Piwigo Batch Manager will have an S3Upload command in the dropdown—it queues for uploading files selected in the Batch Manager. The Batch manager method can be used to S3 upload photos that were added to gallery before the plugin was installed.

Photos added while Active Monitoring is disabled will not be queued/uploaded when monitoring is re-enabled. Use Batch Manager method to select any photos to be S3 uploaded.

## Requirements:

- Piwigo Version 2.5.0
- In the S3Upload plugin Configuration tab:
- Enter your Amazon Web Services (AWS) S3 storage bucket name.
- Enter AWS access key.
- Enter AWS secret key.
- If you are not familiar with the AWS service, be aware that typically Amazon charges (fraction of a penny) for every file transfer and for file storage per period of time, and other requests. As of this writing there is an AWS free tier offering (5 GB of Amazon S3 standard storage, 20,000 Get Requests, and 2,000 Put Requests)

## Limitations:

- S3 Uploads Are One-time / One-way
- Once a photo is queued and uploaded, edits, or any other changes to the image stored in Piwigo *do not* affect the S3 image.
- Deleting a photo from Piwigo *does not* remove or affect the S3 image in any way.
- Any image uploaded more than once to the same location (AWS account + bucket + bucket path + file name [per config setting]) will *not* be re-uploaded / over-written.
Notes:
- If the Prevent duplicate uploads (by md5 hash) S3Upload option is enabled, the plugin will not S3 upload an image that is pixel for pixel a duplicate of a previously queued/uploaded image (regardless of the S3 location of the existing upload).
AWS Bucket

## Licensing / Credits

Special thanks to the Piwigo team / extension developer community, especially J.Commelin and mistic100, for the Skeleton plugin (scaffolding code to build new plugins), upon which this S3Upload was bootstrapped.

Copyright (c) 2013 Micah Wittman <wittman.org>
Dual licensed under the MIT and GPL licenses.