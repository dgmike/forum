<?php

class Test_Sys_Cache_FilesystemTest extends PHPUnit_Framework_TestCase
{
    public $basepath = null;
    public $cacheFS = null;

    public function setUp()
    {
        $basepath = sys_get_temp_dir() . '/phpunit_test_cache';
        $this->basepath = $basepath;
        if (is_dir($basepath)) {
            foreach(glob($basepath . '/*') as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($basepath);
        }
        mkdir($basepath);
        $this->cacheFS = new \Sys\Cache\Filesystem;
        $this->cacheFS->init(array($basepath));
    }

    public function testMustSetBasePath()
    {
        $this->setExpectedException('DomainException');
        $cacheFS = new \Sys\Cache\Filesystem;
        $cacheFS->init(array());
    }

    public function testMustSetBasePath2()
    {
        $cacheFS = new \Sys\Cache\Filesystem;
        $cacheFS->init(array('/tmp'));
        $this->assertEquals('/tmp', $cacheFS->basepath);
    }

    public function testCreateFile()
    {
        $filename = md5('key');
        $this->cacheFS->set('key', 'value');
        $this->assertTrue(is_file($this->basepath . '/' . $filename));
    }

    public function testCreateFileStructure()
    {
        $filename = md5('key');
        $this->cacheFS->set('key', 'value');
        $this->assertEquals(
            implode(
                PHP_EOL, array(
                    '0', // forever
                    's:5:"value";', // serialize
                )
            ),
            file_get_contents($this->basepath . '/' . $filename)
        );
    }

    public function testTimeMachine()
    {
        $filename = $this->basepath . '/' . md5('key');
        $time = (string) (time() + 30);
        $this->cacheFS->set('key', 'value', 30 /* seconds */);
        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        $this->assertEquals($time, $lines[0]);
    }

    public function testGetValue()
    {
        $filename = $this->basepath . '/' . md5('my_key');
        $this->cacheFS->set('my_key', array('name' => 'Michael'));
        $this->assertEquals(
            array('name' => 'Michael'),
            $this->cacheFS->get('my_key')
        );
    }

    public function testExpiresData()
    {
        $filename = $this->basepath . '/' . md5('key');
        $data = array('key' => 'value');
        $this->cacheFS->set('key', $data, 2);
        $this->assertEquals($data, $this->cacheFS->get('key'), '->the cache is here');
        sleep(3);
        $this->assertFalse($this->cacheFS->get('key'), '-> cache must be false if it pass the time');
        $this->assertFalse(is_file($filename), '-> get must delete the file');
    }

    public function testGetsFalseIfNotDefined()
    {
        $this->assertFalse($this->cacheFS->get('my_invalid_cache'), '-> if not defined, cache must return false');
    }

    public function testRemoveWillReturnTrue()
    {
        $this->assertTrue($this->cacheFS->delete('my_key'));
    }

    public function testFlushAll()
    {
        $this->cacheFS->set('key', 'value');
        $this->cacheFS->set('key2', 'value');
        $this->assertEquals(2, count(glob($this->basepath.'/*')), '-> must create 2 files');
        $this->cacheFS->flush();
        $this->assertEquals(0, count(glob($this->basepath.'/*')), '-> must delete all files');
    }
}
