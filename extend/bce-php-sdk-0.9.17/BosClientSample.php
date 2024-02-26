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

include 'BaiduBce.phar';
require 'SampleConf.php';

use BaiduBce\BceClientConfigOptions;
use BaiduBce\Util\Time;
use BaiduBce\Util\MimeTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Services\Bos\BosClient;
use BaiduBce\Services\Bos\CannedAcl;
use BaiduBce\Services\Bos\BosOptions;
use BaiduBce\Auth\SignOptions;
use BaiduBce\Services\Bos\StorageClass;
use BaiduBce\Log\LogFactory;

class BosClientTest extends PHPUnit_Framework_TestCase
{
    private $client;
    private $bucket;

    private $custom_client;
    private $custom_bucket;


    private $key;
    private $filename;
    private $download;

    public function __construct()
    {
        global $BOS_TEST_CONFIG;
        global $CUSTOM_BOS_TEST_CONFIG;

        parent::__construct();
        $this->client = new BosClient($BOS_TEST_CONFIG);
        $this->custom_client = new BosClient($CUSTOM_BOS_TEST_CONFIG);
        $this->logger = LogFactory::getLogger(get_class($this));
    }

    public function setUp()
    {
        $id = rand();
        $this->bucket = sprintf('test-bucket%d', $id);
        $this->key = sprintf('test_object%d', $id);
        $this->filename = sprintf(__DIR__.'\\'.'temp_file%d.txt', $id);
        $this->download = __DIR__.'\\'.'download.txt';
        $this->client->createBucket($this->bucket);
    }

    public function tearDown()
    {
        // Delete all buckets
        $response = $this->client->listBuckets();

        foreach ($response->buckets as $bucket) {
            if (substr($bucket->name, 0, 11) == 'test-bucket') {
                $response = $this->client->listObjects($bucket->name);
                foreach ($response->contents as $object) {
                    $this->client->deleteObject($bucket->name, $object->key);
                }
                $this->client->deleteBucket($bucket->name);
            }
        }

        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
        if (file_exists($this->download)) {
            unlink($this->download);
        }
    }

    /**
     * Generate a random file of specified size
     * @param int $size The size of generated file.
     * @return null
     */
    private function prepareTemporaryFile($size)
    {
        $fp = fopen($this->filename, 'w');
        fseek($fp, $size - 1, SEEK_SET);
        fwrite($fp, '0');
        fclose($fp);
    }

    //test of bucket create/doesExist/list/delete operations
    public function testBucketOperations()
    {
        $id = rand();
        $bucketName = "test-bucket-operations".$id;
        //not created, should be false
        $exist = $this->client->doesBucketExist($bucketName);
        $this->assertFalse($exist);
        //create bucket
        $this->client->createBucket($bucketName);
        //created, should be true
        $exist = $this->client->doesBucketExist($bucketName);
        $this->assertTrue($exist);
        //should be in the bucket list
        $exist = false;
        $response = $this->client->listBuckets();
        foreach ($response->buckets as $bucket) {
            if ($bucket->name == $bucketName) {
                $exist = true;
            }
        }
        $this->assertTrue($exist);
        //delete
        $this->client->deleteBucket($bucketName);
        //deleted should be false
        $exist = $this->client->doesBucketExist($bucketName);
        $this->assertFalse($exist);
    }

    //test of acl set/set canned/get
    public function testAclOperations()
    {
        //there is no public-read-write
        $result = $this->client->getBucketAcl($this->bucket);
        $found = false;
        foreach($result->accessControlList as $acl) {
            if(strcmp($acl->grantee[0]->id, '*') == 0) {
                $this->assertEquals($acl->permission[0], 'READ');
                $this->assertEquals($acl->permission[1], 'WRITE');
                $found = true;
            }
        }
        $this->assertFalse($found);
        //there is public-read-write
        $this->client->setBucketCannedAcl($this->bucket, CannedAcl::ACL_PUBLIC_READ_WRITE);
        $result = $this->client->getBucketAcl($this->bucket);
        $found = false;
        foreach($result->accessControlList as $acl) {
            if(strcmp($acl->grantee[0]->id, '*') == 0) {
                $this->assertEquals($acl->permission[0], 'READ');
                $this->assertEquals($acl->permission[1], 'WRITE');
                $found = true;
            }
        }
        $this->assertTrue($found);
        //upload customized acl
        $found = false;
        $myAcl = array(
            array(
                'grantee' => array(
                    array(
                        'id' => 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
                    ),
                    array(
                        'id' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
                    ),
                ),
                'permission' => array('FULL_CONTROL'),
            ),
        );
        $this->client->setBucketAcl($this->bucket, $myAcl);
        $result = $this->client->getBucketAcl($this->bucket);
        foreach($result->accessControlList as $acl) {
            foreach($acl->grantee as $grantee) {
                if(strcmp($grantee->id, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa') == 0) {
                    $found = true;
                    $this->assertEquals($acl->permission[0], 'FULL_CONTROL');
                }
            }
        }
        $this->assertTrue($found);
    }

    //test of object acl set/set canned/get
    public function testObjectAclOperations()
    {
        //put string
        $this->client->putObjectFromString($this->bucket, $this->key, 'test');

        // set object acl private
        $canned_acl = array("x-bce-acl" => "private");
        $this->client->setObjectCannedAcl($this->bucket, $this->key, $canned_acl);

        //there is no public-read-write
        $result = $this->client->getObjectAcl($this->bucket, $this->key);
        $found = false;
        foreach ($result->accessControlList as $acl) {
            if (strcmp($acl->grantee[0]->id, '*') == 0) {
                $this->assertEquals($acl->permission[0], 'READ');
                $this->assertEquals($acl->permission[1], 'WRITE');
                $found = true;
            }
        }
        $this->assertFalse($found);
        //there is public-read
        $canned_acl = array("x-bce-acl" => "public-read");
        $this->client->setObjectCannedAcl($this->bucket, $this->key, $canned_acl);
        $result = $this->client->getObjectAcl($this->bucket, $this->key);
        $found = false;
        foreach ($result->accessControlList as $acl) {
            if (strcmp($acl->grantee[0]->id, '*') == 0) {
                $this->assertEquals($acl->permission[0], 'READ');
                $found = true;
            }
        }
        $this->assertTrue($found);

        //set object acl x-bce-grant-read
        $canned_acl = array("x-bce-grant-read" => "id=\"6c47a952\",id=\"8c47a95\"");
        $this->client->setObjectCannedAcl($this->bucket, $this->key, $canned_acl);
        $result = $this->client->getObjectAcl($this->bucket, $this->key);
        $found = 0;
        $acl = $result->accessControlList[0];
        if (strcmp($acl->grantee[0]->id, '6c47a952') == 0) {
            $this->assertEquals($acl->permission[0], 'READ');
            $found++;
        }
        if (strcmp($acl->grantee[1]->id, '8c47a95') == 0) {
            $this->assertEquals($acl->permission[0], 'READ');
            $found++;
        }
        $this->assertEquals($found, 2);
        //set object acl x-bce-grant-full-control
        $canned_acl = array("x-bce-grant-full-control" => "id=\"6c47a953\",id=\"8c47a96\"");
        $this->client->setObjectCannedAcl($this->bucket, $this->key, $canned_acl);
        $result = $this->client->getObjectAcl($this->bucket, $this->key);
        $found = 0;
        $acl = $result->accessControlList[0];
        if (strcmp($acl->grantee[0]->id, '6c47a953') == 0) {
            $this->assertEquals($acl->permission[0], 'FULL_CONTROL');
            $found++;
        }
        if (strcmp($acl->grantee[1]->id, '8c47a96') == 0) {
            $this->assertEquals($acl->permission[0], 'FULL_CONTROL');
            $found++;
        }
        $this->assertEquals($found, 2);
        //upload customized acl
        $found = false;
        $my_acl = array(
            array(
                'grantee' => array(
                    array(
                        'id' => '7f34788d02a64a9c98f85600567d98a7',
                    ),
                    array(
                        'id' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
                    ),
                ),
                'permission' => array('FULL_CONTROL'),
            ),
        );
        $this->client->setObjectAcl($this->bucket, $this->key, $my_acl);
        $result = $this->client->getObjectAcl($this->bucket, $this->key);
        foreach ($result->accessControlList as $acl) {
            foreach ($acl->grantee as $grantee) {
                if (strcmp($grantee->id, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa') ==
                    0
                ) {
                    $found = true;
                    $this->assertEquals($acl->permission[0], 'FULL_CONTROL');
                }
            }
        }
        $this->assertTrue($found);
    }

    //test of object operations basic:
    //List; listObjects
    //Delete: deleteObject
    //Copy: copyObject
    //Put: putObjectFromString/putObjectFromFile
    //Get: getObjectAsString/getObjectToFile
    public function testObjectBasicOperations()
    {
        $this->objectBasicOperations($this->client, $this->bucket);
    }

    /**
     * Operate object in bucket.
     * @param BosClient $client The bos client.
     * @param string $bucket The bucket name.
     * @return null
     */
    public function objectBasicOperations($client, $bucket)
    {
        //put string
        $client->putObjectFromString($bucket, $this->key, 'test');

        //put file
        file_put_contents($this->filename, "test of put object from string");
        $otherKey = $this->key."other";
        $client->putObjectFromFile($bucket, $otherKey, $this->filename);

        //list the objects and check
        $response = $client->listObjects($bucket);
        $keyArr = array(
            $this->key => false,
            $otherKey => false,
        );
        $this->assertEquals(2, count($response->contents));
        foreach ($response->contents as $object) {
            foreach(array_keys($keyArr) as $tempKey) {
                if(strcasecmp($object->key, $tempKey) == 0) {
                    unset($keyArr[$tempKey]);
                    break;
                }
            }
        }
        $this->assertEquals(0, count($keyArr));

        //copy object
        $response = $client->copyObject($bucket, $this->key, $bucket, "copy_of_test");

        //list the bucket and check
        $response = $client->listObjects($bucket);
        $keyArr = array(
            $this->key => false,
            $otherKey => false,
            "copy_of_test" => false,
        );
        $this->assertEquals(3, count($response->contents));
        foreach ($response->contents as $object) {
            foreach(array_keys($keyArr) as $tempKey) {
                if(strcasecmp($object->key, $tempKey) == 0) {
                    unset($keyArr[$tempKey]);
                    break;
                }
            }
        }
        $this->assertEquals(0, count($keyArr));

        //delete object
        $client->deleteObject($bucket, "copy_of_test");

        //list the bucket and check
        $response = $client->listObjects($bucket);
        $keyArr = array(
            $this->key => false,
            $otherKey => false,
            "copy_of_test" => false,
        );
        $this->assertEquals(2, count($response->contents));
        foreach ($response->contents as $object) {
            foreach(array_keys($keyArr) as $tempKey) {
                if(strcasecmp($object->key, $tempKey) == 0) {
                    unset($keyArr[$tempKey]);
                    break;
                }
            }
        }
        $this->assertEquals(1, count($keyArr));
        $this->assertTrue(array_key_exists("copy_of_test", $keyArr));

        //get object as string
        $result = $client->getObjectAsString($bucket, $otherKey);
        $this->assertStringEqualsFile($this->filename, $result);

        //get object to file
        $client->getObjectToFile($bucket, $this->key, $this->download);
        $this->assertStringEqualsFile($this->download, 'test');

        // append object
        file_put_contents($this->filename, "test of put append object");
        $appendKey = $this->key."append";
        $response = $client->appendObjectFromFile($bucket, $appendKey, $this->filename, 0);
        $nextOffsetTmp = $response->metadata[BosOptions::NEXT_APPEND_OFFSET];
        $appendStr = "appendStr";
        $response = $client->appendObjectFromString($bucket, $appendKey, $appendStr, intval($nextOffsetTmp));
        $nextOffset = $response->metadata[BosOptions::NEXT_APPEND_OFFSET];
        $this->assertEquals($nextOffset, strlen($appendStr) + $nextOffsetTmp);
    }

    //test of object operations advanced:
    //List; listObjects
    //Delete: deleteObject
    //Copy: copyObject
    //Put: putObjectFromString/putObjectFromFile
    //Get: getObjectAsString/getObjectToFile
    public function testObjectAdvancedOperations()
    {
        //put object from file with options
        file_put_contents($this->filename, "test of put object from string");
        $userMeta = array("private" => "private data");
        $options = array(
            BosOptions::CONTENT_TYPE=>"text/plain",
            BosOptions::CONTENT_MD5=>base64_encode(hash_file("md5", $this->filename, true)),
            BosOptions::CONTENT_LENGTH=>filesize($this->filename),
            BosOptions::CONTENT_SHA256=>hash_file("sha256", $this->filename),
            BosOptions::USER_METADATA => $userMeta,
        );
        $response = $this->client->putObjectFromFile($this->bucket, $this->key, $this->filename, $options);
        //stash etag which will be used in copy with options
        $sourceEtag = $response->metadata[BosOptions::ETAG];

        //get object with options:
        //get content from 12 to 17 in $this->key
        $options = array(
            BosOptions::RANGE=>array(12,17),
        );
        $slice = $this->client->getObjectAsString($this->bucket, $this->key, $options);
        $this->assertEquals("object", $slice);

        //put a dir and objects under this dir
        $this->client->putObjectFromString($this->bucket, "usr", '');
        for ($i = 0; $i < 10; $i++) {
            $this->client->putObjectFromString($this->bucket, "usr/".'object'.$i, "test".$i);
        }

        //list objects with options:
        //list 5 objects under dir usr start from usr/object4
        $options = array(
            BosOptions::MAX_KEYS=>5,
            BosOptions::PREFIX=>"usr/",
            BosOptions::MARKER=>"usr/object4",
            BosOptions::DELIMITER=>"/",
        );
        $response = $this->client->listObjects($this->bucket, $options);
        $this->assertEquals(5, count($response->contents));

        //copy object with options
        $options = array(
            BosOptions::USER_METADATA=>$userMeta,
            BosOptions::ETAG=>$sourceEtag,
        );
        $this->client->copyObject($this->bucket, $this->key, $this->bucket, "copy_of_test", $options);

        //get user meta from source
        $response = $this->client->getObjectMetadata($this->bucket, $this->key);
        $this->assertTrue(array_key_exists('private', $response['userMetadata']));
        $this->assertEquals('private data', $response['userMetadata']['private']);

        //get user meta from copy
        $response = $this->client->getObjectMetadata($this->bucket, "copy_of_test");
        $this->assertTrue(array_key_exists('private', $response['userMetadata']));
        $this->assertEquals('private data', $response['userMetadata']['private']);
    }

    //test of multi-part operations
    public function testMultiPartBaseOperations() {
        //initiate multi-upload
        $response = $this->client->initiateMultipartUpload($this->bucket, $this->key);
        $uploadId1 =$response->uploadId;
        $response = $this->client->initiateMultipartUpload($this->bucket, $this->key);
        $uploadId2 =$response->uploadId;

        //list multi-upload and check
        $upload_array = array(
            $uploadId1 => 0,
            $uploadId2 => 0,
        );
        $response = $this->client->listMultipartUploads($this->bucket);
        $this->assertEquals(2, count($response->uploads));
        foreach($response->uploads as $upload) {
            $this->assertEquals($upload->key, $this->key);
            $this->assertTrue(array_key_exists($upload->uploadId, $upload_array));
        }

        //about multi-upload
        $this->client->abortMultipartUpload($this->bucket, $this->key, $uploadId2);

        //list multi-upload and check
        $response = $this->client->listMultipartUploads($this->bucket);
        $this->assertEquals(1, count($response->uploads));
        $this->assertEquals($uploadId1, $response->uploads[0]->uploadId);
        $this->assertNotEquals($uploadId2, $response->uploads[0]->uploadId);

        //upload part from file
        $this->prepareTemporaryFile(6 * 1024 * 1024);
        $eTags = array();
        $partList = array();
        $response = $this->client->uploadPartFromFile($this->bucket,
            $this->key,
            $uploadId1,
            1,
            $this->filename,
            0,
            5*1024*1024);
        $eTags[$response->metadata[BosOptions::ETAG]] = true;
        array_push($partList, array("partNumber"=>1, "eTag"=>$response->metadata[BosOptions::ETAG]));
        $response = $this->client->uploadPartFromFile($this->bucket,
            $this->key,
            $uploadId1,
            2,
            $this->filename,
            5*1024*1024,
            1*1024*1024);
        $eTags[$response->metadata[BosOptions::ETAG]] = true;
        array_push($partList, array("partNumber"=>2, "eTag"=>$response->metadata[BosOptions::ETAG]));

        //list parts and compare
        $response = $this->client->listParts($this->bucket, $this->key, $uploadId1);
        $this->assertEquals(2, count($response->parts));
        foreach($response->parts as $part) {
            $this->assertTrue(array_key_exists($part->eTag, $eTags));
        }

        //complete multi-upload
        $response = $this->client->completeMultipartUpload($this->bucket, $this->key, $uploadId1, $partList);

        //download it and compare
        $this->client->getObjectToFile($this->bucket, $this->key, $this->download);
        $this->assertFileEquals($this->filename, $this->download);
    }

    public function testMultiPartCopyOperations() {
        //prepare file
        $fileSize = 21 * 1024 * 1024;
        $partSize = 5 * 1024 * 1024;
        $this->prepareTemporaryFile($fileSize);

        $this->client->putObjectFromFile($this->bucket, $this->key, $this->filename);

        //multi-upload
        $partNumber = 1;
        $length = $partSize;
        $bytesLeft = $fileSize;
        $offSet = 0;
        $partList = array();
        $response = $this->client->initiateMultipartUpload($this->bucket, $this->key."_multi_copy");
        $uploadId =$response->uploadId;
        while ($bytesLeft > 0) {
            $length = ($length > $bytesLeft) ? $bytesLeft : $length;
            $options = array(
                BosOptions::RANGE => array($offSet, $offSet + $length - 1)
            );
            $response = $this->client->uploadPartCopy($this->bucket,
                $this->key,
                $this->bucket,
                $this->key."_multi_copy",
                $uploadId,
                $partNumber,
                $options
            );
            array_push(
                $partList,
                array("partNumber"=>$partNumber, "eTag"=>$response->eTag)
            );
            $partNumber++;
            $bytesLeft -= $length;
            $offSet += $length;
        }

        //list parts with options
        $options = array(
            BosOptions::LIMIT=>5,
        );
        $response = $this->client->listParts($this->bucket, $this->key."_multi_copy", $uploadId, $options);
        $this->assertEquals(5, count($response->parts));

        //complete multi part upload
        $this->client->completeMultipartUpload($this->bucket, $this->key."_multi_copy", $uploadId, $partList);

        //compare content length with file size
        $contentLength =  $this->client->getObjectMetadata($this->bucket, $this->key."_multi_copy")["contentLength"];
        $this->assertEquals($contentLength, $fileSize);

        $this->client->deleteObject($this->bucket, $this->key."_multi_copy");

    }

    //test of multi-part operations
    public function testMultiPartAdvancedOperations() {
        //prepare file
        $fileSize = 101 * 1024 * 1024;
        $partSize = 5 * 1024 * 1024;
        $this->prepareTemporaryFile($fileSize);

        //multi-upload
        $userMeta = array("private" => "private data");
        $offset = 0;
        $partNumber = 1;
        $length = $partSize;
        $bytesLeft = $fileSize;
        $partList = array();
        $response = $this->client->initiateMultipartUpload($this->bucket, $this->key);
        $uploadId =$response->uploadId;
        while ($bytesLeft > 0) {
            $length = ($length > $bytesLeft) ? $bytesLeft : $length;
            $response = $this->client->uploadPartFromFile($this->bucket,
                $this->key,
                $uploadId,
                $partNumber,
                $this->filename,
                $offset,
                $length);
            array_push(
                $partList,
                array("partNumber"=>$partNumber, "eTag"=>$response->metadata[BosOptions::ETAG],)
            );
            $offset += $length;
            $partNumber++;
            $bytesLeft -= $length;
        }

        //list parts with options
        $options = array(
            BosOptions::LIMIT=>5,
            BosOptions::MARKER=>5,
        );
        $response = $this->client->listParts($this->bucket, $this->key, $uploadId, $options);
        $this->assertEquals(5, count($response->parts));

        //complete with user-metadata
        $options = array(BosOptions::USER_METADATA => $userMeta,);
        $this->client->completeMultipartUpload($this->bucket, $this->key, $uploadId, $partList, $options);

        //get user meta
        $response = $this->client->getObjectMetadata($this->bucket, $this->key);
        $this->assertTrue(array_key_exists('private', $response['userMetadata']));
        $this->assertEquals('private data', $response['userMetadata']['private']);

        //put a dir and init multi-upload for each object under dir
        $uploadIdList = array();
        $this->client->putObjectFromString($this->bucket, "usr", '');
        for ($i = 0; $i < 10; $i++) {
            $response = $this->client->initiateMultipartUpload($this->bucket, "usr/".'object'.$i);
            $uploadIdList["usr/".'object'.$i] = $response->uploadId;
        }

        //list objects with options:
        //list 5 objects under dir usr start from usr/object4
        $options = array(
            BosOptions::LIMIT=>5,
            BosOptions::PREFIX=>"usr/",
            BosOptions::MARKER=>"usr/object4",
            BosOptions::DELIMITER=>"/",
        );
        $response = $this->client->listMultipartUploads($this->bucket, $options);
        $this->assertEquals(5, count($response->uploads));

        //clear env
        foreach ($uploadIdList as $key => $uploadId) {
            $this->client->abortMultipartUpload($this->bucket, $key, $uploadId);
        }
    }

    public function testPutSuperObjectFromFile() {
        //prepare file
        $fileSize = 101 * 1024 * 1024;
        $partSize = 5 * 1024 * 1024;
        $this->prepareTemporaryFile($fileSize);

        $userMeta = array("private" => "private data");
        $options = array(BosOptions::USER_METADATA => $userMeta);

        $this->client->putSuperObjectFromFile($this->bucket, $this->key, $this->filename, $options);

        //get user meta
        $response = $this->client->getObjectMetadata($this->bucket, $this->key);
        $this->assertTrue(array_key_exists('private', $response['userMetadata']));
        $this->assertEquals('private data', $response['userMetadata']['private']);
    }


    //test of misc functions:generatePreSignedUrl
    public function testMiscOperations() {
    //put an object
    $this->client->putObjectFromString($this->bucket, $this->key, 'test string');

    //generatePreSignedUrl
    $url = $this->client->generatePreSignedUrl($this->bucket, $this->key);
    $file = file_get_contents($url);
    $this->assertEquals('test string', $file);

    //generatePreSignedUrl with timestamp and expiration
    $signOptions = array(
        SignOptions::TIMESTAMP=>new \DateTime(),
        SignOptions::EXPIRATION_IN_SECONDS=>300,
    );
    $url = $this->client->generatePreSignedUrl($this->bucket,
        $this->key,
        array(BosOptions::SIGN_OPTIONS => $signOptions)
    );
    $file = file_get_contents($url);
    $this->assertEquals('test string', $file);
    }

    // test of client config with custom endpoint
    public function testCustomObjectBasicOperations()
    {
        // If want to test custom endpoint, comment markTestSkipped and modify endpoint of $CUSTOM_BOS_TEST_CONFIG to custom endpoint
        $this->markTestSkipped(
            'Skip custom endpoint Case'
        );
        // modify it to your bucket associated with custom endpoint
        // for example, 'endpoint' => 'http://cus-bucket.bj.bcebos.com', custom_bucket = "cus-bucket"
        $this->custom_bucket = "your bucket name";
        $this->objectBasicOperations($this->custom_client, $this->custom_bucket);

        $custom_response = $this->custom_client->listObjects($this->custom_bucket);
        foreach ($custom_response->contents as $object) {
            $this->custom_client->deleteObject($this->custom_bucket, $object->key);
        }
    }

    // test of put/get/delete bucket replication and get bucket replication progress
    public function testBucketReplicationOperation()
    {
        $this->markTestSkipped(
              'Skip Replication Case'
            );
        $replication_rule = array(
            'status' => 'enabled',
            'replicateDeletes' => 'enabled',
            'id' => 'sample'
        );
        $replication_rule['resource'][0] = $this->bj_bucket . "/*";
        $replication_rule['destination']['bucket'] = $this->gz_bucket;
        $replication_rule['replicateHistory']['bucket'] = $this->gz_bucket;
        $this->bj_client->putBucketReplication($this->bj_bucket, $replication_rule);
        sleep(2);
        $this->bj_client->putObjectFromString($this->bj_bucket, "increment", "content");
        sleep(60);

        $response = $this->bj_client->getBucketReplicationProgress($this->bj_bucket);
        $this->assertEquals($response->historyReplicationPercent, 100);

        $response = $this->bj_client->getBucketReplication($this->bj_bucket);
        $this->assertEquals($response->status, "enabled");

        $response = $this->bj_client->listBucketReplication($this->bj_bucket);
        $response = $this->bj_client->deleteBucketReplication($this->bj_bucket);
    }

    //test of bucket put/get/delete lifecycle operations
    public function testBucketLifecycleOperations() {
        $lifecycle_rule = array(
            array(
                'id' => 'rule-id0',
                'status' => 'enabled',
                'resource' => array(
                    $this->bucket.'/prefix/*',
                ),
                'condition' => array(
                    'time' => array(
                        'dateGreaterThan' => '2016-09-07T00:00:00Z',
                    ),
                ),
                'action' => array(
                    'name' => 'DeleteObject',
                )         
            ),
            array(
                'id' => 'rule-id1',
                'status' => 'disabled',
                'resource' => array(
                    $this->bucket.'/prefix/*',
                ),
                'condition' => array(
                    'time' => array(
                        'dateGreaterThan' => '2016-09-07T00:00:00Z',
                    ),
                ),
                'action' => array(
                    'name' => 'Transition',
                    'storageClass' => 'COLD',
                ),      
            ), 
        );

        $this->client->putBucketLifecycle($this->bucket, $lifecycle_rule);

        $lifecycle_ret = $this->client->getBucketLifecycle($this->bucket);
        $this->assertEquals(sizeof($lifecycle_ret->rule), 2);
        $this->assertEquals($lifecycle_ret->rule[0]->status, 'enabled');
        $this->assertEquals($lifecycle_ret->rule[1]->action->name, 'Transition');
        $this->client->deleteBucketLifecycle($this->bucket);
    }

    //test of bucket put/get/delete logging operations
    public function testBucketLoggingOperations() {
        // prepare target bucket
        $this->client->createBucket($this->bucket.'logging');
        $logging = array(
                'targetBucket' => $this->bucket.'logging',
                'targetPrefix' => 'TargetPrefixName'
        );

        $this->client->putBucketLogging($this->bucket, $logging);

        $logging_ret = $this->client->getBucketLogging($this->bucket);
        $this->assertEquals($logging_ret->status, 'enabled');
        $this->assertEquals($logging_ret->targetPrefix, 'TargetPrefixName');
        $this->client->deleteBucketLogging($this->bucket);
        $this->client->deleteBucket($this->bucket.'logging');
    }

    //test of bucket put/get/delete trash operations
    public function testBucketTrashOperations() {
        $this->client->putBucketTrash($this->bucket, '.trashDirName');
        $trash_ret = $this->client->getBucketTrash($this->bucket);
        $this->assertEquals($trash_ret->trashDir, '.trashDirName');
        $this->client->deleteBucketTrash($this->bucket);
    }

    //test of bucket put/get/delete static website operations
    public function testBucketStaticWebsiteOperations() {
        $static_website = array(
                'index' => 'index.html',
                'notFound' => '404.html'
        );

        $this->client->putBucketStaticWebsite($this->bucket, $static_website);
        $static_website_ret = $this->client->getBucketStaticWebsite($this->bucket);
        $this->assertEquals($static_website_ret->index, 'index.html');
        $this->assertEquals($static_website_ret->notFound, '404.html');
        $this->client->deleteBucketStaticWebsite($this->bucket);
    }

    //test of bucket put/get/delete encryption operations
    public function testBucketEncryptionOperations() {
        $this->client->putBucketEncryption($this->bucket, 'AES256');
        $encryption_ret = $this->client->getBucketEncryption($this->bucket);
        $this->assertEquals($encryption_ret->encryptionAlgorithm, 'AES256');
        $this->client->deleteBucketEncryption($this->bucket);
    }

    //test of bucket put/get/delete cors operations
    public function testBucketCorsOperations() {
        $cors_rule = array(
            array(
                'allowedOrigins' => array(
                    'http://www.example.com',
                    'www.example2.com'
                ),
                'allowedMethods' => array(
                    'GET',
                    'HEAD'
                ),
                'allowedHeaders' => array(
                    'Authorization'
                ),
                'allowedExposeHeaders' => array(
                    'user-custom-expose-header'
                ),
                'maxAgeSeconds' => 3600        
            ),
            array(
                'allowedOrigins' => array(
                    'http://www.example3.com'
                ),
                'allowedMethods' => array(
                    'GET',
                    'PUT'
                ),
                'allowedHeaders' => array(
                    'x-bce-test'
                ),
                'allowedExposeHeaders' => array(
                    'user-custom-expose-header'
                ),
                'maxAgeSeconds' => 3600        
            )
        );

        $this->client->putBucketCors($this->bucket, $cors_rule);

        $cors_ret = $this->client->getBucketCors($this->bucket);
        $this->assertEquals(sizeof($cors_ret->corsConfiguration), 2);
        $this->assertEquals($cors_ret->corsConfiguration[0]->maxAgeSeconds, 3600);
        $this->assertEquals($cors_ret->corsConfiguration[1]->allowedOrigins[0], 'http://www.example3.com');
        $this->client->deleteBucketCors($this->bucket);
    }

    //test of bucket put/get/delete copyright protection operations
    public function testBucketCopyrightProtectionOperations() {
        $copyright_protection = array(
                $this->bucket.'/prefix/*',
                $this->bucket.'/*/suffix'
        );

        $this->client->putBucketCopyrightProtection($this->bucket, $copyright_protection);
        $copyright_protection_ret = $this->client->getBucketCopyrightProtection($this->bucket);
        $this->assertEquals($copyright_protection_ret->resource[0], $this->bucket.'/prefix/*');
        $this->assertEquals($copyright_protection_ret->resource[1], $this->bucket.'/*/suffix');
        $this->client->deleteBucketCopyrightProtection($this->bucket);
    }

    public function testUserQuotaOperations(){
        $maxBucketCount=53;
        $maxCapacityMegaBytes=110;
        $this->client->putUserQuota($maxBucketCount,$maxCapacityMegaBytes,100);
        $this->assertEquals($this->client->getUserQuota()->maxBucketCount,$maxBucketCount);
        $this->assertEquals($this->client->getUserQuota()->maxCapacityMegaBytes,$maxCapacityMegaBytes);
        $this->client->deleteUserQuota();
//        $res = $this->client->getUserQuota();
//        $this->assertEquals($res->getErrorCode(),'UserQuotaNotConfigured');
    }


    public function testBucketObjectLock(){
        $BUCKET_NAME="bucketobjectest";
        $retentDays=20;
        $extendDays=30;
        $this->client->deleteBucket($BUCKET_NAME);
        $this->client->createBucket($BUCKET_NAME);
        $this->client->initBucketObjectLock($BUCKET_NAME, $retentDays);
        $this->assertEquals($this->client->getBucketObjectLock($BUCKET_NAME)->lockStatus, "IN_PROGRESS");
        $this->client->deleteBucketObjectLock($BUCKET_NAME);
        $this->client->initBucketObjectLock($BUCKET_NAME, $retentDays);
        $this->client->completeBucketObjectLock($BUCKET_NAME);
        $this->client->extendBucketObjectLock($BUCKET_NAME, $extendDays);
        $this->assertEquals($this->client->getBucketObjectLock($BUCKET_NAME)->retentionDays, $extendDays);
    }

    public function testBucketAndObject()
    {
        $diping_key = "diping_key";
        $diping_value = "diping_value";
        $this->client->putObjectFromString($this->bucket, $diping_key, $diping_value);
        $this->assertEquals(count($this->client->listObjects($this->bucket)->contents),1);
        $this->client->putBucketStorageClass($this->bucket,'STANDARD');
        $res = $this->client->getBucketStorageClass($this->bucket);
        $this->assertEquals($res->storageClass, "STANDARD");
        $deleteattay = array(
            array(
                "key"=>$diping_key
            ),
        );
        $this->client->deleteMultipleObjects($this->bucket, $deleteattay);
        $this->assertEquals(count($this->client->listObjects($this->bucket)->contents),0);
    }

    public function testRestoreObject() {
        $archiveDay = 10;
        $archiveKey = 'archiveKey';
        $archiveValue = 'archiveValue';
        $options = array(
            BosOptions::STORAGE_CLASS => StorageClass::ARCHIVE,
        );
        $this->client->putObjectFromString($this->bucket, $archiveKey, $archiveValue, $options);
        try {
          $this->client->restoreObject($this->bucket, $archiveKey, $archiveDay, "Stander");
        }
        catch(Exception $e) {
          echo $e->getMessage();
        }
        $this->client->restoreObject($this->bucket, $archiveKey);
        try {
        $this->client->restoreObject($this->bucket, $archiveKey, $archiveDay, "Expedited");
        }
        catch(Exception $e) {
           echo $e->getMessage();
        }
        try {
        $this->client->restoreObject($this->bucket, $archiveKey, $archiveDay, "Standard");
        } 
        catch(Exception $e) {
          echo $e->getMessage();
        }
        $getObject = $this->client->getObjectMetadata($this->bucket, $archiveKey);
    }


    public function testNotification(){
        $mynotification =
            array(
            array(
                'id'=>        "water-test-1",
                'name'=>      "water-rule-1",
                'appId'=>     "water-app-id-1",
                'status'=>    "enabled",
                'resources'=> array("/*.jpg", "/*.png"),
                'events'=>   array("PutObject") ,
                'apps'=> array(
                    array(
                        'id' =>       "app-id-1",
                        'eventUrl' => "https://op-log-app-service.chehejia.com/op-log-app-service/v1-0/log/bos/event",
                        'xVars' =>    "xvars",
                    ),
                ),
            ));
        $this->client->putNotification($this->bucket,$mynotification);
        $getRes = $this->client->getNotification($this->bucket);
        $this->assertEquals($getRes->notifications[0]->appId,"water-app-id-1");
        $this->client->deleteNotification($this->bucket);

    }
}
