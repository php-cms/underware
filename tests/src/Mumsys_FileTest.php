<?php

/**
 * Test class for File.
 */
class Mumsys_FileTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Mumsys_File
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_version = '3.1.0';

        $this->_testsDir = realpath(dirname(__FILE__) .'/../');

        $this->_fileOk = $this->_testsDir . '/tmp/' . basename(__FILE__) . '.tmp';
        $this->_fileNotOk = $this->_testsDir . '/tmp/notExists/file.tmp';

        // auto open!
        $parts['way'] = 'w+'; // r+w + clr file
        $parts['file'] = $this->_fileOk;
        $parts['buffer'] = 10;
        $this->_object = new Mumsys_File($parts);
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unlink($this->_fileOk);
    }


    public function __destruct()
    {
//        exec('ls -al '.$this->_fileOk, $x);
//        print_r($x);
//        unlink($this->_fileOk);
        $this->_object = null;
    }


    // destructor retuns noting -> null
    public function test__destruct()
    {
        $this->_object->__destruct();
        $this->assertFalse($this->_object->isOpen());
    }


    public function testOpen()
    {
        $this->_object->setFile($this->_fileOk);
        $this->assertTrue($this->_object->open());
    }


    public function testOpenException()
    {
        $this->_object->setFile($this->_fileNotOk);

        $this->setExpectedException('Mumsys_File_Exception',
            'Can not open file "'. $this->_testsDir . '/tmp/notExists/file.tmp" '
            . 'with mode "w+". Directory is writeable: "No", readable: "No".');
        $this->_object->open();
    }


    public function testClose()
    {
        $this->assertTrue($this->_object->close());
    }


    public function testWrite1()
    {
        // test 1
        $x = false;
        $x = $this->_object->write('hello world');

        $this->assertTrue($x);
        $this->assertEquals('hello world', file_get_contents($this->_fileOk));
    }


    public function testWriteException1()
    {
        $this->_object->close();

        $this->setExpectedException('Mumsys_File_Exception',
            'File not open. Can not write to file: "'. $this->_testsDir
            . '/tmp/Mumsys_FileTest.php.tmp".');
        $x = $this->_object->write('hello world');
    }


    public function testWriteException2()
    {
        $this->_object->close();

        chmod($this->_fileOk, 0444);
//        exec('ls -al '.$this->_fileOk, $x);
//        print_r($x);
        $o = new Mumsys_File();
        $o->setFile($this->_fileOk);
        $o->setMode('r');
        $o->open();

        $this->assertTrue($o->isReadable());
        $this->assertFalse($o->isWriteable());

        $this->setExpectedException('Mumsys_File_Exception', 'File not writeable: '
            . '"'. $this->_testsDir . '/tmp/Mumsys_FileTest.php.tmp".');
        $x = $o->write('hello world');
    }


    // bad content
    public function testWriteException3()
    {
        $this->_object->close();

        $o = new Mumsys_File(array('file' => $this->_fileOk, 'way' => 'r'));
        $o->setFile($this->_fileOk);

        $this->setExpectedException('Mumsys_File_Exception',
            'Can not write to file: "'. $this->_testsDir . '/tmp/Mumsys_FileTest.php.tmp". '
            . 'IsOpen: "Yes", Is writeable: "Yes".');
        $x = $o->write($this);
    }


    public function testRead()
    {
        $this->_object->open();
        $this->_object->write('hello world');
        $this->_object->close();

        $o = new Mumsys_File(array('file' => $this->_fileOk, 'way' => 'r'));
        $o->setBuffer(5);
        $text1 = $o->read();
        $o->setBuffer(0);
        $text2 = $o->read();
        $o->close();

        $this->assertEquals('hello', $text1);
        $this->assertEquals(' world', $text2);
    }


    public function testReadException1()
    {
        $o = new Mumsys_File(array('way' => 'w'));
        $o->setFile($this->_fileNotOk);

        $this->setExpectedException('Mumsys_File_Exception',
            'File not open. Can not read from file: "'.$this->_testsDir . '/tmp/notExists/file.tmp".');
        $text1 = $o->read();
    }


    // not writable
    public function testReadException2()
    {
        $this->_object->close();

        chmod($this->_fileOk, 0222);
//        exec('ls -al '.$this->_fileOk, $x);
//        print_r($x);
        $o = new Mumsys_File();
        $o->setFile($this->_fileOk);
        $o->setMode('w');
        $o->open();

        $this->assertFalse($o->isReadable());
        $this->assertTrue($o->isWriteable());

        $this->setExpectedException('Mumsys_File_Exception',
            'File "'.$this->_testsDir . '/tmp/Mumsys_FileTest.php.tmp" not readable with mode "w". '
            . 'Is writeable "Yes", readable: "No".');
        $x = $o->read();
    }


    // empty file error?
    public function testReadException3()
    {
        $o = new Mumsys_File(array('file' => $this->_fileOk, 'way' => 'w'));

        $this->setExpectedException('Mumsys_File_Exception',
            'Error when reading the file: "'.$this->_testsDir . '/tmp/Mumsys_FileTest.php.tmp". IsOpen: "Yes".');
        $text1 = $o->read();
    }


    public function testSetBuffer()
    {
        $this->_object->write("hello world\nhello flobee");

        $o = new Mumsys_File(array('file' => $this->_fileOk, 'way' => 'r'));
        $o->setBuffer(17);
        $string = $o->read();
        $o->close();
        $this->assertEquals("hello world\nhello", $string);
    }


    public function testSetMode()
    {
        $this->_object->write("hello world\nhello flobee");

        $o = new Mumsys_File(array('file' => $this->_fileOk, 'way' => 'r'));
        $o->setBuffer(17);
        $string = $o->read();
        $o->close();
        $this->assertEquals("hello world\nhello", $string);
    }

    public function testSetModeException()
    {
        $this->_object->write("hello world\nhello flobee");

        $o = new Mumsys_File(array('file' => $this->_fileOk, 'way' => 'r'));
        $this->setExpectedException('Mumsys_File_Exception', 'Invalid mode');
        $o->setMode('this it wrong');
    }


    public function testGetFile()
    {
        $this->assertEquals($this->_fileOk, $this->_object->getFile());
    }


    public function testSetFile()
    {
        $this->_object->setFile($this->_fileNotOk);
        $this->assertEquals($this->_fileNotOk, $this->_object->getFile());
    }


    public function testIsWriteable()
    {
        // connection already opened in setup
        $actual = $this->_object->isWriteable();
        $this->assertTrue($actual);

        // no changes when closing
        $this->_object->close();
        $actual = $this->_object->isWriteable();
        $this->assertTrue($actual);

        $this->_object->setFile($this->_fileOk);
        $this->_object->setMode('r');
        $this->_object->open();
        $actual = $this->_object->isWriteable();
        $this->_object->close();
        $this->assertTrue($actual); // the owner always can write!
    }


    public function testIsReadable()
    {
        // file will be opend and created in setup() must exists
        $actual = file_exists($this->_fileOk);
        $this->assertTrue($actual);

        $actual = $this->_object->isReadable();
        $this->assertTrue($actual);

        // test with no auto opening
        $this->_object->setFile($this->_fileOk);
        $this->_object->setMode('w');
        $this->_object->open();
        $actual = $this->_object->isReadable();
        $this->_object->close();
        $this->assertTrue($actual); // the owner always can read!
        //
        // not opened and readable
        $o = new Mumsys_File();
        $o->setFile($this->_fileOk);
        $actual = $o->isReadable();
        $o->close();
        $this->assertTrue($actual); // the owner always can read!
    }


    // test abstracts


    /**
     * @covers Mumsys_File::getVersion
     */
    public function testGetVersion()
    {
        $this->assertEquals('Mumsys_File ' . $this->_version, $this->_object->getVersion());
    }

    /**
     * @covers Mumsys_File::getVersionID
     */
    public function testgetVersionID()
    {
        $this->assertEquals($this->_version, $this->_object->getVersionID());
    }

    /**
     * @covers Mumsys_File::getVersions
     */
    public function testgetVersions()
    {
        $expected = array(
            'Mumsys_Abstract' => '3.0.1',
            'Mumsys_File' => '3.1.0',
        );

        $possible = $this->_object->getVersions();

        foreach ($expected as $must => $value) {
            $this->assertTrue(isset($possible[$must]));
            $this->assertTrue(($possible[$must] == $value));
        }
    }

}
