<?php
/*
* Copyright 2014 Baidu, Inc.
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/

namespace BaiduBce\Services\Bos;


class BosOptions
{
    // Common options
    const CONFIG = 'config';

    // Options for generatePreSignedUrl
    const SIGN_OPTIONS = 'signOptions';
    const HEADERS = 'headers';
    const PARAMS = 'params';

    const ACCESS_KEY_ID = 'accessKeyId';
    const ACCESS_KEY_SECRET = 'accessKeySecret';
    const ENDPOINT = 'endpoint';
    const CHARSET = 'charset';
    const DATE = 'date';
    const ETAG = 'etag';
    const LAST_MODIFIED = 'lastModified';

    const BUCKET = 'bucket';
    const OBJECT = 'object';

    const RANGE = 'range';

    const OBJECT_CONTENT_STRING = 'objectContentString';
    const OBJECT_CONTENT_STREAM = 'objectDataStream';

    const OBJECT_COPY_SOURCE = 'copySource';
    const OBJECT_COPY_SOURCE_IF_MATCH_TAG = 'ifMatchTag';
    const OBJECT_COPY_SOURCE_IF_NONE_MATCH_TAG = 'ifNoneMatchTag';
    const OBJECT_COPY_SOURCE_IF_UNMODIFIED_SINCE = 'ifUnmodifiedSince';
    const OBJECT_COPY_SOURCE_IF_MODIFIED_SINCE = 'ifModifiedSince';
    const OBJECT_COPY_METADATA_DIRECTIVE = 'metadataDirective';

    const BUCKET_LOCATION = 'bucketLocation';

    const LIST_MAX_UPLOAD_SIZE = 'listMaxUploadSize';

    const ACL = 'acl';
    const LOCATION = 'location';
    const REPLICATION = 'replication';
    const REPLICATION_PROGRESS = 'replicationProgress';

    const UPLOAD_ID = 'uploadId';
    const PART_NUM = 'partNum';
    const PART_LIST = 'partList';

    const CONTENT_LENGTH = 'contentLength';
    const CONTENT_TYPE = 'contentType';
    const CONTENT_MD5 = 'contentMd5';
    const CONTENT_SHA256 = 'contentSHA256';
    const USER_METADATA = 'userMetadata';
    const CONTENT_DISPOSITION = 'contentDisposition';
    const EXPIRES = 'Expires';
    const CACHE_CONTROL = 'cacheControl';

    const BCE_ACL = 'x-bce-acl';
    const BCE_ACL_GRANT_READ = 'x-bce-grant-read';
    const BCE_ACL_GRANT_FULL_CONTROL = 'x-bce-grant-full-control';

    const BCE_SERVER_SIDE_ENCRYPTION = 'x-bce-server-side-encryption';
    const BCE_KEEP_TIME = 'x-bce-keep-last-modified';

    const MAX_PARTS_COUNT = 'maxPartsCount';
    const PART_NUMBER_MARKER = 'partNumberMarker';

    const BCE_TAG = 'x-bce-tagging';
    const BCE_TAG_COUNT = 'x-bce-tagging-count';
    const BCE_TAG_DIRECTIVE = 'x-bce-tagging-directive';
    const MAX_KEYS = 'maxKeys';
    const PREFIX = 'prefix';
    const MARKER = 'marker';
    const VERSIONID_MARKER = 'versionIdMarker';
    const DELIMITER = 'delimiter';
    const LIMIT = 'limit';
    const STORAGE_CLASS = 'storageClass';
    const NEXT_APPEND_OFFSET = 'nextAppendOffset';
    const BCE_OBJECT_TYPE = 'objectType';
    const COPY_SOURCE_BUCKET = 'copySourceBucket';
    const COPY_SOURCE_OBJECT = 'copySourceObject';
    const CONTENT_CRC32 = 'crc32';

    const BCE_FETCH_MODE = 'x-bce-fetch-mode';
    const BCE_FETCH_SOURCE = 'x-bce-fetch-source';
    const FETCH = 'fetch';

    const LIFECYCLE = 'lifecycle';
    const LOGGING = 'logging';
    const TRASH = 'trash';
    const WEBSITE = 'website';
    const ENCRYPTION = 'encryption';
    const CORS = 'cors';
    const COPYRIGHTPROTECTION = 'copyrightProtection';

    const PART_SIZE = "partSize";
    const BCE_PROCESS = "x-bce-process";

    const SYMLINK = "symlink";
    const BCE_SYMLINK_TARGET = "x-bce-symlink-target";
    const BCE_FORBID_OVERWRITE = "x-bce-forbid-overwrite";
    const BCE_VERSION_ID = 'x-bce-version-id';

    const USER_QUOTA = 'userQuota';

    const NOTIFICATION = 'notification';

    const EVENT = 'event';
    const RESULT = 'result';

    const OBJECT_LOCK = 'objectlock';
    const EXTEND_OBJECT_LOCK = 'extendobjectlock';
    const COMPLETE_OBJECT_LOCK = 'completeobjectlock';
    const DELETE = 'delete';
    const RESTORE = 'restore';
    const VERSION_ID = 'versionId';
    const TAG = 'tagging';

    const IS_BUCKET_API = 'isBucketApi';
} 
