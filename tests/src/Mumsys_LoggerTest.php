<?php

/**
 * Test class for Mumsys_Logger.
 */
class Mumsys_LoggerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Mumsys_Logger
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_testsDir = realpath(dirname(__FILE__) .'/../');

        $this->_logfile = $this->_testsDir . '/tmp/Mumsys_LoggerTest_defaultfile.test';
        $this->_opts = $opts = array(
            'logfile' => $this->_logfile,
            'way' => 'a',
            'logLevel' => 7,
            'msglogLevel' => 7,
            'msgEcho' => false,
            'msgReturn' => true,
            'maxfilesize' => 80,
            'lineFormat' => '%5$s',
        );
        $this->_object = new Mumsys_Logger($opts);
    }


    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->_object = null;
        unset($this->_object);
    }


    public function test__constructor1()
    {
        $_SERVER['PHP_AUTH_USER'] = 'flobee';
        $opts = $this->_opts;
        $opts['compress'] = 'gz';
        $opts['timeFormat'] = 'Y-m-d H:i:s';
        $opts['debug'] = true;
        $opts['verbose'] = false;
        $opts['lf'] = "\n";

        $object = new Mumsys_Logger($opts);
    }

    // for 100% code coverage
    public function test__constructor2()
    {
        $opts = $this->_opts;
        $opts['username'] = 'flobee';
        unset($opts['logfile'], $opts['way']);

        $object = new Mumsys_Logger($opts);

        unset($opts['username']);
        $_SERVER['REMOTE_USER'] = 'flobee';
        $object = new Mumsys_Logger($opts);

        unset($opts['username'], $_SERVER['REMOTE_USER'], $_SERVER['PHP_AUTH_USER'], $_SERVER['USER'], $_SERVER['LOGNAME']);
        $object = new Mumsys_Logger($opts);

        $_SERVER['LOGNAME'] = 'God';
        $object = new Mumsys_Logger($opts);
    }


    public function testGetLogfile()
    {
        $this->assertEquals($this->_opts['logfile'], $this->_object->getLogFile());
    }


    public function testLevelNameGet()
    {
        $this->assertEquals('EMERG', $this->_object->levelNameGet(0));
        $this->assertEquals('ALERT', $this->_object->levelNameGet(1));
        $this->assertEquals('CRIT', $this->_object->levelNameGet(2));
        $this->assertEquals('ERR', $this->_object->levelNameGet(3));
        $this->assertEquals('WARN', $this->_object->levelNameGet(4));
        $this->assertEquals('NOTICE', $this->_object->levelNameGet(5));
        $this->assertEquals('INFO', $this->_object->levelNameGet(6));
        $this->assertEquals('DEBUG', $this->_object->levelNameGet(7));

        $this->assertEquals('unknown', $this->_object->levelNameGet(999));
    }


    public function testEmerg()
    {
        $this->assertEquals('log emergency', trim($this->_object->emerg('log emergency')));
    }


    public function testAlert()
    {
        $this->assertEquals('log alert', trim($this->_object->alert('log alert')));
    }


    public function testCrit()
    {
        $this->assertEquals('log critical', trim($this->_object->crit('log critical')));
    }


    public function testErr()
    {
        $this->assertEquals('log error', trim($this->_object->err('log error')));
    }


    public function testWarn()
    {
        $this->assertEquals('log warning', trim($this->_object->warn('log warning')));
    }


    public function testNotice()
    {
        $this->assertEquals('log notice', trim($this->_object->notice('log notice')));
    }


    public function testInfo()
    {
        $this->assertEquals('log info', trim($this->_object->info('log info')));
    }


    public function testDebug()
    {
        $this->assertEquals('log debug', trim($this->_object->debug('log debug')));
    }


    public function testLog()
    {
        $this->assertEquals('long log', trim($this->_object->log('long log', 7)));

        $exp = 'ff_0: array("0" => "long log");';
        $this->assertEquals($exp, trim($this->_object->log(array('long log'), 7)));
    }

    public function testLogException()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Notice');
        $this->_object->log('invalid log level', 99);
    }


    public function testLogEchoMsg1()
    {
        $opts = $this->_opts;
        $opts['msgEcho'] = true;
        $opts['msgReturn'] = false;
        $object = new Mumsys_Logger($opts);
        ob_start();
        $object->log('test', 7);
        $x1 = ob_get_clean();

        $opts['msgLineFormat'] = 'x %5$s';
        $object = new Mumsys_Logger($opts);
        ob_start();
        $object->log('test', 7);
        $x2 = ob_get_clean();

        // as array input
        ob_start();
        $object->log(array('test1','test2'), 7);
        $x3 = ob_get_clean();
        $y3 = 'x ff_0: array("0" => "test1");' . $object->lf
            . 'x ff_0: array("1" => "test2");';

        $this->assertEquals('test', trim($x1));
        $this->assertEquals('x test', trim($x2));
        $this->assertEquals($y3, trim($x3));
    }


    public function testLogException1()
    {
        $opts = $this->_opts;
        $opts['lineFormat'] = '';
        $this->setExpectedException('Mumsys_Logger_Exception', 'Log format empty');

        $object = new Mumsys_Logger($opts);
    }


    public function testWrite()
    {
        $this->setExpectedException('Mumsys_File_Exception',
            'Can not write to file: "'.$this->_testsDir . '/tmp/Mumsys_LoggerTest_defaultfile.test". IsOpen: "Yes", Is writeable: "Yes".');
        $this->_object->write($this);
    }


}
